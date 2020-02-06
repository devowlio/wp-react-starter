/**
 * This file is used in yarn debug:php:generate and generates the launch.json `pathMapping`
 * for xdebug. Usually you do not have to use this script because it is used through create-wp-react-app.
 */

import { resolve } from "path";
import { readdirSync, readFileSync, writeFileSync } from "fs";

// Read plugins
async function generate() {
    const pathMapping = {} as any;

    // Generate plugins' path mapping
    const plugins = readdirSync(resolve(process.env.PWD, "plugins"));
    plugins.forEach(
        (plugin) =>
            (pathMapping[`/var/www/html/wp-content/plugins/${plugin}`] = `\${workspaceFolder}/plugins/${plugin}/src`)
    );

    // Generate packages' path mappings
    const packages = readdirSync(resolve(process.env.PWD, "packages"));
    packages.forEach(
        (packagee) =>
            (pathMapping[
                `/var/www/html/wp-content/packages/${packagee}/src`
            ] = `\${workspaceFolder}/packages/${packagee}/src`)
    );

    // Regenerate launch.json
    const launchJsonPath = resolve(process.env.PWD, ".vscode/launch.json");
    let launchJson = readFileSync(launchJsonPath, { encoding: "UTF-8" });
    let stringedPathMapping = JSON.stringify(pathMapping).substr(1);
    stringedPathMapping = stringedPathMapping.substring(0, stringedPathMapping.length - 1);
    launchJson = launchJson.replace(
        /(\/\/ create-wp-react-app -->\n)(.*)(\/\/ <-- create-wp-react-app)/gms,
        `$1${stringedPathMapping}\n$3`
    );

    writeFileSync(launchJsonPath, launchJson, { encoding: "UTF-8" });
}

generate();
