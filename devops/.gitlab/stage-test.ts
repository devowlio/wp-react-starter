import { ExtendConfigFunction, MacroArgs } from "node-gitlab-ci";

type PhpUnitMacroArgs = MacroArgs & {
    prefix: string;
};

type JestMacroArgs = PhpUnitMacroArgs;

const extendConfig: ExtendConfigFunction = async (config) => {
    // Collect all artifacts and save as own artifacts so they can be used in review apps / next steps
    config.job("collect all artifacts", {
        stage: "test",
        script: ["echo 'Collect all artifacts for the next jobs (review apps, ...)'"],
        cache: {},
        artifacts: {
            name: "all build artifacts",
            paths: ["plugins/*/build/*", "plugins/*/docs/*"]
        }
    });

    config.job(
        "upload codecov",
        {
            after_script: ["test $CODECOV_TOKEN && bash <(curl -s https://codecov.io/bash)"]
        },
        true
    );

    // PHPUnit a package with coverage
    config.extends(
        [".install", ".upload codecov"],
        "phpunit",
        {
            stage: "test",
            script: [
                "docker-php-ext-enable xdebug",
                "cd $JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME",
                "yarn test:phpunit:coverage --colors=never"
            ],
            coverage: "/^\\s*Lines:\\s*\\d+.\\d+\\%/",
            artifacts: {
                name: "$CI_JOB_NAME",
                when: "always",
                paths: ["$JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME/coverage/phpunit"],
                reports: {
                    junit: "$JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME/test/junit/phpunit.xml"
                }
            }
        },
        true
    );

    // Jest a package with coverage
    config.extends(
        [".install", ".upload codecov"],
        "jest",
        {
            stage: "test",
            script: ["cd $JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME", "yarn test:jest:coverage --no-colors"],
            coverage: "/All files[^|]*\\|[^|]*\\s+([\\d\\.]+)/",
            artifacts: {
                name: "$CI_JOB_NAME",
                when: "always",
                paths: ["$JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME/coverage/jest"],
                reports: {
                    junit: "$JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME/test/junit/jest.xml"
                }
            }
        },
        true
    );

    // Start the cypress e2e test
    config.extends(
        ".install",
        "docker e2e cypress",
        {
            stage: "test",
            script: [
                // Cleanup previous environments
                "export COMPOSE_PROJECT_NAME_SUFFIX=-ci-$JOB_PACKAGE_NAME",
                "./devops/scripts/purge-ci.sh",
                // Find mountable container of this job container (https://gitlab.com/gitlab-org/gitlab-foss/issues/41227#note_128388116) and mount it correctly
                'export JOB_CONTAINER_ID=$(docker ps -q -f "label=com.gitlab.gitlab-runner.job.id=$CI_JOB_ID" -f "label=com.gitlab.gitlab-runner.type=build")',
                'export JOB_MOUNT_PATH=$(docker inspect $JOB_CONTAINER_ID -f "{{ range .Mounts }}{{ if eq .Destination \\"/builds\\" }}{{ .Source }}{{end}}{{end}}")"/$CI_PROJECT_PATH"',
                'yarn docker-compose:e2e --project-directory "$JOB_MOUNT_PATH/devops/docker-compose" up --build -d',
                "export WP_WAIT_CONTAINER=$(yarn --silent docker-compose:e2e:name-wordpress)",
                // Collect all builds and copy them to the container, and wait for the plugin until it is ready
                "for slug in $(yarn --silent workspace:slugs); do for dirs in $(find plugins/$slug/build/$slug* -maxdepth 0 -type d 2>/dev/null); do docker cp $dirs $WP_WAIT_CONTAINER:/var/www/html/wp-content/plugins/; done; done;",
                "yarn wp-wait",
                // Connect the gitlab container to our environment
                'docker network connect "$COMPOSE_PROJECT_NAME-ci-$JOB_PACKAGE_NAME-$CI_COMMIT_REF_SLUG""_locl" $JOB_CONTAINER_ID',
                "cd plugins/$JOB_PACKAGE_NAME",
                // Start cypress
                "CYPRESS_CACHE_FOLDER=../../.cypress/ yarn cypress run"
            ],
            after_script: ["export COMPOSE_PROJECT_NAME_SUFFIX=-ci-$JOB_PACKAGE_NAME", "./devops/scripts/purge-ci.sh"],
            artifacts: {
                name: "$CI_JOB_NAME",
                when: "always", // # Always create cypress results
                paths: [
                    "plugins/$JOB_PACKAGE_NAME/test/cypress/screenshots",
                    "plugins/$JOB_PACKAGE_NAME/test/cypress/videos"
                ],
                reports: {
                    junit: "plugins/$JOB_PACKAGE_NAME/test/junit/cypress.xml"
                }
            },
            only: {
                variables: ["$JOB_PACKAGE_NAME && $DOCKER_DAEMON_ALLOW_UP"]
            }
        },
        true
    );

    config.macro<PhpUnitMacroArgs>("phpunit", (self, { prefix }) => {
        const definingJob = `.${prefix} jobs`;
        const packageName = self.getVariable(definingJob, "JOB_PACKAGE_NAME");
        const packageFolder = self.getVariable(definingJob, "JOB_PACKAGE_FOLDER");
        config.extends([`.${prefix} jobs`, `.common files changed`, `.phpunit`], `${prefix} phpunit`, {
            only: {
                changes: [
                    `${packageFolder}/${packageName}/package.json`,
                    `${packageFolder}/${packageName}/devops/.gitlab/**/*`,
                    `${packageFolder}/${packageName}/**/*.php`
                ]
            }
        });
    });

    config.macro<JestMacroArgs>("jest", (self, { prefix }) => {
        const definingJob = `.${prefix} jobs`;
        const packageName = self.getVariable(definingJob, "JOB_PACKAGE_NAME");
        const packageFolder = self.getVariable(definingJob, "JOB_PACKAGE_FOLDER");
        config.extends([`.${prefix} jobs`, `.common files changed`, `.jest`], `${prefix} jest`, {
            only: {
                changes: [
                    `${packageFolder}/${packageName}/package.json`,
                    `${packageFolder}/${packageName}/devops/.gitlab/**/*`,
                    `${packageFolder}/${packageName}/**/*.{js,jsx,ts,tsx}`
                ]
            }
        });
    });
};

export { extendConfig, PhpUnitMacroArgs, JestMacroArgs };
