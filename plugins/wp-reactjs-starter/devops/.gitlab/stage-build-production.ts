import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";

const extendConfig: ExtendConfigFunction = async (config) => {
    const { prefix } = createPackageJobs(config, __dirname, "plugins");

    // Build production ready WP plugin
    config.extends([`.${prefix} jobs`, `.only production`, `.build plugin`], `${prefix} build production`, {
        stage: "build production",
        // Always rely on "release" so ".lerna changes" works correctly
        dependencies: [`${prefix} yarn licenses`, `${prefix} composer licenses`, "release", "semver"]
    });
};

export { extendConfig };
