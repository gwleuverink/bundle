// NOTE: we don't have to check if Bun is installed sinsce this script is invoked with the Bun runtime

import { exit } from "./utils/dump";
import { parseArgs } from "node:util";
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
        entry: "[dir]/[name].[ext]",
        chunk: "chunks/[name]-[hash].[ext]", // Not in use without --splitting
        asset: "assets/[name]-[hash].[ext]", // Not in use without --splitting
    },

    target: "browser",
    format: "esm",

    plugins: [
        cssLoader({
            minify: Boolean(options.minify),
            sourcemaps: Boolean(options.sourcemaps),
        }),
    ],
});

if (!result.success) {
    // console.error(result)
    // for (const message of result.logs) {
    //     console.error(message);
    // }
    // process.exit(1);

    // TODO: needs to be reworked
    let output = result.logs.map(log => log.message)
        .filter(val => val)
        .concat(options.entrypoint)

    exit('build-failed', '', output)
}
