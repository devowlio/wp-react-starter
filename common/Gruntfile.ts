/**
 * Common tasks for grunt.
 */

import { execSync } from "child_process";

/**
 * Create "pre:" and "post:" hooks for an array of tasks.
 *
 * @param grunt
 * @returns
 */
function hookable(grunt: IGrunt, tasks: string[]) {
    const hooks: string[] = [];
    tasks.forEach((task) => {
        grunt.task.exists(`pre:${task}`) && hooks.push(`pre:${task}`);
        hooks.push(task);
        grunt.task.exists(`post:${task}`) && hooks.push(`post:${task}`);
    });
    return hooks;
}

function applyDefaultRunnerConfiguration(grunt: IGrunt) {
    grunt.file.defaultEncoding = "utf8";

    const pkg = grunt.file.readJSON("package.json");

    /**
     * Tasks configuration.
     */
    grunt.config.merge({
        pkg
    });

    /**
     * Generate a beautiful disclaimer file: https://yarnpkg.com/lang/en/docs/cli/licenses/#toc-yarn-licenses-generate-disclaimer
     */
    grunt.registerTask("yarn:disclaimer", () => {
        const cwd = process.cwd();
        execSync("yarn --silent licenses generate-disclaimer --production > LICENSE_3RD_PARTY_JS.md", {
            cwd
        });
    });

    /**
     * `yarn licenses` outputs dependency licenses in `ndjson` format. Iterate all entries and check.
     */
    grunt.registerTask("yarn:license:check", () => {
        const cwd = process.cwd();
        const allowed = (grunt.config.get("pkg.license-check.spdx") as string[]).map((l) => l.toLowerCase());
        const ignorePackages = (grunt.config.get("pkg.license-check.packages") as string[]).map((l) => l.toLowerCase());
        console.log(`Allowed licenses: ${allowed.join(";")}`);

        const unlicensed = execSync("yarn --silent licenses list --production --json --no-progress", { cwd })
            .toString()
            .split("\n")
            .filter(Boolean)
            .map((o) => JSON.parse(o))
            .filter((o) => o.type === "table")[0]
            .data.body.filter(
                // eslint-disable-next-line @typescript-eslint/no-unused-vars
                ([name, version, license, url, authorUrl, author]: [
                    string,
                    string,
                    string,
                    string,
                    string,
                    string
                ]) => {
                    // Iterate all licenses and check validity.
                    if (
                        allowed.indexOf(license.toLowerCase()) === -1 &&
                        !name.startsWith("workspace-aggregator-") /* Skip workspace packages */ &&
                        ignorePackages.indexOf(`${name.toLowerCase()}@${version}`) === -1
                    ) {
                        return true;
                    }
                    return false;
                }
            );

        if (unlicensed.length) {
            grunt.log.errorlns(JSON.stringify(unlicensed, null, 4));
            throw new Error(
                "The above packages do not meet the allowed license definition.\nUse `yarn why` to detect usage or add them to the ignored packages in package.json#license-check.packages"
            );
        } else {
            grunt.log.oklns("All production licenses passed!");
        }
    });

    /**
     * Generate a beautiful disclaimer file: https://packagist.org/packages/comcast/php-legal-licenses
     */
    grunt.registerTask("composer:disclaimer", () => {
        const cwd = process.cwd();
        execSync("./vendor/bin/php-legal-licenses generate && mv licenses.md LICENSE_3RD_PARTY_PHP.md", { cwd });
    });

    /**
     * `composer check-licenses` outputs dependency licenses in `ndjson` format. Iterate all entries and check.
     */
    grunt.registerTask("composer:license:check", () => {
        const cwd = process.cwd();
        const settings = grunt.file.readJSON("./composer.json").extra["metasyntactical/composer-plugin-license-check"];
        const allowed = (settings.whitelist as string[]).map((l) => l.toLowerCase());
        const ignorePackages = (settings.packages as string[]).map((l) => l.toLowerCase());
        console.log(`Allowed licenses: ${allowed}`);

        const { dependencies } = JSON.parse(
            execSync("composer check-licenses --no-dev --format=json || true", { cwd }).toString()
        );
        const unlicensed: any = {};
        Object.keys(dependencies).forEach((name) => {
            const dep = dependencies[name];
            if (!dep.allowed_to_use && ignorePackages.indexOf(`${name}@${dep.version}`) === -1) {
                unlicensed[name] = dep;
            }
        });

        if (Object.keys(unlicensed).length) {
            grunt.log.errorlns(JSON.stringify(unlicensed, null, 4));
            throw new Error(
                "The above packages do not meet the allowed license definition.\nFind alternatives or add them to the ignored packages in composer.json#extra.metasyntactical/composer-plugin-license-check.packages"
            );
        } else {
            grunt.log.oklns("All production licenses passed!");
        }
    });
}

export { hookable, applyDefaultRunnerConfiguration };
