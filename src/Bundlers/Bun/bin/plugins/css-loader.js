import determineTargets from "./../utils/browser-targets";
import { exit, dd } from "./../utils/dump";
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
                const css = await compileCss(args.path, {
                    ...defaultOptions,
                    ...options,
                });

                return {
                    contents: `export default ${css}`,
                    loader: "js",
                };
            });

            // Compile sass pass output through Lightning CSS
            build.onLoad({ filter: /\.scss$/ }, async (args) => {
                const css = await compileSass(args.path, {
                    ...defaultOptions,
                    ...options,
                });

                return {
                    contents: `export default ${css}`,
                    loader: "js",
                };
            });
        },
    };
}

const compileCss = async function (filename, opts) {
    const lightningcss = await import("lightningcss-wasm").catch((error) => {
        exit("lightningcss-not-installed");
    });

    const targets = await determineTargets(opts.browserslist);
    const { code } = lightningcss.bundle({
        targets,
        filename,

        minify: opts.minify,
        // sourceMap: opts.sourcemaps,
        sourceMap: false, // Files not generated. must handle artifacts manually. disable for now
    });

    return JSON.stringify(code.toString());
};

const compileSass = async function (filename, opts) {
    const lightningcss = await import("lightningcss-wasm").catch((error) => {
        exit("lightningcss-not-installed");
    });

    const sass = await import("sass").catch((error) => {
        exit("sass-not-installed");
    });

    const source = sass.compile(filename);
    const targets = await determineTargets(opts.browserslist);
    const { code } = lightningcss.transform({
        targets,
        code: Buffer.from(source.css),

        minify: opts.minify,
        // sourceMap: opts.sourcemaps,
        sourceMap: false, // Files not generated. must handle artifacts manually. disable for now
    });

    return JSON.stringify(code.toString());
};
