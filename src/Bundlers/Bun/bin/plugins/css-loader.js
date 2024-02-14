import { transform, browserslistToTargets } from "lightningcss-wasm";
import { readFile } from "fs/promises";

const defaultOptions = {
    targets: [],
    minify: true,
    sourcemaps: false
};

export default function (options = {}) {

    return {
        name: "css-loader",
        async setup(build) {

            build.onLoad({ filter: /\.css$|\.scss$/ }, async (args) => {

                const expression = await compile(args, { ...defaultOptions, ...options })

                return {
                    contents: expression,
                    loader: "js",
                }
            })
        }
    }
}

const compile = async function (args, opts) {

    const imports = [];
    const source = await readFile(args.path, "utf8");
    const targets = opts.targets?.length
        ? browserslistToTargets(opts.targets)
        : undefined;

    const { code } = transform({
        targets,
        filename: args.path,
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

    const css = JSON.stringify(code.toString())
    const imported = imports.map((url, i) => `import _css${i} from "${url}";`).join("\n");
    const exported = imports.map((_, i) => `_css${i}`).join(" + ");

    // No CSS imports. Return processed file
    if (!imports.length) {
        return `export default ${css}`
    }

    // Has both imports & CSS rules in processed file
    return `${imported}\nexport default ${exported} + ${css}`;
}
