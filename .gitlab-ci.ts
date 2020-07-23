import { basename, join } from "path";
import { realpathSync, existsSync } from "fs";
import { Config, CreateConfigFunction } from "node-gitlab-ci";

const createConfig: CreateConfigFunction = async () => {
    const config = new Config();
    const pkg = require("./package.json");

    config.stages("containerize", "install", "validate", "build", "test", "release", "build production", "deploy");

    config.defaults({
        image: "$CI_REGISTRY_IMAGE/gitlab-ci:$CI_COMMIT_REF_SLUG"
    });

    // Prefix created compose services so we can act on them (volumes, network, not container because they are named by container_name), should be same as package.json name
    // Also consider that all docker relevant prefixes should also contain the CI_COMMIT_REF_SLUG environment variable
    config.variable("COMPOSE_PROJECT_NAME", pkg.name);
    config.variable("COMPOSER_HOME", "$CI_PROJECT_DIR/.composer");

    // This is set to 1 by your project variables so jobs like E2E and review apps gets activated. Currently I do not know another way to detect that, feel free to contribute...
    // config.variable("DOCKER_DAEMON_ALLOW_UP", "1")
    // Additional settings
    config.variable("DOCKER_DRIVER", "overlay2");
    config.variable("JOB_PACKAGE_FOLDER", "plugins");
    config.variable("LERNA_SKIP_MESSAGE", "Lerna detected no change in this package, skip it...");
    config.variable("GL_TOKEN", "$GITLAB_TOKEN");

    // Allow git interactions (e. g. lerna version & publish)
    config.variable("GIT_AUTHOR_NAME", "$GITLAB_USER_NAME");
    config.variable("GIT_AUTHOR_EMAIL", "$GITLAB_USER_EMAIL");
    config.variable("GIT_COMMITTER_NAME", "$GITLAB_USER_NAME");
    config.variable("GIT_COMMITTER_EMAIL", "$GITLAB_USER_EMAIL");

    // List of install-dependent files - generally, we built a mechanism to hold a hot cache in our gitlab-ci image
    // If you add files to this list AND you are using shared-runners you need to add the glob to `gitlab-ci shared#only.changes`, too
    config.variable(
        "INSTALL_FILES",
        [
            "package.json",
            "yarn.lock",
            "common/patch-package/*",
            "packages/*/composer.*",
            "plugins/*/composer.*",
            "packages/*/package.json",
            "plugins/*/package.json"
        ].join(" ")
    );
    config.variable(
        "INSTALL_VENDOR_FOLDERS",
        ["{packages,plugins}/*/{vendor,node_modules,.yarn}", "node_modules", ".yarn", ".cypress"].join(" ")
    );

    // Install all needed node and composer modules as extendable job
    config.job(
        "install",
        {
            before_script: [
                // Copy temporary installation files from Dockerfile gitlab-ci...
                "export TMP_CI_PROJECT_DIR=/tmp$(realpath $CI_PROJECT_DIR)",
                "echo $TMP_CI_PROJECT_DIR",
                "export TMP_FILES=$(cd $TMP_CI_PROJECT_DIR && eval find $INSTALL_VENDOR_FOLDERS -maxdepth 0 2>/dev/null)",
                "echo $TMP_FILES",
                "time for dirs in $TMP_FILES; do ln -s $TMP_CI_PROJECT_DIR/$dirs $dirs; done",
                "git stash || :", // Allow to fail, e. g. in `docker review stop`
                // Make sure all dependencies are installed correctly
                "yarn bootstrap",
                // Make sure cypress is installed correctly
                "test $DOCKER_DAEMON_ALLOW_UP && yarn cypress install",
                "git stash pop || :",
                // Recreate our local package symlinks
                `for sym in $(find node_modules/@$COMPOSE_PROJECT_NAME/ {plugins,packages}/*/node_modules/@$COMPOSE_PROJECT_NAME {plugins,packages}/*/vendor/$COMPOSE_PROJECT_NAME -maxdepth 1 -type l 2>/dev/null); do ln -sf "$(realpath $sym | cut -c5-)" "$(dirname $sym)"; done`
            ]
        },
        true
    );

    // Run a job only in production branch
    config.job(
        "only production",
        {
            only: {
                refs: ["master"]
            }
        },
        true
    );

    const commonFiles = ["common/**/*", "devops/**/*", "*.{yml,ts,json,lock}"];
    config.job(
        "common files changed",
        {
            only: {
                changes: commonFiles
            }
        },
        true
    );
    config.job(
        "no common files changed",
        {
            except: {
                changes: commonFiles
            }
        },
        true
    );

    config.job(
        "lerna changes",
        {
            before_script: [
                "[ ! -f $JOB_PACKAGE_FOLDER/$JOB_PACKAGE_NAME/.publish ] && [ ! $LERNA_CHANGES_FORCE ] && echo $LERNA_SKIP_MESSAGE && exit 0"
            ]
        },
        true
    );

    config.job("install", {
        stage: "install",
        variables: {
            TEMP_CONTAINER_NAME: "$COMPOSE_PROJECT_NAME-install"
        },
        script: [
            "echo This container just makes sure the built image is correctly published with newest dependencies.",
            "test ! $DOCKER_DAEMON_ALLOW_UP && exit 0",
            // See `stage-containerize.yml` job for more information about this mechanism
            "export INSTALL_FILES=$(ls -t $INSTALL_FILES 2>/dev/null)",
            // Reset times for cache consistency
            "touch -a -m -t 201501010000.00 $INSTALL_FILES",
            "tar -cvf install.tar $INSTALL_FILES",
            // Create temporary container where we make sure installing current dependencies
            "export DOCKER_CONTAINER_FQN=$CI_REGISTRY_IMAGE/gitlab-ci:$CI_COMMIT_REF_SLUG",
            "(docker rm -f $TEMP_CONTAINER_NAME || :)",
            "docker run --name $TEMP_CONTAINER_NAME -it -d $DOCKER_CONTAINER_FQN",
            "docker cp install.tar $TEMP_CONTAINER_NAME:/tmp$(realpath $CI_PROJECT_DIR)",
            `docker exec -t $TEMP_CONTAINER_NAME /bin/bash -c "tar -xvf install.tar && yarn bootstrap && yarn cypress install"`,
            `docker commit --author "GitLab CI" --message "Reinstall dependencies" $TEMP_CONTAINER_NAME $DOCKER_CONTAINER_FQN`,
            // Push to registry
            "docker login -u $CI_REGISTRY_USER -p $CI_REGISTRY_PASSWORD $CI_REGISTRY",
            "docker history $DOCKER_CONTAINER_FQN",
            "docker push $DOCKER_CONTAINER_FQN"
        ],
        after_script: [
            // Purge
            "docker rm -f $TEMP_CONTAINER_NAME",
            // Purge <none> tagged images
            `export DANGLING_IMAGES=$((docker images -f "dangling=true" | grep "^$CI_REGISTRY_IMAGE\/gitlab-ci[ \t]*<none>" | awk '{ print $3 }') || :)`,
            `test "$DANGLING_IMAGES" && docker rmi $DANGLING_IMAGES`,
            // Purge branch tagged images
            `export DANGLING_IMAGES=$((docker images -f "dangling=true" | grep "^$CI_REGISTRY_IMAGE\/gitlab-ci[ \t]*$CI_COMMIT_REF_SLUG" | awk '{ print $3 }') || :)`,
            `test "$DANGLING_IMAGES" && docker rmi $DANGLING_IMAGES`
        ]
    });

    await config.include(__dirname, ["devops/.gitlab/*.ts"]);
    await config.include(__dirname, ["packages/*/devops/.gitlab/.gitlab-ci.ts"]);
    await config.include(__dirname, ["plugins/*/devops/.gitlab/.gitlab-ci.ts"]);

    return config;
};

