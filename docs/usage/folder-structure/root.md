# Root

Here is a complete list of all available files and folders in the root directory with a short description.

You don't need to know the contents of each file individually, as you will learn what they do over time. Just make sure you understand what **you are responsible for** (see the links). In addition, folders/files marked with a **prefixed** 💡 are important for **getting started** (that means, learn more about it).

All files and folders written in italics are not saved in Git, but automatically arranged when you develop or deploy the projects.

{% hint style="warning" %}
Don't give up too early, because it can take a while until you find your way in the **modern (WordPress plugin) development**!
{% endhint %}

### Folder structure

-   📁 `my-plugin` Folder you created with `create-wp-react-app create-workspace`
    -   📁 _`coverage`_ Coverage reports, see [this](../../advanced/tests.md#coverage)
    -   📁 `.vscode` Visual Studio Code (VSCode) specific files
        -   📄 `extensions.json` Recommend VSCode extensions, [read more](https://code.visualstudio.com/docs/editor/extension-gallery#_workspace-recommended-extensions)
        -   📄 `settings.json` Predefined VSCode settings, [read more](https://code.visualstudio.com/docs/getstarted/settings)
        -   📄 `launch.json` Predefined VSCode PHP debug settings, [read more](../../php-development/debugging.md)
        -   📄 `tasks.json` Predefined VSCode PHP debug tasks, [read more](../../php-development/debugging.md)
    -   📁 `common` Common files can be reused by plugins and packages, or are root specific
        -   📁 `create-wp-react-app` Templates or new plugins created with `create-wp-react-app`, but you can ignore it - just commit it to your repository
        -   📁 `patch-package` See [patch-package](https://www.npmjs.com/package/patch-package) package
        -   📄 `.env-default` Default values for environment variables which are used in Docker Compose
        -   📄 `.eslintrc` ESLint [configuration file](https://eslint.org/docs/user-guide/configuring)
        -   📄 `generate-launch-json.ts` Dynamically create `launch.json` file with all available plugins and packages (used for [PHP debugging](../../php-development/debugging.md))
        -   📄 `Gruntfile.plugin.ts` Predefined tasks for [Grunt](https://gruntjs.com/sample-gruntfile) (only plugin, e. g. [build a plugin](../../advanced/build-production-plugin.md))
        -   📄 `Gruntfile.ts` Predefined tasks for [Grunt](https://gruntjs.com/sample-gruntfile) (packages and plugins)
        -   📄 `hookdoc.json` Configuration file for [wp-hookdoc](https://github.com/matzeeable/wp-hookdoc), used in [`yarn docs:hooks`](../available-commands/plugin.md#documentation)
        -   📄 `jest.base.js` Base Jest [configuration file](https://jestjs.io/docs/en/configuration)
        -   📄 `phpcs.xml` Base PHP CodeSniffer [configuration file](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Configuration-Options)
        -   📄 `phpunit.base.php` Base PHPUnit [bootstrap](https://phpunit.readthedocs.io/en/8.4/configuration.html) file
        -   📄 `postcss-plugin-clean.ts` [clean-css](https://github.com/jakubpawlowicz/clean-css) plugin for [PostCSS](https://postcss.org/)
        -   📄 💡 `tsconfig.json` Base TypeScript [configuration file](https://www.typescriptlang.org/docs/handbook/tsconfig-json.html)
        -   📄 `vuepress-php.ts` phpDocumentor VuePress theme [configuration file](https://vuepress.vuejs.org/guide/basic-config.html), used in [`yarn docs:php`](../available-commands/plugin.md#documentation)
        -   📄 💡 `webpack.factory.ts` Base webpack [configuration file](https://webpack.js.org/configuration/) for plugins, see [here](../../advanced/extend-compose-webpack.md#webpack)
        -   📄 `webpack.multi.ts` Multi-package configuration for `yarn docker:start`
    -   📁 `devops` Files related to CI/CD, Docker and so on
        -   📁 `.gitlab` [CI/CD predefined jobs](../../gitlab-integration/predefined-pipeline.md), included in root `.gitlab-ci.ts`
            -   📄 `stage-containerize.ts` Jobs for building and pushing docker files to GitLab container registry
            -   📄 `stage-build.ts` Jobs for build plugin, docs and linting
            -   📄 `stage-release.ts` Jobs for release, review applications and wordpress.org deployment
            -   📄 `stage-test.ts` Jobs for tests
            -   📄 `stage-validate.ts` Jobs for Docker garbage collection, semantic versioning and license scanner
        -   📁 `docker` Predefined [docker images](https://docs.docker.com/engine/reference/builder/)
            -   📁 `gitlab-ci` Dockerfile used in GitLab CI/CD jobs
        -   📁 `docker-compose` [Compose files](https://docs.docker.com/compose/compose-file/) for different contexts
            -   📄 `docker-compose.e2e.yml` Used in Cypress [E2E tests](../../advanced/tests.md#e2e)
            -   📄 `docker-compose.local.yml` Used locally with port expose
            -   📄 `docker-compose.traefik.yml` Used for [Review Apps](../../gitlab-integration/review-applications.md)
            -   📄 `docker-compose.yml` Base compose file, merged automatically with `{plugins,packages}/*/devops/docker-compose/docker-compose.yml` files for extensibility, see [here](../../advanced/extend-compose-webpack.md#docker-compose)
        -   📁 `scripts` Used in Docker containers ([mounted](https://docs.docker.com/compose/compose-file/#volumes))
            -   📄 `container-wordpress-cli-entrypoint.sh` Extended [entrypoint](https://docs.docker.com/compose/compose-file/#entrypoint) for `wordpress-cli` service
            -   📄 `container-wordpress-command.sh` [Command](https://docs.docker.com/compose/compose-file/#command) for `wordpress` service
            -   📄 `custom-php.ini` Custom PHP [configuration file](https://www.php.net/manual/en/configuration.file.php) for `wordpress` service
            -   📄 `e2e-tests-autologin-plugin.php` Micro-plugin for automatic login via URL in WordPress for E2E tests
            -   📄 `lerna-ready-ci.sh` Make `lerna` work in GitLab CI environment
            -   📄 `purge-ci.sh` Purge Socker resources for E2E tests and Review Apps in CI/CD
            -   📄 `task-xdebug-start.sh` Used in `tasks.json` file, starts XDebug in WordPress container
            -   📄 `task-xdebug-stop.sh` Used in `tasks.json` file, stops XDebug in WordPress container
            -   📄 💡 `wordpress-startup.sh` Global bash script to for custom actions not specific to single plugins, which get executed in start of the WordPress Docker container
    -   📁 `docs` Documentation you currently read available as markdown files
    -   📁 _`node_modules`_ [Node dependencies](https://docs.npmjs.com/files/folders.html#node-modules)
    -   📁 `packages` Non-plugin packages (e.g. for shared styles)
        -   📁 `utils` Predefined utils package coming with `create-wp-react-app create-workspace`
            -   📁 `devops` Files related to CI/CD, Docker and so on for this specific package
                -   📁 `.gitlab`
                    -   📄 `.gitab-ci.ts` [CI/CD similar root file](../../gitlab-integration/predefined-pipeline.md), included in root `.gitlab-ci.ts`
                    -   📄 `stage-build.ts` Predefined jobs for lint, included in `./.gitlab-ci.ts`
                    -   📄 `stage-test.ts` Jobs for tests
                    -   📄 `stage-validate.ts` Jobs for license scanner
            -   📁 `languages` Languages files for this package
                -   📁 `backend` Server-side [language files](../../php-development/localization.md)
                    -   📄 `utils.pot` Language file can be translated with [Poedit](https://poedit.net/)
                -   📁 `frontend` Client-side [language files](../../php-development/localization.md)
                    -   📄 `utils.pot` Language file can be translated with [Poedit](https://poedit.net/)
            -   📁 `lib` TypeScript coding you will consume in your dependents
                -   📁 `components` Predefined ReactJS components
                -   📁 `factory` [Factory functions](../../typescript-development/utils-package.md#factories) (pass arguments, get dynamic functions)
                    -   📄 📁 `ajax.tsx` AJAX related functions (e. g. do WP REST API calls)
                    -   📄 💡 `context.tsx` [React Context](https://reactjs.org/docs/context.html) helpers
                    -   📄 💡 `i18n.tsx` `__()` and so on for your client-side translations (uses [`@wordpress/i18n`](https://www.npmjs.com/package/@wordpress/i18n))
                    -   📄 `index.tsx` Export all files from this folder
                -   📁 `types` Additional [declaration files](https://www.typescriptlang.org/docs/handbook/declaration-files/introduction.html)
                    -   📄 `global.d.ts` Avoid errors on plain JS packages (see [this](https://git.io/JeMCt))
                -   📁 `wp-api` Predefined WP REST API types
                    -   📄 `index.tsx` Export all files from this folder
                    -   📄 `rest.plugin.get.tsx` Types for `wp-json/your-plugin/plugin`
                -   📄 `helpers.tsx` Some helper functions
                -   📄 `index.tsx` Export all files from this folder
                -   📄 💡 `options.tsx` Base options class of `src/inc/base/Assets.php` output
            -   📁 `scripts` Scripts related to development
                -   📄 💡 `Gruntfile.ts` [Gruntfile](https://gruntjs.com/sample-gruntfile) for this package, extends `common/Gruntfile.ts`
            -   📁 `src` PHP coding you will consume in your dependents
                -   📄 💡 `Activator.php` [Abstract trait](../../php-development/predefined-classes.md#activator) for activate, deactivate and install actions
                -   📄 💡 `Assets.php` [Abstract trait](../../php-development/predefined-classes.md#assets) for assets management
                -   📄 💡 `Base.php` [Abstract base trait](../../php-development/predefined-classes.md#notice)
                -   📄 💡 `Core.php` [Abstract core trait](../../php-development/predefined-classes.md#core) for main initialization of namespacing and so on, similar to the well-known `functions.php`
                -   📄 💡 `PluginReceiver.php` [Abstract trait](../../advanced/create-package.md#dynamically-get-plugin-data) for package development
                -   📄 💡 `Localization.php` [Abstract trait](../../php-development/predefined-classes.md#localization) for i18n functionality
                -   📄 💡 `Service.php` Final class for boilerplate related endpoints
                -   📄 💡 `PackageLocalization.php` Extends from Localization class and is a helper class for package localization
            -   📁 _`node_modules`_ [Node dependencies](https://docs.npmjs.com/files/folders.html#node-modules)
            -   📁 `test`
                -   📁 `jest` Put all your [Jest](../../advanced/tests.md#jest) tests here
                -   📁 `phpunit` Put all your [PHPUnit](../../advanced/tests.md#phpunit) tests here
                -   📄 `jest.config.js` Jest [configuration file](https://jestjs.io/docs/en/configuration)
                -   📄 `jest.setup.js` Jest [setup file](https://jestjs.io/docs/en/configuration#setupfiles-array)
                -   📄 `patchwork.json` Patchwork [configuration file](http://patchwork2.org/features/)
                -   📄 `phpunit.bootstrap.php` PHPUnit [bootstrap](https://phpunit.readthedocs.io/en/8.4/configuration.html) file
                -   📄 `phpunit.xdebug.php` PHPUnit + [XDebug filtering](<(https://xdebug.org/docs/code_coverage)>) for faster code coverage analysis
                -   📄 `phpunit.xml` PHPUnit [configuration file](https://phpunit.readthedocs.io/en/8.4/configuration.html)
            -   📄 `CHANGELOG.md` [Conventional changelog](https://github.com/conventional-changelog/conventional-changelog) output
            -   📄 `composer.json` Composer [configuration file](https://getcomposer.org/doc/04-schema.md)
            -   📄 `composer.lock` Composer [lock file](https://getcomposer.org/doc/01-basic-usage.md#installing-with-composer-lock)
            -   📄 `LICENSE` Package license file
            -   📄 _`LICENSE_3RD_PARTY_JS.md`_ Yarn dependencies disclaimer, see [License checker](../../advanced/license-checker.md#javascript)
            -   📄 _`LICENSE_3RD_PARTY_PHP.md`_ Composer dependencies disclaimer, see [License checker](../../advanced/license-checker.md#php)
            -   📄 `package.json` Package [definition file](https://docs.npmjs.com/files/package.json)
            -   📄 `README.md` "Because no one can read your mind (yet)" - [makeareadme.com](https://www.makeareadme.com/)
            -   📄 `tsconfig.json` TypeScript [configuration file](https://www.typescriptlang.org/docs/handbook/tsconfig-json.html), extends `common/tsconfig.json`
    -   📁 💡 `plugins` See [Plugin folder structure](plugin.md)
    -   📄 `.gitignore` Ignore files in your Git repository on commit (see [this](https://git-scm.com/docs/gitignore))
    -   📄 💡 `.gitlab-ci.yml` [CI/CD root file](../../gitlab-integration/predefined-pipeline.md)
    -   📄 💡 `.gitlab-ci.ts` [CI/CD root file](../../gitlab-integration/predefined-pipeline.md), should include all `{packages,plugins}/*/devops/.gitlab/.gitlab-ci.ts` files
    -   📄 `.prettierignore` Ignore files to be prettified (see [this](https://prettier.io/docs/en/ignore.html))
    -   📄 `package.json` Package [definition file](https://docs.npmjs.com/files/package.json) with [Yarn Workspace](https://yarnpkg.com/lang/en/docs/workspaces/#toc-how-to-use-it) definition
    -   📄 `README.md` "Because no one can read your mind (yet)" - [makeareadme.com](https://www.makeareadme.com/)
    -   📄 `yarn.lock` [Lock file](https://yarnpkg.com/lang/en/docs/yarn-lock/) for JavaScript dependencies
