import { browserslistToTargets } from "lightningcss-wasm";
import { readFile, exists } from "fs/promises";
import path from "path";

/**
 * Use targets from config or when none given try
 * to detect browserslist from package.json
 */
export default async function (browserslist) {
    // If config was given, return browserlist immediately
    if (browserslist?.length) {
        return browserslistToTargets(browserslist);
    }

    // Otherwise read from package.json
    const pkg = await packageJson();
    browserslist = pkg.browserslist || [];

    if (browserslist?.length) {
        return browserslistToTargets(browserslist);
    }

    // If no package.json found or browserslist was not defined
    return undefined;
}

/**
 * Get contents of nearest package.json
 */
async function packageJson() {
    try {
        const path = await findNearestPackageJson();
        const content = await readFile(path, "utf8");
        return JSON.parse(content);
    } catch (error) {
        console.error(
            "Error reading browserslist from package.json:",
            error.message
        );
        return [];
    }
}

/**
 * Get path of nearest package.json
 */
async function findNearestPackageJson() {

    let currentDir = process.cwd();

    if(process.env['APP_ENV'] === 'testing') {
        currentDir += '/workbench';
    }

    while (true) {
        const packageJsonPath = path.join(currentDir, "package.json");

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
