# Predefined pipeline

If you have a look at [`.gitlab-ci.ts`](../usage/folder-structure/root.md#folder-structure) you notice a high-order pipeline configuration with a specific amount of other `ts` includes. Due to the fact that the boilerplate is built modular, the [GitLab CI configuration](https://docs.gitlab.com/ee/ci/yaml/) should be, too.

{% hint style="warning" %}
A CI/CD configuration itself (not matter what you are using) can be very huge. You will not understand the configuration while read through first. Do not worry, you can just start developing plugins and do not need to adjust the CI/CD configuration at the beginning.
{% endhint %}

## Why GitLab?

-   It provides one of the **best documentation** - it's very clear and extensive.
-   The documentation is **very easy to understand** - even for beginners.
-   It has a very good market position.
-   CI/CD system is directly integrated in your Git repository and directly triggered for merge requests.
-   It's **free** (unlimited if you use your [own runner](./use-own-runner.md))!

{% page-ref page="./use-own-runner.md" %}

## Disadvantages

If you choose to use the [**Shared Runners**](https://docs.gitlab.com/ee/ci/runners/#shared-specific-and-group-runners) coming out-of-the-box with GitLab CI/CD you will notice, some of our defined pipeline jobs will not be triggered due to the following fact: Shared Runners does not allow you to run additional docker containers.

[**E2E tests**](../advanced/tests.md#e2e) requires to use own started docker containers so the test itself can run on a clean installation.

[**Review applications**](./review-applications.md) requires to start persistent docker containers running a specific time (until the branch is merged) so you can directly view changes in a clean installation of your plugins.

{% page-ref page="./use-own-runner.md" %}

## Pipeline stages

The predefined pipeline coming with **WP ReactJS Starter** is defined in multiple `yml` files and included in the root [`.gitlab-ci.ts`](../usage/folder-structure/root.md#folder-structure) file. If you create a new plugin with [`create-wp-react-app create-plugin`](../usage/getting-started.md#create-workspace) you do not need to modify [`.gitlab-ci.ts`](../usage/folder-structure/root.md#folder-structure) yourself because this is done automatically by the command (it adds a new `include`).

### Containerize

Job definitions in: [`devops/.gitlab/stage-containerize.ts`](../usage/folder-structure/root.md#folder-structure)

Build docker images from `devops/docker/` and push to [Gitlab Container Registry](https://docs.gitlab.com/ee/user/packages/container_registry/). Currently there is one docker images built: `gitlab-ci` for the GitLab CI itself (each job gets its own container with that image).

### Install

Job definitions in: [`devops/.gitlab/stage-install.ts`](../usage/folder-structure/root.md#folder-structure)

The first step of our pipeline starts [installing / bootstrapping](../usage/available-commands/root.md#development) the complete plugin environment. This job must be defined only once because it is responsible for all your packages - due to the fact we are using a multi-package repository with `lerna`. The complete installation files (dependencies of composer and yarn) are put into a cache so further jobs can fetch that cache and do not need to reinstall again.

Per plugin an additional job for [license checker](../advanced/license-checker.md#javascript) is added. As we use `lerna` we need a way to bootstrap a single package.

{% hint style="info" %}
You need to add `.install` to all your jobs' `extends` which require all dependencies installed. Learn more [here](extend-gitlab-ci-pipeline.md#root).
{% endhint %}

### Validate

Job definitions in:

-   [`devops/.gitlab/stage-validate.ts`](../usage/folder-structure/root.md#folder-structure)
-   [`plugins/*/devops/.gitlab/stage-validate.ts`](../usage/folder-structure/plugin.md#folder-structure)

Bump versions and generate a `CHANGELOG.md` for all packages ([`lerna version`](https://github.com/lerna/lerna/tree/master/commands/version)). The bumped version is used for release candidate `.zip`'s of the built plugins.

Additionally license checks are done, learn more [here](../advanced/license-checker.md).

### Build

Job definitions in:

-   [`devops/.gitlab/stage-build.ts`](../usage/folder-structure/root.md#folder-structure)
-   [`packages/*/devops/.gitlab/stage-build.ts`](../usage/folder-structure/root.md#folder-structure)
-   [`plugins/*/devops/.gitlab/stage-build.ts`](../usage/folder-structure/plugin.md#folder-structure)

After validation succeeds the usual [build process](../advanced/build-production-plugin.md) starts. [Linting](../usage/available-commands/plugin.md#development), [technical docs](../usage/available-commands/plugin.md#documentation) and the [build](../usage/available-commands/plugin.md#build) process itself are parts of this stage. For packages, only linting is done because they are directly consumed in the plugins' build process.

As [artifact](https://docs.gitlab.com/ee/user/project/pipelines/job_artifacts.html) you will get the technical docs (as `.zip`) and the installable plugin files.

There is one job related to the root: Lint `common` files.

### Test

Job definitions in:

-   [`devops/.gitlab/stage-test.ts`](../usage/folder-structure/root.md#folder-structure)
-   [`packages/*/devops/.gitlab/stage-test.ts`](../usage/folder-structure/root.md#folder-structure)
-   [`plugins/*/devops/.gitlab/stage-test.ts`](../usage/folder-structure/plugin.md#folder-structure)

The plugins are built now. Let's make some [tests](../advanced/tests.md). Currently, [E2E tests](../advanced/tests.md#e2e) are executed (only [own runner](./use-own-runner.md)) and unit tests / Snapshot tests with Jest and PHPUnit. Another important job at this stage is to collect all build files and put them in a single artifact for further usage in [Review applications](./review-applications.md).

### Release

Job definitions in: [`devops/.gitlab/stage-validate.ts`](../usage/folder-structure/root.md#folder-structure)

The release stage contains two different "release jobs". The `release` job is only executed in `master` branch. It prepares further deployment in the next stage [`build-production`](#build-production) and releases non-`private` packages to [npmjs.com](https://npmjs.com) directly with [`lerna publish`](https://github.com/lerna/lerna/tree/master/commands/publish).

The other job `docker review start` is only executed in non-`master` branches (and requires [own runner](./use-own-runner.md)). It starts a complete new docker container environment and provides an URL where the [reviewer](./review-applications.md) can access it.

### Build production

Job definitions in: [`plugins/*/devops/.gitlab/stage-build-production.ts`](../usage/folder-structure/plugin.md#folder-structure)

This stage is meant to only run on `master` branch. It [builds](../advanced/build-production-plugin.md) your plugins again and prepares it for upload on wordpress.org. It stores the installable plugin `.zip` file as artifact - upload it for example to your live site or a license server.

### Deploy

Job definitions in: [`plugins/*/devops/.gitlab/stage-deploy.ts`](../usage/folder-structure/plugin.md#folder-structure)

This stage is meant to only run on `master` branch. It has an example job definition you can extend, to upload the artifact files (technical docs and the installable plugin) to your server with [`wput`](http://wput.sourceforge.net/).

It comes also with a predefined job uploading the new plugin to the wordpress.org SVN repository automatically ([requires configuration](extend-gitlab-ci-pipeline.md#release)).
