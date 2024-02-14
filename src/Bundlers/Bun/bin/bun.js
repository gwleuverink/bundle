import { parseArgs } from "util";
import cssLoader from "./plugins/css-loader";

const options = parseArgs({
    args: Bun.argv,
    strict: true,
    allowPositionals: true,

    options: {
        entrypoint: {
            type: "string",
        },

        inputPath: {
            type: "string",
        },

        outputPath: {
            type: "string",
        },

        sourcemaps: {
            type: "boolean",
        },

        minify: {
            type: "boolean",
        },
    },
}).values;

const result = await Bun.build({
    entrypoints: [options.entrypoint],
    publicPath: options.outputPath,
    outdir: options.outputPath,
    root: options.inputPath,
    minify: options.minify,

    sourcemap: options.sourcemaps ? "external" : "none",

    naming: {
        entry: '[dir]/[name].[ext]',
        chunk: "chunks/[name]-[hash].[ext]", // Not in use without --splitting
        asset: "assets/[name]-[hash].[ext]", // Not in use without --splitting
    },

    target: "browser",
    format: "esm",

    plugins: [
        cssLoader({
            minify: Boolean(options.minify),
            sourcemaps: Boolean(options.sourcemaps)
        })
    ]
});

if (!result.success) {
    console.error("Build failed");
    for (const message of result.logs) {
        console.error(message);
    }
    process.exit(1); // Exit with an error code
}
