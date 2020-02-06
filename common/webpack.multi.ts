/* eslint-disable @typescript-eslint/no-var-requires */
/* eslint-disable import/no-default-export */
/* eslint-disable import/no-extraneous-dependencies */
/**
 * Multi-package repository collector for webpack configurations.
 */

import glob from "glob";
import { resolve, dirname } from "path";

export default async () => {
    const result = [];
    const configs = glob
        .sync("{plugins,packages}/*/scripts/webpack.config.ts", {
            absolute: true
        })
        .map((path) => ({
            pwd: resolve(dirname(path), "../"),
            path
        }));

    for (const config of configs) {
        const { pwd, path } = config;
        process.env.DOCKER_START_PWD = pwd;
        result.push(((await import(path)) as any).default);
    }

    return result.flat();
};
