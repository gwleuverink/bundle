import { transform, browserslistToTargets } from "lightningcss-wasm";
import { readFile } from "fs/promises";

const defaultOptions = {
    targets: [],
    minify: true
};

export default function (options = {}) {

    return {
        name: "css-loader",
        async setup(build) {

            build.onLoad({ filter: /\.css$|\.scss$/ }, async (args) => {

                const css = await compile(args, { ...defaultOptions, ...options })

                return {
                    contents: `export default ${css};`,
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
        code: Buffer.from(source),
        filename: args.path,

        minify: opts.minify,
        sourceMap: opts.sourcemaps,
        targets,
        visitor: {
            Rule: {
                import(rule) {
                    imports.push(rule.value.url);
                    return [];
                },
            },
        },
    });

    const css = JSON.stringify(code.toString())

    if (!imports.length) {
        return css
    }

    const imported = imports
        .map((url, i) => `import _css${i} from "${url}";`)
        .join("\n");
    const exported = imports.map((_, i) => `_css${i}`).join(" + ");

    return `${imported}\nexport default ${exported} + ${css};`
}
