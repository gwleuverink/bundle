import dd from "./../utils/dd";
import determineTargets from "./../utils/browser-targets";
import { readFile } from "fs/promises";

const defaultOptions = {
    browserslist: [],
    minify: true,
    sourcemaps: false,
};

export default function (options = {}) {
    return {
        name: "css-loader",
        async setup(build) {

            // Compile plain css with Lightning CSS
            build.onLoad({ filter: /\.css$/ }, async (args) => {
                const source = await readFile(args.path, "utf8");

                const expression = await compile(source, args.path, {
                    ...defaultOptions,
                    ...options,
                });

                return {
                    contents: expression,
                    loader: "js",
                };
            });

            // Compile sass pass output through Lightning CSS
            build.onLoad({ filter: /\.scss$/ }, async (args) => {
                const sass = await import('sass').catch(error => {
                    console.error('bundle:sass-not-installed')
                    process.exit(1)
                })

                const source = sass.compile(args.path)

                const expression = await compile(source.css, args.path, {
                    ...defaultOptions,
                    ...options,
                });

                return {
                    contents: expression,
                    loader: "js",
                };
            });
        },
    };
}

const compile = async function (source, filename, opts) {

    const lightningcss = await import("lightningcss-wasm").catch(error => {
        console.error('bundle:lightningcss-not-installed')
        process.exit(1)
    })

    const imports = [];
    const targets = await determineTargets(opts.browserslist);

    const { code } = lightningcss.transform({
        targets,
        filename,
        code: Buffer.from(source),

        minify: opts.minify,
        // sourceMap: opts.sourcemaps,
        sourceMap: false, // Files not generated. must handle artifacts manually. disable for now

        visitor: {
            Rule: {
                import(rule) {
                    imports.push(rule.value.url);
                    return []; // Can't be removed
                },
            },
        },
    });

    const css = JSON.stringify(code.toString());

    // No CSS imports. Return processed file
    if (!imports.length) {
        return `export default ${css}`;
    }

    // Has both imports & CSS rules in processed file
    const imported = imports
        .map((url, i) => `import _css${i} from "${url}";`)
        .join("\n");

    const exported = imports.map((_, i) => `_css${i}`)
        .join(" + ");

    return `${imported}\nexport default ${exported} + ${css}`;
};
