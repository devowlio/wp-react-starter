import { ExtendConfigFunction, MacroArgs } from "node-gitlab-ci";

type YarnLicensesMacroArgs = MacroArgs & {
    prefix: string;
};

type ComposerLicensesMacroArgs = YarnLicensesMacroArgs;

const extendConfig: ExtendConfigFunction = async (config) => {
    // Semantic versioning, changelog and disclaimer generation
    config.extends(".install", "semver", {
        stage: "validate",
        script: [
            // Make lerna work in CI
            "./devops/scripts/lerna-ready-ci.sh",
            // Skip tags because they already should include the generated CHANGELOG.md
            '[ ! "$CI_BUILD_TAG" ] && yarn lerna version --yes --no-push --no-git-tag-version --allow-branch "$CI_COMMIT_REF_NAME" --loglevel silly'
        ],
        artifacts: {
            name: "semver",
            paths: [
                "package.json",
                "plugins/*/package.json",
                "plugins/*/CHANGELOG.md",
                "plugins/*/src/index.php",
                "plugins/*/LICENSE_3RD_PARTY*",
                "packages/*/package.json",
                "packages/*/CHANGELOG.md",
                "packages/*/LICENSE_3RD_PARTY*",
                "yarn.lock"
            ]
        }
    });

    // Validate licenses for yarn packages
    config.extends(
        ".install",
        "yarn licenses",
        {
            stage: "validate",
            script: ["cd $JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME", "yarn grunt yarn:license:check"]
        },
        true
    );

    // Validate licenses for composer packages
    config.extends(
        ".install",
        "composer licenses",
        {
            stage: "validate",
            script: ["cd $JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME", "yarn grunt composer:license:check"]
        },
        true
    );

    config.macro<YarnLicensesMacroArgs>("yarn licenses", (self, { prefix }) => {
        const definingJob = `.${prefix} jobs`;
        const packageName = self.getVariable(definingJob, "JOB_PACKAGE_NAME");
        const packageFolder = self.getVariable(definingJob, "JOB_PACKAGE_FOLDER");
        self.extends([definingJob, `.common files changed`, `.yarn licenses`], `${prefix} yarn licenses`, {
            only: {
                changes: [
                    `${packageFolder}/${packageName}/devops/.gitlab/**/*`,
                    `${packageFolder}/${packageName}/package.json`
                ]
            }
        });
    });

    config.macro<ComposerLicensesMacroArgs>("composer licenses", (self, { prefix }) => {
        const definingJob = `.${prefix} jobs`;
        const packageName = self.getVariable(definingJob, "JOB_PACKAGE_NAME");
        const packageFolder = self.getVariable(definingJob, "JOB_PACKAGE_FOLDER");
        config.extends(
            [`.${prefix} jobs`, `.common files changed`, `.composer licenses`],
            `${prefix} composer licenses`,
            {
                only: {
                    changes: [
                        `${packageFolder}/${packageName}/devops/.gitlab/**/*`,
                        `${packageFolder}/${packageName}/composer.*`
                    ]
                }
            }
        );
    });
};

export { extendConfig, YarnLicensesMacroArgs, ComposerLicensesMacroArgs };
