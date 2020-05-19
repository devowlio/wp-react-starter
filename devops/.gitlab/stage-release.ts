import { ExtendConfigFunction } from "node-gitlab-ci";

const extendConfig: ExtendConfigFunction = async (config) => {
    // Semantic release with automatic versioning/changelog depending on the commit messages
    config.extends([".install", ".only production"], "release", {
        stage: "release",
        script: [
            // Make lerna work in CI
            "./devops/scripts/lerna-ready-ci.sh",
            // Create a ".publish" file for all changed packages so next jobs can rely on it (.lerna changes)
            "rm -f {packages,plugins}/*/.publish",
            '(yarn --silent lerna changed --all -p --loglevel silent 2>/dev/null || true) | while read line; do touch "$line"/.publish; done',
            // Do the semantic versioning, changelog amd disclaimer generation (it also includes npm publishing for non-private packages)
            "git reset --hard",
            "yarn lerna $(test $NPM_TOKEN && echo 'publish' || echo 'version') --yes --loglevel silly || true"
        ],
        artifacts: {
            name: "technical publish files",
            paths: ["packages/*/.publish", "plugins/*/.publish"]
        }
    });

    // Use the GitLab "Review apps" feature so the commits can be viewed directly in the browser
    config.job("docker review start", {
        stage: "release",
        dependencies: ["collect all artifacts"],
        needs: ["collect all artifacts"],
        variables: {
            COMPOSE_PROJECT_NAME_SUFFIX: "-traefik"
        },
        script: [
            // Cleanup previous environments
            "./devops/scripts/purge-ci.sh",
            // Find mountable container of this job container (https://gitlab.com/gitlab-org/gitlab-foss/issues/41227#note_128388116) and mount it correctly
            'export JOB_CONTAINER_ID=$(docker ps -q -f "label=com.gitlab.gitlab-runner.job.id=$CI_JOB_ID" -f "label=com.gitlab.gitlab-runner.type=build")',
            'export JOB_MOUNT_PATH=$(docker inspect $JOB_CONTAINER_ID -f "{{ range .Mounts }}{{ if eq .Destination \\"/builds\\" }}{{ .Source }}{{end}}{{end}}")"/$CI_PROJECT_PATH"',
            // Start traefik containers and sleep until server is up and running
            'export WP_CI_INSTALL_URL="$CI_COMMIT_REF_SLUG-$COMPOSE_PROJECT_NAME-$CI_TRAEFIK_HOST"',
            'yarn docker-compose:traefik --project-directory "$JOB_MOUNT_PATH/devops/docker-compose" up --build -d',
            "export WP_WAIT_CONTAINER=$(yarn --silent docker-compose:traefik:name-wordpress)",
            // Collect all builds and copy them to the container, and wait for the plugin until it is ready
            "for slug in $(yarn --silent workspace:slugs); do for dirs in $(find plugins/$slug/build/$slug* -maxdepth 0 -type d 2>/dev/null); do docker cp $dirs $WP_WAIT_CONTAINER:/var/www/html/wp-content/plugins/; done; done;",
            "yarn wp-wait"
        ],
        tags: ["traefik"],
        only: {
            refs: ["branches"],
            // Skip this step on shared runners
            variables: ["$CI_TRAEFIK_HOST && $CI_TRAEFIK_BAUTH && $DOCKER_DAEMON_ALLOW_UP"]
        },
        except: {
            refs: ["master"]
        },
        environment: {
            name: "review/$CI_COMMIT_REF_NAME",
            url: "http://${CI_COMMIT_REF_SLUG}-${COMPOSE_PROJECT_NAME}-${CI_TRAEFIK_HOST}",
            on_stop: "docker review stop"
        }
    });

    config.extends(".install", "docker review stop", {
        extends: [".install"],
        stage: "release",
        variables: {
            COMPOSE_PROJECT_NAME_SUFFIX: "-traefik",
            // We do not need to fetch anything from coding
            GIT_STRATEGY: "none"
        },
        script: [
            // - ./devops/scripts/purge-ci.sh Can not be used because it is not fetched at this time
            // Remove running containers
            'echo "[CONTAINERS]"',
            'export CURRENT_CONTAINERS="$(docker ps -a --format "{{.ID}} {{.Names}}" | awk \'$2~/\'"$COMPOSE_PROJECT_NAME$COMPOSE_PROJECT_NAME_SUFFIX-$CI_COMMIT_REF_SLUG"\'/{print $1}\')"',
            'test "$CURRENT_CONTAINERS" && echo "Removing..." && docker rm -f -v $CURRENT_CONTAINERS',
            // Remove available volumes
            "echo",
            'echo "[VOLUMES]"',
            'export CURRENT_VOLUMES="$(docker volume ls --format "{{.Name}}" | awk \'$1~/\'"$COMPOSE_PROJECT_NAME$COMPOSE_PROJECT_NAME_SUFFIX-$CI_COMMIT_REF_SLUG"\'/{print $1}\')"',
            'test "$CURRENT_VOLUMES" && echo "Removing..." && docker volume remove -f $CURRENT_VOLUMES',
            // Remove available networks
            "echo",
            'echo "[NETWORKS]"',
            'export CURRENT_NETWORKS="$(docker network list --format "{{.Name}}" | awk \'$1~/\'"$COMPOSE_PROJECT_NAME$COMPOSE_PROJECT_NAME_SUFFIX-$CI_COMMIT_REF_SLUG"\'/{print $1}\')"',
            'test "$CURRENT_NETWORKS" && echo "Removing..." && docker network remove $CURRENT_NETWORKS',
            "echo",
            'echo "Purged"'
        ],
        when: "manual",
        tags: ["traefik"],
        only: {
            refs: ["branches"],
            // Skip this step on shared runners
            variables: ["$CI_TRAEFIK_HOST && $CI_TRAEFIK_BAUTH && $DOCKER_DAEMON_ALLOW_UP"]
        },
        except: {
            refs: ["master"]
        },
        environment: {
            name: "review/$CI_COMMIT_REF_NAME",
            action: "stop"
        }
    });

    // Publish a plugin to the wordpress.org repository
    config.job(
        "wordpress.org",
        {
            stage: "deploy",
            cache: {},
            script: [
                "cd plugins/$JOB_PACKAGE_NAME",
                'svn co $WPORG_SVN_URL wporg --username "$WPORG_SVN_USERNAME" --password "$WPORG_SVN_PASSWORD" --non-interactive --no-auth-cache',
                "rm -rf wporg/assets wporg/trunk",
                "mkdir wporg/assets wporg/trunk",
                "cp -r wordpress.org/assets/* wporg/assets",
                "cp -r build/$COPY_BUILD_FOLDER/* wporg/trunk",
                "cd wporg",
                "svn status",
                "svn add --force * --auto-props --parents --depth infinity -q",
                // Prune SVN repository (https://stackoverflow.com/a/61255370/5506547)
                '(svn status | grep "^!" | cut -c2- | xargs -i{} svn delete "{}") || :',
                'svn ci -m "This commit is generated through CI/CD, see the GIT repository for more details ($CI_COMMIT_SHA)" --username "$WPORG_SVN_USERNAME" --password "$WPORG_SVN_PASSWORD" --non-interactive --no-auth-cache'
            ],
            only: {
                refs: ["master"],
                variables: [
                    "$WPORG_SVN_URL && $WPORG_SVN_USERNAME && $WPORG_SVN_PASSWORD && $JOB_PACKAGE_NAME && $COPY_BUILD_FOLDER"
                ]
            }
        },
        true
    );
};

export { extendConfig };
