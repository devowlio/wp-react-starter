import { ExtendConfigFunction } from "node-gitlab-ci";

const extendConfig: ExtendConfigFunction = async (config) => {
    // Containerize a Dockerfile to the GitLab CI container registry.
    // The job name represents the folder in devops/docker/$IMAGE_NAME/*
    // "only.changes" can not be extendable: https://gitlab.com/gitlab-org/gitlab/issues/8177
    config.job(
        "containerize",
        {
            stage: "containerize",
            image: {
                name: "gcr.io/kaniko-project/executor:debug-v0.22.0",
                entrypoint: [""]
            },
            cache: {},
            script: [
                // Always create a install.tar archive which contains all files relevant for installation
                "export INSTALL_FILES=$(ls -t $INSTALL_FILES 2>/dev/null)",
                // Reset times for cache consistency
                "touch -a -m -t 201501010000.00 $INSTALL_FILES",
                "tar -cvf install.tar $INSTALL_FILES",
                // Create image name
                "export DOCKER_CONTAINER_FQN=$CI_REGISTRY_IMAGE/$IMAGE_NAME:$CI_COMMIT_REF_SLUG",
                "echo Build as $DOCKER_CONTAINER_FQN",
                `echo "{\\"auths\\":{\\"$CI_REGISTRY\\":{\\"username\\":\\"$CI_REGISTRY_USER\\",\\"password\\":\\"$CI_REGISTRY_PASSWORD\\"}}}" > /kaniko/.docker/config.json`,
                "cat /kaniko/.docker/config.json",
                `/kaniko/executor --cache=true --build-arg GL_CI_WORKDIR=$(realpath $CI_PROJECT_DIR) --context $CI_PROJECT_DIR --dockerfile $CI_PROJECT_DIR/devops/docker/$IMAGE_NAME/Dockerfile --destination $DOCKER_CONTAINER_FQN`
            ]
        },
        true
    );

    // Build an hot-cache image in a shared-runner environment
    config.extends(".containerize", "gitlab-ci shared", {
        variables: {
            IMAGE_NAME: "gitlab-ci"
        },
        before_script: [
            `echo ########################`,
            `echo Building and publishing docker images via GitLab shared runners is very slow. Please checkout this and setup your own runner`,
            `echo https://devowlio.gitbook.io/wp-react-starter/gitlab-integration/predefined-pipeline#disadvantages`,
            `echo ########################`
        ],
        except: {
            variables: ["$DOCKER_DAEMON_ALLOW_UP"]
        },
        only: {
            changes: [
                "package.json",
                "yarn.lock",
                "common/patch-package/*",
                '"{packages,plugins}/*/{composer,package}.*"',
                "devops/.gitlab/stage-containerize.ts",
                "devops/docker/gitlab-ci/*"
            ]
        }
    });

    // Build an hot-cache image in an own-runner environment
    // What's happening here? We are building the image only when changes
    // to non-dependency files are done. The dependencies are created and commit
    // through the `install` job.
    config.extends(".containerize", "gitlab-ci hosted", {
        variables: {
            IMAGE_NAME: "gitlab-ci"
        },
        only: {
            changes: ["devops/.gitlab/stage-containerize.ts", "devops/docker/gitlab-ci/*"],
            variables: ["$DOCKER_DAEMON_ALLOW_UP"]
        }
    });
};

export { extendConfig };
