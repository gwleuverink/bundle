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
    const lightningcss = await import("lightningcss").catch((error) => {
        exit("lightningcss-not-installed");
    });

    const targets = await determineTargets(opts.browserslist);
    let { code, map } = lightningcss.bundle({
        targets,
        filename,

        errorRecovery: true,
        minify: opts.minify,
        sourceMap: opts.sourcemaps,
    });

    let css = code.toString();
    if (map) {
        map = rewriteSourcemapPaths(map);
        css = `${css}\n/*# sourceMappingURL=data:application/json;base64,${btoa(JSON.stringify(map))} */`
    }

    return JSON.stringify(css);
};

const compileSass = async function (filename, opts) {
    const lightningcss = await import("lightningcss").catch((error) => {
        exit("lightningcss-not-installed");
    });

    const sass = await import("sass").catch((error) => {
        exit("sass-not-installed");
    });

    const targets = await determineTargets(opts.browserslist);

    // NOTE: we could use a custom importer to remap sourcemap url's here. But might be able to reuse The one we use for the CSS loader
    const source = await sass.compileAsync(filename, {
        sourceMap: opts.sourcemaps,
        sourceMapIncludeSources: opts.sourcemaps // inlines source countent. refactor when adding extenral sourcemaps
    });

    let { code, map } = lightningcss.transform({
        targets,
        code: Buffer.from(source.css),
        filename: opts.sourcemaps
            ? filename
            : null,

        errorRecovery: true,
        minify: opts.minify,
        sourceMap: opts.sourcemaps,
        inputSourceMap: JSON.stringify(source.sourceMap),
    });

    let css = code.toString();

    if (map) {
        map = rewriteSourcemapPaths(map);
        css = `${css}\n/*# sourceMappingURL=data:application/json;base64,${btoa(JSON.stringify(map))} */`
    }

    return JSON.stringify(css);
};

const rewriteSourcemapPaths = function (map) {

    const replacePath = process.env["APP_ENV"] === "testing"
        ? process.cwd().replace(/^\/+|\/+$/g, "") + "/workbench"
        : process.cwd().replace(/^\/+|\/public+$/g, "");

    map = JSON.parse(map);

    map.sources = map.sources.map((path) => {
        return path.replace(replacePath, "..")
    });

    return map;
};
