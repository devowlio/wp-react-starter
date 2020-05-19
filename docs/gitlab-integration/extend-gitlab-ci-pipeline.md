# Extend GitLab CI pipeline

More advanced developers want to extend the existing [GitLab CI Pipeline](predefined-pipeline.md#pipeline-stages). We want to give you some notes where your jobs should be placed. Also, some "Template jobs" are predefined (jobs prefixed with dot `.`).

## Template jobs

Template jobs are jobs prefixed with a dot `.` in `yml` files. They can be used together with [`extends`](https://docs.gitlab.com/ee/ci/yaml/#extends) and some requires [variables](https://docs.gitlab.com/ee/ci/variables/) defined.

### Containerize

You can find all root template jobs in [`devops/.gitlab/stage-containerize.ts`](../usage/folder-structure/root.md#folder-structure):

`.containerize`: Build docker images from `devops/docker/` and push to [Gitlab Container Registry](https://docs.gitlab.com/ee/user/packages/container_registry/).

### Root

You can find all root template jobs in [`.gitlab-ci.ts`](../usage/folder-structure/root.md#folder-structure):

`.install`: Installs all needed node and composer dependencies. In conjunction with [`install`](predefined-pipeline.md#install) stage it just makes sure all is installed correctly. It does not reinstall instead it prints out something like `Already up-to-date`.

`.only production`: Only run job on `master` branch.

`.lerna changes`: Only run job if lerna detects changes ([`lerna changed`](https://github.com/lerna/lerna/tree/master/commands/changed)) for a given package. Required [variables](#available-variables): `$JOB_PACKAGE_FOLDER`, `$JOB_PACKAGE_NAME`. **Note**: This template job only works after [`release`](predefined-pipeline.md#release) did run.

### Validate

You can find all validate template jobs in [`devops/.gitlab/stage-validate.ts`](../usage/folder-structure/root.md#folder-structure):

`.yarn licenses`: Scan licenses for yarn dependencies. Required [variables](#available-variables): `$JOB_PACKAGE_FOLDER`, `$JOB_PACKAGE_NAME`.

`.composer licenses`: Scan licenses for composer dependencies. Required [variables](#available-variables): `$JOB_PACKAGE_FOLDER`, `$JOB_PACKAGE_NAME`.

{% page-ref page="../advanced/license-checker.md" %}

### Build

You can find all build template jobs in [`devops/.gitlab/stage-build.ts`](../usage/folder-structure/root.md#folder-structure):

`.docs`: Generate technical documents ([`yarn docs`](../usage/available-commands/plugin.md#documentation)) and store as artifact. Required [variables](#available-variables): `$JOB_PACKAGE_FOLDER`, `$JOB_PACKAGE_NAME`.

`.lint eslint`: Lint JavaScript/TypeScript source code ([`yarn lint:eslint`](../usage/available-commands/plugin.md#development)). Required [variables](#available-variables): `$JOB_PACKAGE_FOLDER`, `$JOB_PACKAGE_NAME`.

`.lint phpcs`: Lint PHP source code ([`yarn lint:phpcs`](../usage/available-commands/plugin.md#development)). Required [variables](#available-variables): `$JOB_PACKAGE_FOLDER`, `$JOB_PACKAGE_NAME`.

`.build plugin`: Type check, build the plugin ([`yarn build`](../usage/available-commands/plugin.md#build)) and store as artifact. Required [variable](#available-variables): `$JOB_PACKAGE_NAME`.

See also [this](predefined-pipeline.md#build).

{% page-ref page="../advanced/build-production-plugin.md" %}

### Test

You can find all test template jobs in [`devops/.gitlab/stage-test.ts`](../usage/folder-structure/root.md#folder-structure):

`.phpunit`: Start the [PHPUnit tests](../advanced/tests.md#phpunit) in a given package. Required [variables](#available-variables): `$JOB_PACKAGE_FOLDER`, `$JOB_PACKAGE_NAME`.

`.jest`: Start the [Jest tests](../advanced/tests.md#jest) in a given package. Required [variables](#available-variables): `$JOB_PACKAGE_FOLDER`, `$JOB_PACKAGE_NAME`.

`.upload codecov`: Start to upload [Coverage reports](../advanced/tests.md#coverage) to [codecov.io](https://codecov.io). Required [variables](#available-variables): `$CODECOV_TOKEN`.

`.docker e2e cypress`: Start the [E2E tests](../advanced/tests.md#e2e) in a complete new WordPress environment. Required [variables](#available-variables): `$JOB_PACKAGE_NAME`, `$DOCKER_DAEMON_ALLOW_UP`.

See also [this](predefined-pipeline.md#test).

{% page-ref page="../advanced/tests.md" %}

### Release

You can find all release template jobs in [`devops/.gitlab/stage-release.ts`](../usage/folder-structure/root.md#folder-structure):

`.wordpress.org`: Synchronize the built plugin with the SVN repository. Required [variables](#available-variables): `$WPORG_SVN_URL`, `$WPORG_SVN_USERNAME`, `$WPORG_SVN_PASSWORD`, `$JOB_PACKAGE_NAME`, `$COPY_BUILD_FOLDER`.

See also [this](predefined-pipeline.md#deploy).

{% page-ref page="./deploy-wp-org.md" %}

### Plugin

You can find all the plugin template jobs in [`plugins/*/devops/.gitlab/.gitlab-ci.ts`](../usage/folder-structure/plugin.md#folder-structure):

`.wprjss jobs`: Predefine [`$JOB_PACKAGE_NAME`](#available-variables). Extend from that template in all your jobs for the given plugin.

`.wprjss only changes`: Only run a job when changes of a specific plugin are pushed. **Note:** You have to add also paths of your monolithic dependencies (composer, yarn, other plugins).

{% hint style="info" %}
Plugin template jobs are abstractions to predefine variables needed in other templates like `.build plugin`. In your plugins they are named with the prefix you defined with [`create-wp-react-app create-plugin`](../usage/getting-started#create-workspace).
{% endhint %}

### Package

You can find all the package template jobs in [`packages/*/devops/.gitlab/.gitlab-ci.ts`](../usage/folder-structure/root.md#folder-structure):

`.utils jobs`: Predefine [`$JOB_PACKAGE_NAME`](#available-variables). Extend from that template in all your jobs for the given package.

`.utils only changes`: Only run a job when changes of a specific package are pushed. **Note:** You have to add also paths of your monolithic dependencies (composer, yarn, other plugins).

{% hint style="info" %}
Package template jobs are abstractions to predefine variables needed in other templates like `.yarn licenses`. In your plugins they are named with the prefix you defined with [`create-wp-react-app create-package`](../advanced/create-package.md).
{% endhint %}

{% page-ref page="../advanced/create-package.md" %}

## Available variables

Following [variables](https://docs.gitlab.com/ee/ci/variables/) can be passed to template jobs / pipeline:

| Variable                                                                                           | Description                                                                                                                                                                                                           | Example value                 | Configure in             |
| -------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------- | ------------------------ |
| `JOB_PACKAGE_FOLDER`                                                                               | Subfolder where packages are stored.                                                                                                                                                                                  | `plugins`, `packages`         | Job                      |
| `JOB_PACKAGE_NAME`                                                                                 | Package folder.                                                                                                                                                                                                       | `wp-reactjs-starter`, `utils` | Job                      |
| `DOCKER_DAEMON_ALLOW_UP`                                                                           | If true jobs like E2E tests and [review apps](./review-applications.md) work as expected.                                                                                                                             | `1`                           | Project Settings > CI/CD |
| `WPORG_SVN_URL`                                                                                    | SVN repository URL of your plugin.                                                                                                                                                                                    | -                             | Job                      |
| `WPORG_SVN_USERNAME`                                                                               | SVN username for authentication.                                                                                                                                                                                      | -                             | Project Settings > CI/CD |
| `WPORG_SVN_PASSWORD` ([mask](https://gitlab.com/help/ci/variables/README#masked-variables))        | SVN password for authentication.                                                                                                                                                                                      | -                             | Project Settings > CI/CD |
| `COPY_BUILD_FOLDER`                                                                                | Path joined with `plugins/*/build/` for SVN commit.                                                                                                                                                                   | `wp-reactjs-starter`          | Job                      |
| `NPM_TOKEN` ([mask](https://gitlab.com/help/ci/variables/README#masked-variables))                 | [`lerna publish`](https://github.com/lerna/lerna/tree/master/commands/publish) allows to publish directly to npmjs.com through [auth token](https://docs.npmjs.com/about-authentication-tokens)                       | -                             | Project Settings > CI/CD |
| `GITLAB_TOKEN` ([mask](https://gitlab.com/help/ci/variables/README#masked-variables))              | [`lerna version`](https://github.com/lerna/lerna/tree/master/commands/version) allows to commit directly to the repository through [auth token](https://docs.gitlab.com/ce/user/profile/personal_access_tokens.html). | -                             | Project Settings > CI/CD |
| `CI_TRAEFIK_HOST`                                                                                  | Traefik host for [Review applications](./review-applications.md)                                                                                                                                                      | `192-168-1-250`               | Project Settings > CI/CD |
| `CI_TRAEFIK_BAUTH`                                                                                 | Traefik basic auth for [Review applications](./review-applications.md)                                                                                                                                                | -                             | Project Settings > CI/CD |
| `CODECOV_TOKEN` ([mask](https://gitlab.com/help/ci/variables/README#masked-variables))             | [codecov.io](https://codecov.io) Code coverage upload [token](https://docs.codecov.io/docs/about-the-codecov-bash-uploader#section-upload-token)                                                                      | -                             | Project Settings > CI/CD |
| `PHP_COMPOSER_GITHUB_TOKEN` ([mask](https://gitlab.com/help/ci/variables/README#masked-variables)) | GitHub OAuth token to avoid rate limit. [Learn more](https://getcomposer.org/doc/articles/troubleshooting.md#api-rate-limit-and-oauth-tokens)                                                                         | -                             | Project Settings > CI/CD |
