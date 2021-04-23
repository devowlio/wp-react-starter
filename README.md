# WP React Starter: WordPress React Boilerplate

> **DEPRECATED**: WP React Starter was a "research project" of [devowl.io](https://devowl.io/) for the development of our WordPress plugins. Unfortunately, we don't have enough resources to regularly contribute the developments of our private monorepo to WP React Starter. You are welcome to continue using or forking this project, but it will no longer be updated or extended with new features, structures, etc.

<img align="right" src="https://assets.devowl.io/git/wp-react-starter/logo.png" alt="WP React Starter Logo" height="180" />

Create (multiple) WordPress plugins that use **React**, **TypeScript**, and **object-oriented PHP** in a fully customizable **Docker** development environment, commited in a **monorepo**.

> Wow, I didn't know the WordPress plugin development could look like this!

🚀 **Instant no-config plugin creation with** [**create-wp-react-app**](https://github.com/devowlio/create-wp-react-app) 🔥

[![GitHub stars](https://img.shields.io/github/stars/devowlio/wp-react-starter?style=flat&logo=github)](https://github.com/devowlio/wp-react-starter)
[![Join on Slack](https://img.shields.io/badge/Slack-join-green.svg?style=flat&logo=slack)](https://matthias-web.com/slack)
[![codecov](https://codecov.io/gl/devowlio/wp-reactjs-starter/branch/master/graph/badge.svg)](https://codecov.io/gl/devowlio/wp-reactjs-starter)
[![GitLab CI/CD](https://img.shields.io/badge/CI%20%2F%20CD-See%20history-green?logo=gitlab)](https://gitlab.com/devowlio/wp-reactjs-starter/pipelines)

### **🤗 Why WordPress plugin development is fun with WP React Starter**

Everyone tells us: **WordPress plugins are a mess.** Our answer is always: **Let’s take this opportunity** to make the system that powers every third website on the Internet better.

With WP React Starter we have created a modern WordPress development boilerplate which contains everything you are used to from modern web development projects:

-   **React** Frontend for reactive user interfaces (with PHP fallback for server-side rendering) - React is a part of WordPress since the [Gutenberg](https://wordpress.org/gutenberg/) release
-   **TypeScript** for typesafe frontend development
-   **PHP in an object-oriented style** with namespaces for better backend code
-   **Docker** development environment to develop all you plugins without manual setup steps
-   **CI/CD integration** for automated code quality checks and release management (publish on [wordpress.org](https://wordpress.org/plugins/developers/) or wherever you want)

Does that sound like crappy WordPress plugin development or what you really have been looking for for your plugins for a long time? **Let's start today with your first WordPress plugin! Create it within 5 minutes, thanks to our CLI** [**create-wp-react-app**](https://github.com/devowlio/create-wp-react-app)

### **Client-Side Features**

_Familiar React API & patterns (ES6) with TypeScript_

-   [**React**](https://reactjs.org/) with **Babel** `env` preset + **Hooks**
-   [**MobX**](https://github.com/mobxjs/mobx) for state management
-   [**webpack**](https://webpack.js.org/) build for assets
-   [**core-js**](https://github.com/zloirock/core-js) puts automatically needed polyfills to your distribution files
-   [**Sourcemap**](https://www.html5rocks.com/en/tutorials/developertools/sourcemaps/) generation for debugging purposes (CSS and TypeScript files)
-   [**SASS**](http://sass-lang.com/) stylesheets compiler (`.scss` files) for next-gen CSS
-   [**PostCSS**](http://postcss.org/) for transforming SCSS (including autoprefixing) to CSS
-   **Minified sources** automatically generated for production (JS, CSS)
-   [**Grunt**](https://gruntjs.com/) for automation tasks (build the installable plugin)
-   [**ESLint**](https://eslint.org/) predefined configuration for proper **linting**
-   [**TypeDoc**](https://typedoc.org/guides/doccomments/) for JavaScript Documentation
-   [**WP HookDoc**](https://github.com/matzeeable/wp-hookdoc) for Filters & Actions Documentation
-   **Translation (i18n)** with automatic generation of `.pot` files
-   **Add-On Development** (multiple WordPress plugins), based on a predefined `utils` package that allows you to share TypeScript types across plugins.
-   Admin backend components, in this case an own page with a button (`admin.ts`)
-   Frontend components, in this case a simple widget (`widget.ts`)

### **Server-Side Features**

_OOP-style for building a high-quality PHP development_

-   PHP &gt;= **5.6** required: An admin notice is showed when not available
-   WordPress &gt;= **5.2** required: An admin notice is showed when not available with a link to the updater
-   [**PHP CodeSniffer**](https://github.com/squizlabs/PHP_CodeSniffer) predefined configuration for proper **linting**
-   [**Namespace**](http://php.net/manual/en/language.namespaces.rationale.php) support
-   [**Autloading**](http://php.net/manual/en/language.oop5.autoload.php) classes in connection with namespaces
-   [**WP REST API v2**](http://v2.wp-api.org/) for API programming, no longer use `admin-ajax.php` for CRUD operations
-   [`SCRIPT_DEBUG`](https://codex.wordpress.org/Debugging_in_WordPress#SCRIPT_DEBUG) enables not-minified sources for debug sources (use in connection with `yarn build:js:development`)
-   [**Cachebuster**](http://www.adopsinsider.com/ad-ops-basics/what-is-a-cache-buster-and-how-does-it-work/) for public resources
-   Automatic generation of `.pot` files for **translating (i18n)** the backend plugin
-   [**phpDocumentor**](https://github.com/phpDocumentor/phpDocumentor2) for PHP Documentation
-   [**apiDoc**](http://apidocjs.com//) for API Documentation

### **Automation Features**

_Avoid repetitive work and develop more feature_

-   Workspace creation with **end-to-end setup**: `create-wp-react-app create-workspace`
-   Plugin creation with **monorepo integration**: `create-wp-react-app create-plugin`
-   Package creation with **monorepo integration**: `create-wp-react-app create-package`
-   Predefined [**GitLab CI**](https://about.gitlab.com/product/continuous-integration/) example for **Continous Integration** ([read more](./#using-ci-cd))
-   [**Scoping**](https://github.com/humbug/php-scoper) your PHP coding and dependencies so they are isolated (avoid dependency version conflicts)
-   **Packaging and publishing** of you plugin [wordpress.org](https://wordpress.org/plugins/developers/) ([read more](https://devowlio.gitbook.io/wp-react-starter/gitlab-integration/deploy-wp-org))
-   [**license-checker**](https://www.npmjs.com/package/license-checker) for automated **3th-party-code license scanning** and compliance check

### **Developer Experience Features**

_Providing the right development environment for high quality plugins_

-   Built on top of [**Visual Studio Code**](https://code.visualstudio.com/) (extensions are automatically installed)
-   All your plugins within [**yarn workspaces**](https://yarnpkg.com/lang/en/docs/workspaces/)
-   [**Prettier**](https://prettier.io/) for automatic JavaScript / TypeScript code **formatting on save** (VSCode required)
-   [**PHP CodeSniffer's cbf**](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically) for automatic PHP code **formatting on save** (VSCode required)
-   [**Husky**](https://github.com/typicode/husky) integration for code formatting before Git commit - never have ugly code in your repository
-   **Husky** is also used for [**commitlint**](https://github.com/conventional-changelog/commitlint) to become a common commit message style in your repository
-   [**lerna**](https://lerna.js.org/) for **semantic versioning** and **changelog generation**
-   [**webpackbar**](https://github.com/nuxt/webpackbar) so you can get a real progress bar while development
-   [**Docker**](https://www.docker.com/) for a **local development** environment
-   Predefined WordPress **Stubs** so you get autocompletion for WordPress classes and functions, e. g. `add_action`
-   Within the Docker environment you have [**WP-CLI**](https://developer.wordpress.org/cli/commands/) available
-   Predefined [**Review Apps**](https://docs.gitlab.com/ee/ci/review_apps/) example for branch deployment, read more [here](./#using-ci-cd)
-   Predefined VSCode **PHP debugging** environment

### **Testing Features**

_Cover your source code with test code to to guarantee the last piece quality_

-   [**PHPUnit**](https://phpunit.de) for **PHP** unit testing
-   [**Jest**](https://jestjs.io/) for **TypeScript** unit- and snapshot testing
-   Collect code coverage reports with a single command in each package
-   Automatically push coverage reports to [codecov.io](https://codecov.io)
-   [**Cypress**](https://www.cypress.io/) for **End-To-End** (E2E) tests
-   [**Gherkin**](https://cucumber.io/docs/gherkin/) syntax to write E2E features (combined with Cypress)
-   Automatically failure a GitLab CI pipeline if a coverage percent is not reached (threshold)
-   🚀 **The complete test suite is integrated in GitLab CI**

### Documentation

You want to dive deep into the documentation of WP React Starter? Check, we convinced another developer to write high quality WordPress plugins. 🚀

#### Usage

-   [Getting started](https://devowlio.gitbook.io/wp-react-starter/usage/getting-started)
-   [Folder structure](https://devowlio.gitbook.io/wp-react-starter/usage/folder-structure)
    -   [Root](https://devowlio.gitbook.io/wp-react-starter/usage/folder-structure/root)
    -   [Plugin](https://devowlio.gitbook.io/wp-react-starter/usage/folder-structure/plugin)
-   [Available commands](https://devowlio.gitbook.io/wp-react-starter/usage/available-commands)
    -   [Root](https://devowlio.gitbook.io/wp-react-starter/usage/available-commands/root)
    -   [Plugin](https://devowlio.gitbook.io/wp-react-starter/usage/available-commands/plugin)
    -   [Package](https://devowlio.gitbook.io/wp-react-starter/usage/available-commands/package)

#### PHP development

-   [Predefined constants](https://devowlio.gitbook.io/wp-react-starter/php-development/predefined-constants)
-   [Predefined classes](https://devowlio.gitbook.io/wp-react-starter/php-development/predefined-classes)
-   [Example implementations](https://devowlio.gitbook.io/wp-react-starter/php-development/example-implementations)
-   [Add new classes, hooks and libraries](https://devowlio.gitbook.io/wp-react-starter/php-development/add-classes-hooks-libraries)
-   [Localization](https://devowlio.gitbook.io/wp-react-starter/php-development/localization)
-   [Debugging](https://devowlio.gitbook.io/wp-react-starter/php-development/debugging)

#### TypeScript development

-   [Utils package](https://devowlio.gitbook.io/wp-react-starter/typescript-development/utils-package)
-   [Example implementations](https://devowlio.gitbook.io/wp-react-starter/typescript-development/example-implementations)
-   [Add external library](https://devowlio.gitbook.io/wp-react-starter/typescript-development/add-external-library)
-   [Consume PHP variable](https://devowlio.gitbook.io/wp-react-starter/typescript-development/consume-php-variable)
-   [Using entrypoints](https://devowlio.gitbook.io/wp-react-starter/typescript-development/using-entrypoints)
-   [Localization](https://devowlio.gitbook.io/wp-react-starter/typescript-development/localization)

#### Advanced

-   [Build production plugin](https://devowlio.gitbook.io/wp-react-starter/advanced/build-production-plugin)
-   [How cachebuster works](https://devowlio.gitbook.io/wp-react-starter/advanced/how-cachebuster-works)
-   [Tests](https://devowlio.gitbook.io/wp-react-starter/advanced/tests)
-   [Extend Compose and Webpack](https://devowlio.gitbook.io/wp-react-starter/advanced/extend-compose-webpack)
-   [Create package](https://devowlio.gitbook.io/wp-react-starter/advanced/create-package)
-   [Create Add-On](https://devowlio.gitbook.io/wp-react-starter/advanced/create-add-on)
-   [Persistent database snapshot](https://devowlio.gitbook.io/wp-react-starter/advanced/persistent-database-snapshot)
-   [Showcase](https://devowlio.gitbook.io/wp-react-starter/advanced/showcase)
-   [License checker](https://devowlio.gitbook.io/wp-react-starter/advanced/license-checker)

#### GitLab integration

-   [Predefined pipeline](https://devowlio.gitbook.io/wp-react-starter/gitlab-integration/predefined-pipeline)
-   [Extend GitLab CI pipeline](https://devowlio.gitbook.io/wp-react-starter/gitlab-integration/extend-gitlab-ci-pipeline)
-   [Use own runner](https://devowlio.gitbook.io/wp-react-starter/gitlab-integration/use-own-runner)
-   [Review applications](https://devowlio.gitbook.io/wp-react-starter/gitlab-integration/review-applications)
-   [Deploy wordpress.org](https://devowlio.gitbook.io/wp-react-starter/gitlab-integration/deploy-wp-org)

### Licensing

Thank you for your interest in WP React Starter. This boilerplate was developed organically over years and we at [devowl.io](https://devowl.io/) bring all our experience from best-selling WordPress plugins like [WordPress Real Media Library](https://codecanyon.net/item/wordpress-real-media-library-media-categories-folders/13155134) as well as customer web development orders to this project. **With WP React Starter you get dozens of hundred working hours compressed into one easy-to-use solution.**

We would like to share our knowledge and solution with you to make the development of WordPress plugins more professional. **But we are even happier if you also share your knowledge to make this project even better.**

WP React Starter is licensed partly under [GNU General Public License v3.0 (GPL v3.0 or later)](https://www.gnu.org/licenses/gpl-3.0.en.html) and partly under our [ISC License (ISC)](https://opensource.org/licenses/ISC). Feel free to develop high-quality WordPress plugins at light speed with WP React Starter in real projects. **Don't worry, it's free to use for all non-commercial and commercial WordPress plugins!**
