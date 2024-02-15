import { transform, browserslistToTargets } from "lightningcss-wasm";
import { readFile, exists } from "fs/promises";
import path from "path";

const defaultOptions = {
    browserslist: [],
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
    const targets = await determineTargets(opts.browserslist);

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


//--------------------------------------------------------------------------
// Utilities
//--------------------------------------------------------------------------

/**
 * Use targets from config or when none given try
 * to detect browserslist from package.json
 */
const determineTargets = async function (browserslist) {

    if (browserslist?.length) {
        return browserslistToTargets(browserslist)
    }

    // read from package.json
    const pkg = await packageJson()
    browserslist = pkg.browserslist || []

    if (browserslist?.length) {
        return browserslistToTargets(browserslist)
    }

    return undefined
}

/**
 * Get contents of nearest package.json
 */
const packageJson = async function () {

    try {
        const path = await findNearestPackageJson();
        const content = await readFile(path, 'utf8');
        return JSON.parse(content);
    } catch (error) {
        console.error('Error reading browserslist from package.json:', error.message);
        return [];
    }

}

/**
 * Get path of nearest package.json
 */
const findNearestPackageJson = async function () {

    let currentDir = process.cwd();

    while (true) {

        const packageJsonPath = path.join(currentDir, 'package.json');

        if (await exists(packageJsonPath)) {
            return packageJsonPath;
        }

        // package.json file could not be found.
        if (currentDir === path.dirname(currentDir)) {
            return null;
        }

        currentDir = path.dirname(currentDir);
    }
}
