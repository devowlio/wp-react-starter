import { ExtendConfigFunction, MacroArgs } from "node-gitlab-ci";

type EsLintMacroArgs = MacroArgs & {
    prefix: string;
};

type PhpCsMacroArgs = EsLintMacroArgs;

type BuildPluginMacroArgs = PhpCsMacroArgs;

const extendConfig: ExtendConfigFunction = async (config) => {
    // Generate technical documents
    config.extends(
        ".install",
        "docs",
        {
            stage: "build",
            script: ["cd $JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME", "yarn docs"],
            artifacts: {
                name: "$CI_JOB_NAME",
                paths: ["$JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME/docs/"]
            }
        },
        true
    );

    // Lint JavaScript/TypeScript coding
    config.extends(
        ".install",
        "lint eslint",
        {
            stage: "build",
            script: ["cd $JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME", "yarn lint:eslint"]
        },
        true
    );

    // Lint PHP coding
    config.extends(
        ".install",
        "lint phpcs",
        {
            stage: "build",
            script: ["cd $JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME", "yarn lint:phpcs"]
        },
        true
    );

    // common/**/* JavaScript/TypeScript linting, others are handled in its own job
    config.extends(".install", "common lint eslint", {
        stage: "build",
        script: [`yarn eslint "common/**/*.{jsx,js,tsx,ts}"`],
        only: {
            changes: ["common/**/*.{jsx,js,tsx,ts}"]
        }
    });

    // common/**/* PHP linting, others are handled in its own job
    config.extends(".install", "common lint phpcs", {
        stage: "build",
        script: [
            // Try to delete templates because they should not be considered
            "rm -rf common/create-wp-react-app",
            "./packages/utils/vendor/bin/phpcs common/ --standard=./common/phpcs.xml"
        ],
        only: {
            changes: ["common/**/*.php"]
        }
    });

    // Create build files
    config.extends(
        ".install",
        "build plugin",
        {
            stage: "build",
            before_script: [
                // Check if build already exists so we can safely skip it
                "test -d plugins/$JOB_PACKAGE_NAME/build && exit 0 || :"
            ],
            script: [
                // If we are in production build check if it is necessary
                '[ $CI_JOB_STAGE == "build production" ] && [ ! -f plugins/$JOB_PACKAGE_NAME/.publish ] && echo $LERNA_SKIP_MESSAGE && exit 0',
                "cd plugins/$JOB_PACKAGE_NAME",
                // Set a revision prerelease version so that version can be sent out to customers for testings
                `export CURRENT_VERSION=$(node -e "console.log(require('./package.json').version)")`,
                '[ $CI_JOB_STAGE == "build" ] && yarn version --no-git-tag-version --new-version $CURRENT_VERSION"-"$(git rev-list --full-history --all --count)',
                // Check if typescript .d.ts files can be built for add-ons
                "yarn tsc -p .",
                // Build the plugin
                "yarn build",
                // Output artifact size of plugin zip
                "du -sh build/*"
            ],
            artifacts: {
                name: "$CI_JOB_NAME",
                paths: ["plugins/$JOB_PACKAGE_NAME/build/"]
            }
        },
        true
    );

    config.macro<BuildPluginMacroArgs>("build plugin", (self, { prefix }) => {
        const jobName = `${prefix} build`;
        const cache = {
            key: `${jobName}-$CI_COMMIT_REF_SLUG`,
            untracked: true,
            paths: ["plugins/$JOB_PACKAGE_NAME/build/"]
        };

        // The plugin is changed, build and only push to cache
        config.extends([`.${prefix} jobs`, `.${prefix} only changes`, `.build plugin`], jobName, {
            cache: {
                ...cache,
                policy: "push"
            }
        });

        // The plugin is not changed, pull from cache and rebuild if not found
        config.extends([`.${prefix} jobs`, `.${prefix} except changes`, `.build plugin`], `${jobName} from cache`, {
            cache: {
                ...cache,
                policy: "pull-push"
            }
        });
    });

    config.macro<EsLintMacroArgs>("lint eslint", (self, { prefix }) => {
        const definingJob = `.${prefix} jobs`;
        const packageName = self.getVariable(definingJob, "JOB_PACKAGE_NAME");
        const packageFolder = self.getVariable(definingJob, "JOB_PACKAGE_FOLDER");
        config.extends([`.${prefix} jobs`, `.common files changed`, `.lint eslint`], `${prefix} lint eslint`, {
            only: {
                changes: [
                    `${packageFolder}/${packageName}/package.json`,
                    `${packageFolder}/${packageName}/devops/.gitlab/**/*`,
                    `${packageFolder}/${packageName}/${
                        packageName === "packages"
                            ? "{lib,scripts,test}/**/*.{js,jsx,tsx,ts}"
                            : "{scripts,src/public/ts,test}/**/*.{jsx,js,tsx,ts}"
                    }`
                ]
            }
        });
    });

    config.macro<PhpCsMacroArgs>("lint phpcs", (self, { prefix }) => {
        const definingJob = `.${prefix} jobs`;
        const packageName = self.getVariable(definingJob, "JOB_PACKAGE_NAME");
        const packageFolder = self.getVariable(definingJob, "JOB_PACKAGE_FOLDER");
        config.extends([`.${prefix} jobs`, `.common files changed`, `.lint phpcs`], `${prefix} lint phpcs`, {
            only: {
                changes: [
                    `${packageFolder}/${packageName}/composer.*`,
                    `${packageFolder}/${packageName}/devops/.gitlab/**/*`,
                    `${packageFolder}/${packageName}/src/**/*.php`
                ]
            }
        });
    });
};

export { extendConfig, EsLintMacroArgs, PhpCsMacroArgs, BuildPluginMacroArgs };
