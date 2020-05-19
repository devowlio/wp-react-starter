import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";

const extendConfig: ExtendConfigFunction = async (config) => {
    const { prefix } = createPackageJobs(config, __dirname, "plugins");

    // Do your deployments here, for example upload builds to your license server and publish docs
    config.extends([`.${prefix} jobs`, `.only production`, `.lerna changes`], `${prefix} docs deploy`, {
        stage: "deploy",
        cache: {},
        // Always rely on "release" so ".lerna changes" works correctly
        dependencies: [`${prefix} docs`, "release"],
        script: ["ls -la plugins/$JOB_PACKAGE_NAME/docs/"]
    });

    config.extends([`.${prefix} jobs`, `.only production`, `.lerna changes`], `${prefix} build deploy`, {
        stage: "deploy",
        cache: {},
        // Always rely on "release" so ".lerna changes" works correctly
        dependencies: [`${prefix} build production`, "release"],
        script: ["ls -la plugins/$JOB_PACKAGE_NAME/build/"]
    });

    // Publish the plugin changes to wordpress.org/plugins (not depending on lerna changes because it should simply reflect the complete SVN)
    config.extends(
        [`.${prefix} jobs`, `.${prefix} only changes`, `.lerna changes`, ".only production", ".wordpress.org"],
        `${prefix} wordpress.org`,
        {
            variables: {
                COPY_BUILD_FOLDER: prefix
            },
            // Here we need to rely on "semver" so the updated CHANGELOG.md and package.json is applied
            dependencies: [`${prefix} build production`, "release"]
        }
    );
};

export { extendConfig };