/**
 * Create template jobs for a package.
 *
 * @param config
 * @param cwd Use __dirname, here we can extract dynamically get the job name
 * @return Package prefix which you can use in your files
 */
function createPackageJobs(config: Config, cwd: string, type: "packages" | "plugins") {
    const pkgFolder = realpathSync(join(cwd, "../.."));
    const prefix = basename(pkgFolder);
    let dependencies: string[] = [];
    const composerJsonPath = join(pkgFolder, "composer.json");
    const packageJsonPath = join(pkgFolder, "package.json");
    const rootPkgName = require(join(realpathSync(join(cwd, "../../../../")), "package.json")).name;

    // Collect all dependencies so we can listen to that changes, too
    if (existsSync(composerJsonPath)) {
        const composerJson = require(composerJsonPath);
        composerJson.require && dependencies.push(...Object.keys(composerJson.require));
        composerJson["require-dev"] && dependencies.push(...Object.keys(composerJson["require-dev"]));
    }
    if (existsSync(packageJsonPath)) {
        const packageJson = require(packageJsonPath);
        packageJson.dependencies && dependencies.push(...Object.keys(packageJson.dependencies));
        packageJson.devDependencies && dependencies.push(...Object.keys(packageJson.devDependencies));
    }

    // Filter only monorepo packages, map to correct package name and filter out duplicates
    dependencies = dependencies
        .filter((dep) => dep.startsWith(`${rootPkgName}/`) || dep.startsWith(`@${rootPkgName}/`))
        .map((dep) => dep.split("/")[1].replace(/^@/, ""))
        .filter((value, index, self) => self.indexOf(value) === index);

    // Common extendable job definition for each plugin specific job
    config.job(
        `${prefix} jobs`,
        {
            variables: {
                JOB_PACKAGE_FOLDER: type,
                JOB_PACKAGE_NAME: prefix
            }
        },
        true
    );

    const changes = [`${type}/${prefix}/**/*`].concat(dependencies.map((dep) => `packages/${dep}/**/*`));

    // Create only changes expression so a job is only called when there is a relevant change
    config.extends(
        ".common files changed",
        `${prefix} only changes`,
        {
            only: {
                changes
            }
        },
        true
    );

    // Create except changes expression so a job is only called when there is no relevant change
    config.extends(
        ".no common files changed",
        `${prefix} except changes`,
        {
            except: {
                changes
            }
        },
        true
    );

    return { prefix, dependencies };
}

export { createConfig, createPackageJobs };
