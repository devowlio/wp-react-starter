/* eslint-disable @typescript-eslint/no-var-requires */
/* eslint-disable import/no-default-export */
/* eslint-disable import/no-extraneous-dependencies */
/**
 * Multi-package repository collector for webpack configurations.
 */

import glob from "glob";
import { resolve, dirname, join } from "path";

const cwd = process.cwd();
const rootCwd = resolve(join(__dirname, ".."));
const rootName = require(join(rootCwd, "package.json")).name;

// Check if a single plugin should be built so we consider dependencies, too
const buildPlugin = process.env.BUILD_PLUGIN;
const buildPluginPwds = buildPlugin
    ? Object.keys(require(join(cwd, "package.json")).dependencies)
          .filter((dep) => dep.startsWith(`@${rootName}/`))
          .map((dep) => join(rootCwd, "packages", dep.split("/")[1]))
          .concat([cwd])
    : undefined;

if (buildPlugin) {
    console.log(
        "You are currently building a plugin, please consider to put your webpack:done actions after the `yarn build` command for performance reasons!"
    );
}

export default async () => {
    const result = [];
    const configs = glob
        .sync("{plugins,packages}/*/scripts/webpack.config.ts", {
            absolute: true,
            cwd: rootCwd
        })
        .map((path) => ({
            pwd: resolve(dirname(path), "../"),
            path
        }))
        .filter(({ pwd }) => {
            // When we need to build a plugin, only consider dependent packages and own plugin
            if (buildPlugin) {
                return buildPluginPwds.indexOf(pwd) > -1;
            }
            return true;
        });

    for (const config of configs) {
        const { pwd, path } = config;
        process.env.DOCKER_START_PWD = pwd;
        process.env.NODE_ENV = buildPlugin ? "production" : "development";
        result.push(require(path).default);
    }

    return result.flat();
};
