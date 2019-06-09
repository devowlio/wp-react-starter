<h1><p align="center">WordPress ReactJS Boilerplate :sparkling_heart:</p></h1>
<p align="center">This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin (Frontend Widget, WordPress backend menu page) - within a fully customizable Docker development environment.</p>

---

:new: :fire: **Instant no-config plugin creation with [create-wp-react-app](https://github.com/matzeeable/create-wp-react-app)**

[![GitHub tag](https://img.shields.io/github/tag/matzeeable/wp-reactjs-starter.svg?colorB=green)](https://github.com/matzeeable/wp-reactjs-starter)
[![license](https://img.shields.io/github/license/matzeeable/wp-reactjs-starter.svg?colorB=green)](https://github.com/matzeeable/wp-reactjs-starter/blob/master/LICENSE)
[![Slack channel](https://img.shields.io/badge/Slack-join-green.svg)](https://matthias-web.com/slack)

**Client-side features:** _Familiar React API & patterns (ES6) with TypeScript_

-   [**ReactJS**](https://reactjs.org/) v16 with **Babel v7** `env` preset
-   [**mobx-state-tree**](https://github.com/mobxjs/mobx-state-tree) for Model Driven State Management
-   [**webpack**](https://webpack.js.org/) v4 build for assets
-   CSS and TypeScript [**Sourcemap**](https://www.html5rocks.com/en/tutorials/developertools/sourcemaps/) generation for debugging purposes
-   [**SASS**](http://sass-lang.com/) stylesheets compiler (`.scss` files)
-   [**PostCSS**](http://postcss.org/) for transforming SCSS (including autoprefixing) to CSS
-   Generation of **minified** sources for production (JS, CSS)
-   [**Grunt**](https://gruntjs.com/) for automation tasks
-   [**TSLint**](https://palantir.github.io/tslint/) predefined configuration for proper linting
-   Admin backend components, in this case an own page with a button (`public/src/admin.js`)
-   Frontend components, in this case a simple widget (`public/src/widget.js`)

**Server-side features:** _OOP-style for building a high-quality plugin._

-   PHP >= **5.3** required: An admin notice is showed when not available
-   WordPress >= **4.4** required: An admin notice is showed when not available with a link to the updater
-   [**Namespace**](http://php.net/manual/en/language.namespaces.rationale.php) support
-   [**Autloading**](http://php.net/manual/en/language.oop5.autoload.php) classes in connection with namespaces
-   [**WP REST API v2**](http://v2.wp-api.org/) for API programming, no longer use `admin-ajax.php` for your CRUD operations
-   [`SCRIPT_DEBUG`](https://codex.wordpress.org/Debugging_in_WordPress#SCRIPT_DEBUG) enables not-minified sources for debug sources (use in connection with `yarn build-dev`)
-   [**Cachebuster**](http://www.adopsinsider.com/ad-ops-basics/what-is-a-cache-buster-and-how-does-it-work/) for public resources (`public`)
-   Predefined `.po` files for **translating (i18n)** the plugin
-   [**phpDocumentor**](https://github.com/phpDocumentor/phpDocumentor2) for PHP Documentation
-   [**JSDoc**](http://usejsdoc.org/) for JavaScript Documentation
-   [**apiDoc**](http://apidocjs.com//) for API Documentation
-   [**WP HookDoc**](https://github.com/matzeeable/wp-hookdoc) for Filters & Actions Documentation

**Other features:** _Providing the right development environment for a quality plugin._

-   Built on top of [**VSCode**](https://code.visualstudio.com/) (extensions are automatically installed)
-   [**Prettier**](https://prettier.io/) for automatic code beautifying on save (VSCode IDE)
-   [**Prettier PHP**](https://github.com/prettier/plugin-php#editor-integration) for PHP beautifying (needs manual installation)
-   [**Husky**](https://github.com/typicode/husky) integration for code beautifying (PHP, TS) before GIT commit - never have ugly code in your repository
-   **Husky** is also used for [**commitlint**](https://github.com/conventional-changelog/commitlint) to become a common commit message style in your repository
-   [**webpackbar**](https://github.com/nuxt/webpackbar) so you can get a real progress bar while development
-   Predefined [**GitLab CI**](https://about.gitlab.com/product/continuous-integration/) example for Continous Integration / Deployment
-   [**Docker**](https://www.docker.com/) for a local development environment
-   Within the Docker environment you have [**WP-CLI**](https://developer.wordpress.org/cli/commands/) available
-   [**Cypress**](https://www.cypress.io/) for End-To-End (E2E) tests

## :white_check_mark: Prerequesits

-   [**Node.js**](https://nodejs.org/) as JavaScript engine
-   [**Yarn**](https://yarnpkg.com/lang/en/) `yarn` command globally available in CLI (alternative to Node's `npm`)
-   [**Composer**](https://getcomposer.org/) `composer` command globally available in CLI
-   [**Docker**](https://docs.docker.com/install/) `docker` and `docker-compose` command globally available in CLI

## :mountain_bicyclist: Getting Started

The brandnew **[create-wp-react-app](https://github.com/matzeeable/create-wp-react-app)** allows you now to create your plugin with a single command!

#### Installation

```sh
$ yarn add -g create-wp-react-app
```

#### Create plugin

```sh
$ create-wp-react-app create my-plugin
```

![generate cli](https://matthias-web.com/wp-content/uploads/Posts/create-wp-react-app.gif)

## :book: Documentation

1. [Folder structure](#folder-structure)
1. [Available commands](#available-commands)
1. [Local environment](#local-environment)
1. [Make the boilerplate yours](#make-the-boilerplate-yours)
1. [Available constants](#available-constants)
1. [Activation hooks](#activation-hooks)
1. [Add hooks and own classes](#add-hooks-and-own-classes)
1. [Add external PHP library](#add-external-php-library)
1. [Add external JavaScript library](#add-external-javascript-library)
1. [Using the cachebuster](#using-the-cachebuster)
1. [WP REST API v2](#wp-rest-api-v2)
1. [JavaScript state management](#javascript-state-management)
1. [Localization](#localization)
1. [Building production plugin](#building-production-plugin)

## Folder structure

-   **`build`**: Build relevant files and predefined grunt tasks
-   **`docker`**: Docker relevant files like containers, scripts and compose files
-   **`dist`**: The production plugin, see [Building production plugin](#building-production-plugin)
-   **`docs`**: Auto generated docs (for example for PHP, JS and API Doc), see [Available commands](#available-commands)
-   **`inc`**: All server-side files (PHP)
    -   **`base`**: Abstract base classes
    -   **`general`**: General files for the plugin
    -   **`menu`**: Example page (backend)
    -   **`others`**: Other files (cachebusters, ...)
    -   **`rest`**: Example REST API service, see [WP REST API v2](#wp-rest-api-v2)
    -   **`widget`**: Example widget
-   **`languages`**: Language files
-   **`public`**: All client-side files (TypeScript, SCSS)
    -   **`lib`**: Put external libraries to this folder (cachebuster is only available for copied node modules, see [Add external JavaScript library](#add-external-javascript-library))
    -   **`src`**: Your source files (see client-side features what's possible)
    -   **`dev`**: Generated development sources (`SCRIPT_DEBUG` is active)
    -   **`dist`**: Generated production sources (`SCRIPT_DEBUG` is not active)
-   **`index.php`**: The plugins index file. The entry point for your plugin.
-   **`uninstall.php`**: When uninstalling your plugin this file gets called
-   _`.babelrc`_: [Babel configuration](https://babeljs.io/docs/usage/babelrc/)
-   _`.gitlab-ci.yml`_: [GitLab CI configuration](https://docs.gitlab.com/ee/ci/yaml/)
-   _`.prettierrc`_: [Prettier configuration](https://prettier.io/docs/en/configuration.html)
-   _`commitlint.config.js`_: [Commitlint configuration](https://commitlint.js.org/#/)
-   _`composer.json`_: [Composer package configuration](https://getcomposer.org/)
-   _`Gruntfile.js`_: [Grunt automation file](https://gruntjs.com/sample-gruntfile)
-   _`package.json`_: [NPM package configuration](https://docs.npmjs.com/files/package.json)
-   _`postcss.config.js`_: [PostCSS configuration](https://github.com/postcss/postcss-loader#configuration)
-   _`tsconfig.json`_: [TypeScript configuration](https://www.typescriptlang.org/docs/handbook/tsconfig-json.html)
-   _`tslint.json`_: [TSLint configuration](https://palantir.github.io/tslint/usage/configuration/)
-   _`webpack.config.js`_: [webpack configuration](https://webpack.github.io/docs/configuration.html)

## Available commands

| Command&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; | Context            | Description                                                                                                                                                                                                                                                                                                                  |
| ------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `yarn start-development`                                                                                                                                            | Docker, `.tsx`     | Starts to watch the `public/src` folder for file changes and automatically runs the `build-dev` script. Additionally the npm script `webpack-build-done` is executed after each webpack build. Before the watcher is started the Docker container gets created and started, see also [Local environment](#local-environment) |
| `yarn stop-development`                                                                                                                                             | Docker             | Stops the docker services                                                                                                                                                                                                                                                                                                    |
| `yarn rm-development`                                                                                                                                               | Docker             | Removes the docker services. This does not remove any volumes so if you start the development again all is as before (installed plugins, uploaded files, ...)                                                                                                                                                                |
| `yarn purge-development`                                                                                                                                            | Docker             | Removes and purges the docker services compoletely with volumes included                                                                                                                                                                                                                                                     |
| `yarn wp-cli <command>`                                                                                                                                             | Docker, WP-CLI     | Run a WP-CLI command within the WordPress environment, for example `yarn wp-cli "wp core version"`. Use `--silent` to suppress the output of npm itself                                                                                                                                                                      |
| `yarn db-snapshot <file>`                                                                                                                                           | Docker, WP-CLI, DB | Make a snapshot of the current defined database tables (see below how to configure) and safe to a file                                                                                                                                                                                                                       |
| `yarn db-snapshot-installation`                                                                                                                                     | Docker, WP-CLI, DB | Make a snapshot of the current defined database tables (see below how to configure) and save them in that way, that the next WordPress install will import that snapshot                                                                                                                                                     |
| `yarn db-snapshot-import`                                                                                                                                           | Docker, WP-CLI, DB | The installation snapshot taken                                                                                                                                                                                                                                                                                              |
| with `yarn db-snapshot-installation` is imported to the current running Docker instance. This can be useful for tests for example                                   |
| `yarn release`                                                                                                                                                      | Source files       | A wrapper for [`yarn version`](https://yarnpkg.com/lang/en/docs/cli/version/). You should avoid the original command and use always `yarn release`. For example you can run `yarn release --minor` to create a new minor version                                                                                             |
| `yarn serve`                                                                                                                                                        | Source files       | Bundles all the plugin files together and puts it into the `dist` folder. This folder can be pushed to the wordpress.org SVN. See [Building production plugin](#building-production-plugin).                                                                                                                                 |
| `yarn build`                                                                                                                                                        | `.tsx`             | Create production build of ReactJS files. The files gets generated in `public/dist`. This files should be loaded when `SCRIPT_DEBUG` is not active. Learn more here: [Building production plugin](#building-production-plugin)                                                                                               |
| `yarn build-dev`                                                                                                                                                    | `.tsx`             | Create development build of ReactJS files. The files gets generated in `public/dev`. This files should be loaded when `SCRIPT_DEBUG` is active.                                                                                                                                                                              |
| `yarn lint`                                                                                                                                                         | `.tsx`             | Prints out errors and warning about coding styles.                                                                                                                                                                                                                                                                           |
| `yarn phpdocs`                                                                                                                                                      | `.php`             | Generate PHP docs in `docs/php` of `inc`.                                                                                                                                                                                                                                                                                    |
| `yarn jsdocs`                                                                                                                                                       | `.tsx`             | Generate JS docs in `docs/js` of `public/src`.                                                                                                                                                                                                                                                                               |
| `yarn apidocs`                                                                                                                                                      | `.php`             | Generate API docs in `docs/api` of `inc`.                                                                                                                                                                                                                                                                                    |
| `yarn hookdocs`                                                                                                                                                     | `.php`             | Generate Actions and Filters docs in `docs/hooks` of `inc`.                                                                                                                                                                                                                                                                  |
| `yarn docs`                                                                                                                                                         | Source files       | Generates all docs at once.                                                                                                                                                                                                                                                                                                  |
| `yarn test`                                                                                                                                                         | Tests              | Run all available tests (currently only e2e tests are available)                                                                                                                                                                                                                                                             |
| `yarn test-e2e`                                                                                                                                                     | Tests, Cypress     | Runs the test files in `cypress/integration` in your current running `localhost`                                                                                                                                                                                                                                             |
| `yarn cypress open`                                                                                                                                                 | Tests, Cypress     | Opens the Cypress Test Runner as GUI                                                                                                                                                                                                                                                                                         |
| `yarn prettier-write`                                                                                                                                               | Source files       | Iterate all available source files (.tsx, .php) and pretty print them.                                                                                                                                                                                                                                                       |
| `yarn docker-ci-image-build`                                                                                                                                        | Docker             | Builds `docker/container/ci/Dockerfile` so it can be pushed. Usually you do not need this                                                                                                                                                                                                                                    |
| `yarn docker-ci-image-push`                                                                                                                                         | Docker             | Pushes the built `docker/container/ci/Dockerfile` to the `docker-hub-username`/`docker-hub-image-name` from `package.json`. Usually you do not need this                                                                                                                                                                     |
| `yarn grunt public-cachebuster`                                                                                                                                     | Libraries          | Starts to generate the cachebuster files `inc/others/cachebuster.php` (including `public/dist` and `public/dev` hashes) and `inc/others/cachebuster-lib.php` (including `public/lib`). **Note**: Each build with webpack triggers a cachebuster generation.                                                                  |
| `yarn grunt copy-npmLibs`                                                                                                                                           | Libraries          | Copies the defined public libraries in `Gruntfile.js` to the public/lib folder. See [Add external JavaScript library](#add-external-javascript-library).                                                                                                                                                                     |

## Local environment

When running `yarn start-development` a local development environment with Docker Compose is started and you can start development with TypeScript and your PHP files. If you have a look at `docker/development/docker-compose.yml` you can see that services are registered with the following exposed ports:

| Host / Port      | Description                                      |
| ---------------- | ------------------------------------------------ |
| `localhost(:80)` | The webserver with WordPress installation itself |
| `localhost:3306` | The MySQL database server                        |
| `localhost:8079` | The phpMyAdmin interface                         |

#### Initializing database tables

Running the above command the `docker/scripts/container-wordpress-command.sh` file is called. There you can define actions which should be executed to prepare your local environment so you have to for example not activate your plugin manually. If you want to initialize specific database tables after WordPress installation you can do the following steps, in this scenario we create a new blog post:

1. `yarn start-development` so WordPress is installed in `localhost`
1. Login to your WordPress instance and create a new post
1. Define the tables which you want to snapshot for the startup in `package.json#db-snapshot`: `"db-snapshot": ["wp_posts", "wp_postmeta"]`
1. `yarn db-snapshot-installation` to export the defined database tables into a file in `docker/scripts/startup.sql`
1. `yarn purge-development` removes your current WordPress installation completely
1. `yarn start-development` again and you will see that post is immediatly available after creation

## Make the boilerplate yours

_Make it yours?! Sounds crazy._ Yes, it means it can automatically change the **constant names** (PHP), **namespace** prefix (PHP) and the language **`.pot`** filename. All the magic is done by the module [create-wp-react-app](https://github.com/matzeeable/create-wp-react-app). It will ask you a few plugin details in the CLI prompt and fully automatically generates the files for you. When the generator is finished just have a look at the `index.php` file and all is setup for you.

## Available constants

After generating your boilerplate you should have a look in the generated `index.php` file. There are several PHP constants available for your plugin coding:

-   `YOURCONSTANTPREFIX_FILE`: The plugin file (`__FILE__`)
-   `YOURCONSTANTPREFIX_PATH`: The plugins path
-   `YOURCONSTANTPREFIX_INC`: The plugins `inc` folder with trailing slash
-   `YOURCONSTANTPREFIX_MIN_PHP`: The minimum PHP version
-   `YOURCONSTANTPREFIX_NS`: The namespace for your plugin
-   `YOURCONSTANTPREFIX_DB_PREFIX`: The `Base.class.php` offers a method `getTableName()` which returns a valid table name for your plugin
-   `YOURCONSTANTPREFIX_OPT_PREFIX`: If you want to save options you should use this constant for option names prefix
-   `YOURCONSTANTPREFIX_TD`: The text domain for your plugin. See [Localization](#localization)
-   `YOURCONSTANTPREFIX_VERSION`: The version of the plugin
-   `YOURCONSTANTPREFIX_DEBUG`: If true the `Base.class.php::debug()` method writes to the error log

## Activation hooks

There are four types of activation hooks:

-   **Activate**: This hook / code gets executed even the plugin gets activated in the WordPress backend. You can implement your code in `inc/general/Activator.class.php::activate()`.
-   **Deactivate**: This hook / code gets executed even the plugin gets deactivated in the WordPress backend. You can implement your code in `inc/general/Activator.class.php::deactivate()`.
-   **Install**: This hook / code gets executed when the plugin versions changes. That means every update of the plugin executes this code - also the initial plugin activation. Usually you should implement your database table creation with [`dbDelta`](https://developer.wordpress.org/reference/functions/dbdelta/) here. You can implement your code in `inc/general/Activator.class.php::install()`.
-   **Uninstall**: This hook / code gets executed even the plugin gets uninstalled in the WordPress backend. You can implement your code in `uninstall.php`.

## Add hooks and own classes

Your action and filters can be registered in `inc/general/Core.class.php::init()/__construct()`.

If you want to create your own classes / interfaces / enums, ... please create them in `inc` with `Classname.class.php` or `IMyInterface.interface.php`. The `inc` folder hiearchy represents the namespace prefix. For example you create a class in `inc/my/package/Class.class.php` and your generated namespace prefix is `Company\Plugin` the full name for the class should be `Company\Plugin\my\package\Class`.

## Add external PHP library

PHP libraries should be installed via [**Composer**](https://getcomposer.org/). If you want to use a PHP library in the plugin's production build (`dist`) then install the dependency as non-dev dependency. The `dist` build does not contain any dev dependencies.

When the composer dependency supports autoloading you do not have to worry about including. The boilerplate already includes the vendor autoload if exists.

## Add external JavaScript library

When using external libraries or React components it is recommended to avoid bundling it with the plugin's source code (webpack). Because the WordPress community offers a lot of plugins you should enqueue the library files using the provided `base\AssetsBase` (which is based on `wp_enqueue_script`/`wp_enqueue_style`) to **avoid duplicate JavaScript assets**.

In this example we want to use this NPM package in our WordPress plugin: https://www.npmjs.com/package/jquery-tooltipster. It is a simple tooltip plugin for jQuery.

1. Run `npm install jquery-tooltipster --save-dev` to install the npm module.
2. Add the library name to the `Gruntfile.js` so it looks like this:

```js
clean: {
    /**
     * Task to clean the already copied node modules to the public library folder
     */
    npmLibs: [
        'public/lib/jquery-tooltipster/'
    ]
},
copy: {
    /**
     * Task to copy npm modules to the public library folder.
     */
    npmLibs: {
        expand: true,
        cwd: 'node_modules',
        src: [
            'jquery-tooltipster/js/*.js',
            'jquery-tooltipster/css/*.css'
        ],
        dest: 'public/lib/'
    }
}
```

**Note:** The `src` for your npm module can be different. You must have a look at the modules' folder tree.

3. Run the command `yarn grunt copy-npmLibs` to copy the library and generate the new cachebuster for the library files.
4. Go to `Assets.class.php` and enqueue the styles and scripts:

```php
// This must be before your ReactJS styles and scripts so it can be used in ReactJS
$this->enqueueLibraryScript('jquery-tooltipster', 'jquery-tooltipster/js/tooltipster.js');
$this->enqueueLibraryStyle('jquery-tooltipster', 'jquery-tooltipster/css/tooltipster.css');

// Add the dependencies to the ReactJS styles and scripts
$this->enqueueScript('wp-reactjs-starter', 'admin.js', array('react-dom', 'jquery-tooltipster'));
$this->enqueueStyle('wp-reactjs-starter', 'admin.css', array('jquery-tooltipster');
```

5. If you have a look at your browser network log you see that the plugin automatically appends the right module version to the resource URL.
6. If you want to use the library in your ReactJS coding simply add jQuery to the `webpack.config.js` file as external:

```js
externals: {
	'jquery': 'jQuery'
},
```

7. And this in your ReactJS file:

```js
import $ from "jquery";
```

8. Now you can use the `$.fn.tooltipster` functionality.

## Using the cachebuster

The class `AssetsBase` (`inc/general/AssetsBase.class.php`) provides a few scenarios of cachebusting enqueue (scripts and styles):

-   **Scenario 1 (NPM library)**: Add a dependency to `package.json` > Copy to `public/lib/{PACKAGE_NAME}` (using Grunt) > Use `AssetsBase::enqueueLibraryScript()` to enqueue the handle `public/lib/{PACKAGE_NAME}/{FILE}.js` for example. The cachebuster is applied with the node module version. See [Add external JavaScript library](#add-external-javascript-library) for more.
-   **Scenario 2 (Dist and Dev)**: While developing the `public/src` is automatically transformed to production / dev code. Use `AssetsBase::enqueueScript()` to enqueue the handle `public/dev/admin.js` for example. The cachebuster is applied with a hash.
-   **Scenario 3 (Unknown library)**: Imagine you want to use a JavaScript library which is not installable through npm. > Use `AssetsBase::enqueLibraryScript()` (or [wp_enqueue_script](https://developer.wordpress.org/reference/functions/wp_enqueue_script/)) to enqueue the handle `public/lib/myprivatelib/file.js` for example. The cachebuster is applied with the plugin version.

## WP REST API v2

The boilerplate needs a minimum WordPress version of **4.4**. The **WP REST API v2** is implemented in WordPress core in **4.7**. The user gets an admin notice if < 4.4 to update WordPress core. If the user is > 4.4 and < 4.7 there is an admin notice with a link to download the [WP REST API](https://wordpress.org/plugins/rest-api/) plugin. The boilerplate adds a localization key to provide the REST API url to JavaScript:

```xml
<Notice type="info">The WP REST API URL of the plugin is: "{window.wprjssOpts.restUrl}" (localized variable)</Notice>
```

**Note:** Using the WP REST API v2 is essential for high quality plugins, please avoid using `admin-ajax.php`.

## JavaScript state management

The starter plugin uses the following packages to provide a easy-to use **Model Driven State Management**:

-   [**mobx@4**](https://github.com/mobxjs/mobx): This dependency is needed for mobx-state-tree to work. It uses Version 4 because v5 is for browsers with Proxies supported. Not all browsers (e. g. IE) does not support this kind of functionality.
-   [**mobx-state-tree**](https://github.com/mobxjs/mobx-state-tree): The model driven state management built on top of mobx.
-   [**mobx-react**](https://github.com/mobxjs/mobx-react): Easily create bindings for your React components for your models

## Localization

The boilerplate comes with an automatically created `languages/gyour-plugin-name.pot` file. If you are familar with the [`__()`](https://developer.wordpress.org/reference/functions/__/) translation functions you can use the constant `YOURCONSTANTPREFIX_TD` (see [Available constants](#available-constants)) as the `$domain` parameter.

To translate the plugin you can use for example a tool like [Poedit](https://poedit.net/) or [Loco Translate](https://wordpress.org/plugins/loco-translate/).

For frontend localization (`i18n-calypso`)[https://github.com/Automattic/wp-calypso/tree/master/packages/i18n-calypso] is used which is very easy to understand and works with i18n keys instead of direct translations in your TSX files. This module also allows you interpolating components to your translations so you can translate texts with links for example. You have to use the `inc/general/JsI18n.class.php` file to maintain your translation keys - then you can use them as follow in your code (complex example from `public/src/component-library/index.tsx`):

```tsx
import { translate } from "../util/i18n";

const NoticeExample = (
    <Notice type={ENoticeType.Info}>
        {translate("restUrlNotice", {
            args: { restUrl: pluginOptions.restUrl },
            components: {
                a: (
                    <a href="#" onClick={doTestAjaxCall}>
                        {pluginOptions.restUrl}
                    </a>
                )
            }
        })}
    </Notice>
);
```

## Building production plugin

Before publishing a new version you should run `yarn release`. It is a wrapper to [`yarn version`](https://yarnpkg.com/lang/en/docs/cli/version/) and you should always use that command instead the original one. The reason is that the boilerplate implementation also adjusts the `index.php` file. The release-command does not create a Git tag, you have to create it manually.

Afterwards simply run `yarn serve` and a folder `dist` gets created with a subfolder of the installable plugin and an installable zip file. It is recommenend to use CI / CD to publish the new version to wordpress.org or other marketplaces. An instroduction how to do this can be read below.

**Note:** Do not forget to adjust the `README.md` to your plugin description.

## :construction_worker: Todo

Nothing.

## Licensing / Credits

This boilerplate is MIT licensed. Originally this boilerplate is a fork of [gcorne/wp-react-boilerplate](https://github.com/gcorne/wp-react-boilerplate).
