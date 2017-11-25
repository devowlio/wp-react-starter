<h1><p align="center">WordPress ReactJS Boilerplate :sparkling_heart:</p></h1>
<p align="center">This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin (Frontend Widget, WordPress backend menu page).</p>

---

[![GitHub tag](https://img.shields.io/github/tag/matzeeable/wp-reactjs-starter.svg?colorB=green)](https://github.com/matzeeable/wp-reactjs-starter) 
[![license](https://img.shields.io/github/license/matzeeable/wp-reactjs-starter.svg?colorB=green)](https://github.com/matzeeable/wp-reactjs-starter/blob/master/LICENSE) 
[![Slack channel](https://img.shields.io/badge/Slack-join-green.svg)](https://matthiasweb.signup.team/)

**Client-side features:** _Familiar React API & patterns (ES6)_
* [**ReactJS**](https://reactjs.org/) v16 with babel `env` preset
* [**webpack**](https://webpack.js.org/) v3 build for assets
* CSS and JS [**Sourcemap**](https://www.html5rocks.com/en/tutorials/developertools/sourcemaps/) generation for debugging purposes
* [**SASS**](http://sass-lang.com/) stylesheets compiler (`.scss` files)
* [**Bourbon**](http://bourbon.io/) mixins for SASS
* [**PostCSS**](http://postcss.org/) for transforming CSS with JavaScript (including autoprefixing)
* Generation of **minified** sources for production (JS, CSS)
* [**Grunt**](https://gruntjs.com/) for automation tasks
* Admin backend components, in this case an own page with a button (`public/src/admin.js`)
* Frontend components, in this case a simple widget (`public/src/widget.js`)

**Server-side features:** _OOP-style for building a high-quality plugin._
* PHP >= **5.3** required: An admin notice is showed when not available
* WordPress >= **4.4** required: An admin notice is showed when not available with a link to the updater
* [**Namespace**](http://php.net/manual/en/language.namespaces.rationale.php) support
* [**Autloading**](http://php.net/manual/en/language.oop5.autoload.php) classes in connection with namespaces
* [**WP REST API v2**](http://v2.wp-api.org/) for API programming, no longer use `admin-ajax.php` for your CRUD operations
* [`SCRIPT_DEBUG`](https://codex.wordpress.org/Debugging_in_WordPress#SCRIPT_DEBUG) enables not-minified sources for debug sources (use in connection with `npm run build-dev`)
* [**Cachebuster**](http://www.adopsinsider.com/ad-ops-basics/what-is-a-cache-buster-and-how-does-it-work/) for public resources (`public`)
* Predefined `.po` files for **translating (i18n)** the plugin
* [**ApiGen**](https://github.com/ApiGen/ApiGen) for PHP Documentation
* [**JSDoc**](http://usejsdoc.org/) for JavaScript Documentation
* [**apiDoc**](http://apidocjs.com//) for API Documentation
* [**WP HookDoc**](https://github.com/matzeeable/wp-hookdoc) for Filters & Actions Documentation

## :white_check_mark: Prerequesits
* [**Node.js**](https://nodejs.org/) `npm` command globally available in CLI
* [**Grunt CLI**](https://gruntjs.com/using-the-cli) `grunt` command globally available in CLI
* [**Composer**](https://getcomposer.org/) `composer` command globally available in CLI

## :mountain_bicyclist: Getting Started

Navigate to the plugin directory, install `npm` and `composer` dependencies, and run this installation script:

#### Download boilerplate
```sh
$ cd /path/to/wordpress/wp-content/plugins
$ git clone https://github.com/matzeeable/wp-reactjs-starter.git ./your-plugin-name
$ cd your-plugin-name
```

#### Create plugin
```sh
$ npm run create    # Guide through plugin generation
$ npm run dev       # Start webpack in "watch" mode so that the assets are automatically compiled when a file changes
                    # You are now able to activate the plugin in your WordPress backend
```

#### Generate CLI preview (npm run create)
![generate cli](https://image.prntscr.com/image/z61WDD8RQ3GJ3Bp4pZ-ElQ.png)

## :book: Documentation

1. [Folder structure](#folder-structure)
1. [Available commands](#available-commands)
1. [Make the boilerplate yours](#make-the-boilerplate-yours)
1. [Available constants](#available-constants)
1. [Activation hooks](#activation-hooks)
1. [Add hooks and own classes](#add-hooks-and-own-classes)
1. [Add external PHP library](#add-external-php-library)
1. [Add external JavaScript library](#add-external-javascript-library)
1. [Using the cachebuster](#using-the-cachebuster)
1. [WP REST API v2](#wp-rest-api-v2)
1. [Localization](#localization)
1. [Building production plugin](#building-production-plugin)

## Folder structure
* **`build`**: Build relevant files and predefined grunt tasks
* **`dist`**: The production plugin, see [Building production plugin](#building-production-plugin)
* **`docs`**: Auto generated docs (for example for PHP, JS and API Doc), see [Available commands](#available-commands)
* **`inc`**: All server-side files (PHP)
    * **`base`**: Abstract base classes
    * **`general`**: General files for the plugin
    * **`menu`**: Example page (backend)
    * **`others`**: Other files (cachebusters, ...)
    * **`rest`**: Example REST API service, see [WP REST API v2](#wp-rest-api-v2)
    * **`widget`**: Example widget
* **`languages`**: Language files
* **`public`**: All client-side files (JavaScript, CSS)
    * **`lib`**: Put external libraries to this folder (cachebuster is only available for copied node modules, see [Add external JavaScript library](#add-external-javascript-library))
    * **`src`**: Your source files (see client-side features what's possible)
    * **`dev`**: Generated development sources (`SCRIPT_DEBUG` is active)
    * **`dist`**: Generated production sources (`SCRIPT_DEBUG` is not active)
* **`index.php`**: The plugins index file. The entry point for your plugin.
* **`uninstall.php`**: When uninstalling your plugin this file gets called
* _`.babelrc`_: [Babel configuration](https://babeljs.io/docs/usage/babelrc/)
* _`.editorconfig`_: [Editor configuration](http://editorconfig.org/)
* _`.eslintrc`_: [ESLint configuration](https://eslint.org/docs/user-guide/configuring)
* _`Gruntfile.js`_: [Grunt automation file](https://gruntjs.com/sample-gruntfile)
* _`package.json`_: [NPM package configuration](https://docs.npmjs.com/files/package.json)
* _`postcss.config.js`_: [PostCSS configuration](https://github.com/postcss/postcss-loader#configuration)
* _`webpack.config.js`_: [webpack configuration](https://webpack.github.io/docs/configuration.html)

## Available commands
```sh
$ npm run create
```
Starts to make the boilerplate yours and fit to your plugin name. Learn more here: [Make the boilerplate yours](#make-the-boilerplate-yours).

```sh
$ npm run build
```
Create production build of ReactJS files. The files gets generated in `public/dist`. This files should be loaded when `SCRIPT_DEBUG` is not active. Learn more here: [Building production plugin](#building-production-plugin)

```sh
$ npm run build-dev
```
Create development build of ReactJS files. The files gets generated in `public/dev`. This files should be loaded when `SCRIPT_DEBUG` is active.

```sh
$ npm run dev
```
Starts to watch the `public/src` folder for file changes and automatically runs the `build-dev` script. Additionally the npm script `webpack-build-done` is executed after each webpack build.

```sh
$ npm run lint
```
Prints out errors and warning about coding styles.

```sh
$ npm run phpdocs
```
Generate PHP docs in `docs/php` of `inc`.

```sh
$ npm run jsdocs
```
Generate JS docs in `docs/js` of `public/src`.

```sh
$ npm run apidocs
```
Generate API docs in `docs/api` of `inc`.

```sh
$ npm run hookdocs
```
Generate Actions and Filters docs in `docs/hooks` of `inc`.

```sh
$ npm run docs
```
Generates all docs at once.

```sh
$ grunt public-cachebuster
```
Starts to generate the cachebuster files `inc/others/cachebuster.php` (including `public/dist` and `public/dev` hashes) and `inc/others/cachebuster-lib.php` (including `public/lib`). **Note**: Each build with webpack triggers a cachebuster generation.

```sh
$ grunt copy-npmLibs
```
Copies the defined public libraries in `Gruntfile.js` to the public/lib folder. See [Add external JavaScript library](#add-external-javascript-library).

```sh
$ npm run serve
```
Bundles all the plugin files together and puts it into the `dist` folder. This folder can be pushed to the wordpress.org SVN. See [Building production plugin](#building-production-plugin).

## Make the boilerplate yours
This boilerplate plugin allows you with a simple CLI command to make it yours. _Make it yours?! Sounds crazy._ Yes, it means it can automatically change the **constant names** (PHP), **namespace** prefix (PHP) and the language **`.pot`** filename.

All the magic is done by the command `npm run generate`. It will ask you a few plugin details in the CLI prompt and fully automatically generates the files for you. When the generator is finished just have a look at the `index.php` file.

## Available constants
After generating your boilerplate you should have a look in the generated `index.php` file. There are several PHP constants available for your plugin coding:
* `YOURCONSTANTPREFIX_FILE`: The plugin file (`__FILE__`)
* `YOURCONSTANTPREFIX_PATH`: The plugins path
* `YOURCONSTANTPREFIX_INC`: The plugins `inc` folder with trailing slash
* `YOURCONSTANTPREFIX_MIN_PHP`: The minimum PHP version
* `YOURCONSTANTPREFIX_NS`: The namespace for your plugin
* `YOURCONSTANTPREFIX_DB_PREFIX`: The `Base.class.php` offers a method `getTableName()` which returns a valid table name for your plugin
* `YOURCONSTANTPREFIX_OPT_PREFIX`: If you want to save options you should use this constant for option names prefix
* `YOURCONSTANTPREFIX_TD`: The text domain for your plugin. See [Localization](#localization)
* `YOURCONSTANTPREFIX_VERSION`: The version of the plugin
* `YOURCONSTANTPREFIX_DEBUG`: If true the `Base.class.php::debug()` method writes to the error log

## Activation hooks
There are four types of activation hooks:
* **Activate**: This hook / code gets executed even the plugin gets activated in the WordPress backend. You can implement your code in `inc/general/Activator.class.php::activate()`.
* **Deactivate**: This hook / code gets executed even the plugin gets deactivated in the WordPress backend. You can implement your code in `inc/general/Activator.class.php::deactivate()`.
* **Install**: This hook / code gets executed when the plugin versions changes. That means every update of the plugin executes this code - also the initial plugin activation. Usually you should implement your database table creation with [`dbDelta`](https://developer.wordpress.org/reference/functions/dbdelta/) here. You can implement your code in `inc/general/Activator.class.php::install()`.
* **Uninstall**: This hook / code gets executed even the plugin gets uninstalled in the WordPress backend. You can implement your code in `uninstall.php`.

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

3. Run the command `grunt copy-npmLibs` to copy the library and generate the new cachebuster for the library files.
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
import $ from 'jquery';
```
8. Now you can use the `$.fn.tooltipster` functionality.

## Using the cachebuster
The class `AssetsBase` (`inc/general/AssetsBase.class.php`) provides a few scenarios of cachebusting enqueue (scripts and styles):

* **Scenario 1 (NPM library)**: Add a dependency to `package.json` > Copy to `public/lib/{PACKAGE_NAME}` (using Grunt) > Use `AssetsBase::enqueueLibraryScript()` to enqueue the handle `public/lib/{PACKAGE_NAME}/{FILE}.js` for example. The cachebuster is applied with the node module version. See [Add external JavaScript library](#add-external-javascript-library) for more.
* **Scenario 2 (Dist and Dev)**: While developing the `public/src` is automatically transformed to production / dev code. Use `AssetsBase::enqueueScript()` to enqueue the handle `public/dev/admin.js` for example. The cachebuster is applied with a hash.
* **Scenario 3 (Unknown library)**: Imagine you want to use a JavaScript library which is not installable through npm. > Use `AssetsBase::enqueLibraryScript()` (or [wp_enqueue_script](https://developer.wordpress.org/reference/functions/wp_enqueue_script/)) to enqueue the handle `public/lib/myprivatelib/file.js` for example. The cachebuster is applied with the plugin version.

## WP REST API v2
The boilerplate needs a minimum WordPress version of **4.4**. The **WP REST API v2** is implemented in WordPress core in **4.7**. The user gets an admin notice if < 4.4 to update WordPress core. If the user is > 4.4 and < 4.7 there is an admin notice with a link to download the [WP REST API](https://wordpress.org/plugins/rest-api/) plugin. The boilerplate adds a localization key to provide the REST API url to JavaScript:

```xml
<Notice type="info">The WP REST API URL of the plugin is: "{window.wprjssOpts.restUrl}" (localized variable)</Notice>
```

**Note:** Using the WP REST API v2 is essential for high quality plugins, please avoid using `admin-ajax.php`.

## Localization
The boilerplate comes with an automatically created `languages/gyour-plugin-name.pot` file. If you are familar with the [``__()``](https://developer.wordpress.org/reference/functions/__/) translation functions you can use the constant `YOURCONSTANTPREFIX_TD` (see [Available constants](#available-constants)) as the `$domain` parameter.

To translate the plugin you can use for example a tool like [Poedit](https://poedit.net/) or [Loco Translate](https://wordpress.org/plugins/loco-translate/).

In this boilerplate you can find an example of using a [`wp_localize_script`](https://developer.wordpress.org/reference/functions/wp_localize_script/)'ed object in React (`inc/menu/Page.class.php::enqueue_scripts()`, `public/src/component-library/index.js`):
```xml
<Notice type="info">The text domain of the plugin is: "{window.wprjssOpts.textDomain}" (localized variable)</Notice>
```

## Building production plugin
To build production JS and CSS code you simply run `npm run build`.

#### Building installable WordPress plugin (WordPress.org)
If you want to publish the plugin you have to change the version. The version is defined in the `index.php` header comment. You do not have to change the version constant. It is also recommended to use the `npm verison` command to set the new version.

After setting the new version and want to build an installable **wordpress.org** plugin you can run the command `npm run serve`. _What does this mean?_ This command creates all **plugin-only** folder and files, without `package.json`, `composer.json`, `public/src`, ... The command does exactly this:

1. Build production and development resources (JS, CSS) (`npm run build && npm run build-dev`)
1. Create library files in `public/lib` (`grunt copy-npmLibs`)
1. Generate cachebusters for the resources (JS, CSS) (`grunt public-cachebuster`)
1. Copy all plugin relevant files to `dist` (= `['composer.json', 'index.php', 'inc/**/*', 'public/**/*', 'LICENSE', 'README.md', 'languages/**/*']`)
1. Install composer dependencies (no-dev) for `dist`
1. Delete `composer.json` file
1. Finished

#### Using wordpress.org SVN repository together with the `serve` command
1. Create the `dist` folder manually
1. Initialize the wordpress.org SVN repository in `dist`
1. Change the `SERVE_DIR` in `Gruntfile.js` to the folder where you want to place the build files (example: `dist/trunk`)
1. wordpress.org needs a `README.txt` instead of `README.md`
1. Register the predefined rename task as post task for the `serve` command with: `SERVE_POST_TASKS: ['serveRenameReadme']` in `Gruntfile.js`
1. Run `npm run serve`

**Note:** Do not forget to adjust the `README.md` to your plugin description.

## :information_desk_person: Useful resources
1. [Chrome React Developer Tools](https://chrome.google.com/webstore/detail/react-developer-tools/fmkadmapgofadopljbjfkapdkoienihi)
1. [Redux State Management Concept](http://www.youhavetolearncomputers.com/blog/2015/9/15/a-conceptual-overview-of-redux-or-how-i-fell-in-love-with-a-javascript-state-container)
1. [Redux+React Provider and Connect explained](http://www.sohamkamani.com/blog/2017/03/31/react-redux-connect-explained/)
1. [Redux with API's](http://www.sohamkamani.com/blog/2016/06/05/redux-apis/)
1. [Install Babel's polyfills](https://babeljs.io/docs/usage/polyfill/)

## :construction_worker: Todo

Nothing.

## Licensing / Credits
This boilerplate is MIT licensed. Originally this boilerplate is a fork of [gcorne/wp-react-boilerplate](https://github.com/gcorne/wp-react-boilerplate).