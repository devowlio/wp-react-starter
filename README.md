<h1><p align="center">WordPress ReactJS Boilerplate :sparkling_heart:</p></h1>
<p align="center">This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin.</p>

---

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
* [**Namespace**](http://php.net/manual/en/language.namespaces.rationale.php) support
* [**Autloading**](http://php.net/manual/en/language.oop5.autoload.php) classes in connection with namespaces
* [`SCRIPT_DEBUG`](https://codex.wordpress.org/Debugging_in_WordPress#SCRIPT_DEBUG) enables not-minified sources for debug sources (use in connection with `npm run build-dev`)
* Predefined `.po` files for **translating (i18n)** the plugin

## :mountain_bicyclist: Getting Started

Make sure that you have [**Node.js**](https://nodejs.org/en/)/[**Grunt**](https://gruntjs.com/getting-started) installed and `SCRIPT_DEBUG` set `true` (only development sources are built). Navigate to the plugin directory, install `npm` dependencies, and run the dev build command:

```sh
$ cd /path/to/wordpress/wp-content/plugins
$ git clone https://github.com/matzeeeeeable/wp-reactjs-starter.git ./your-plugin-name
$ cd your-plugin-name
$ npm install # Install dependencies
$ npm run generate # Make the plugin yours and set plugin information
$ npm run build # Generate production versions of static assets
$ npm run dev # Start webpack in "watch" mode so that the assets are automatically compiled when a file changes
$ # >> You are now able to activate the plugin in your WordPress backend
```

## :book: Documentation

1. [Folder structure](#folder-structure)
1. [Available commands](#available-commands)
1. [Make the boilerplate yours](#make-the-boilerplate-yours)
1. Available constants
1. [Activation hooks](#activation-hooks)
1. Hooks and own classes
1. Add external JS library (with Grunt)
1. [Localization](#localization)
1. [Building production plugin](#building-production-plugin)

## Folder structure
* **`build`**: Build relevant files
* **`inc`**: All server-side files (PHP)
    * **`general`**: General files
    * **`others`**: Other files (for example the starter file)
* **`languages`**: Language files
* **`public`**: All client-side files (JavaScript, CSS)
    * **`lib`**: Put external libraries to this folder
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
$ npm run build
```
Create production build. Files get generated in `public/dist`. This files should get loaded when `SCRIPT_DEBUG` is active. Learn more here: [Building production plugin](#building-production-plugin)

```sh
$ npm run build-dev
```
Create development build. Files get generated in `public/dev`. This files should get loaded when `SCRIPT_DEBUG` is not active.

```sh
$ npm run dev
```
Starts to watch the `public/src` folder for file changes and automatically runs the `build-dev` script.

```sh
$ npm run lint
```
Prints out errors and warning about coding styles.

```sh
$ npm run generate
```
Starts to make the boilerplate yours and fit to your plugin name. Learn more here: [Make the boilerplate yours](#make-the-boilerplate-yours)

## Make the boilerplate yours
This boilerplate plugin allows you with a simple CLI command to make it yours. _Make it yours?! Sounds crazy._ Yes, it means it can automatically change the **constant names** (PHP), **namespace** prefix (PHP) and the language **`.pot`** filename.

All the magic is done by the command `npm run generate`. It will ask you a few plugin details in the CLI prompt and fully automatically generates the files for you. When the generator is finished just have a look at the `index.php` file.

## Activation hooks
There are four types of activation hooks:
* **Activate**: This hook / code gets executed even the plugin gets activated in the WordPress backend. You can implement your code in `inc/general/Activator.class.php::activate()`.
* **Deactivate**: This hook / code gets executed even the plugin gets deactivated in the WordPress backend. You can implement your code in `inc/general/Activator.class.php::deactivate()`.
* **Install**: This hook / code gets executed when the plugin versions changes. That means every update of the plugin executes this code - also the initial plugin activation. Usually you should implement your database table creation with [`dbDelta`](https://developer.wordpress.org/reference/functions/dbdelta/) here. You can implement your code in `inc/general/Activator.class.php::install()`.
* **Uninstall**: This hook / code gets executed even the plugin gets uninstalled in the WordPress backend. You can implement your code in `uninstall.php`.

## Localization
The boilerplate comes with an automatically created `languages/gyour-plugin-name.pot` file. If you are familar with the [``__()``](https://developer.wordpress.org/reference/functions/__/) translation functions you can use the constant `YOURCONSTANTPREFIX_TD` (see `index.php` for constants) as the `$domain` parameter.

To translate the plugin you can use for example a tool like [Poedit](https://poedit.net/) or [Loco Translate](https://wordpress.org/plugins/loco-translate/).

## Building production plugin
To build production JS and CSS code you simply run `npm run build`. More coming soon to prepare plugin for wordpress.org (serve).

## :construction_worker: Todo

1. Make widget src runnable
1. Make wordpress.org compatible (readme, banners, etc.)
1. Add Babel polyfill
1. Add documentation generation
1. Add redux
1. Add hot loading of both React components and css

## Licensing / Credits
This boilerplate is MIT licensed. Originally this boilerplate is a fork of [gcorne/wp-react-boilerplate](https://github.com/gcorne/wp-react-boilerplate).