<h1><p align="center">WordPress ReactJS Boilerplate :sparkling_heart:</p></h1>
<p align="center">This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin.</p>

---

**Client side features:** __Familiar React API & patterns (ES6)__
* [**ReactJS**](https://reactjs.org/) v16 with babel `env` preset
* [**webpack**](https://webpack.js.org/) v3 build for assets
* CSS and JS [**Sourcemap**](https://www.html5rocks.com/en/tutorials/developertools/sourcemaps/) generation for debugging purposes
* [**SASS**](http://sass-lang.com/) stylesheets compiler (`.scss` files)
* [**Bourbon**](http://bourbon.io/) mixins for SASS
* [**PostCSS**](http://postcss.org/) for transforming CSS with JavaScript (including autoprefixing)
* Generation of **minified** sources for production (JS, CSS)
* Admin backend components, in this case an own page with a button (`public/src/admin.js`)
* Frontend components, in this case a simple widget (`public/src/widget.js`)

**Server side features:** __OOP-style for building a high-quality plugin.__
* PHP >= **5.3** required: An admin notice is showed when not available
* [**Namespaces**](http://php.net/manual/en/language.namespaces.rationale.php) support
* [**Autloading**](http://php.net/manual/en/language.oop5.autoload.php) classes in connection with namespaces
* [`WP_DEBUG`](https://codex.wordpress.org/WP_DEBUG) enables not-minified sources for debug sources (use in connection with `npm run build-dev`)
* Predefined `.po` files for **translating (i18n)** the plugin
* TODO Migration system

## :mountain_bicyclist: Getting Started

Make sure that you have [**Node.js**](https://nodejs.org/en/) installed. Navigate to the plugin directory, install `npm` dependencies, and run the dev build command:

```sh
cd /path/to/wordpress/wp-content/plugins/wp-react-boilerplate
npm install # Install dependencies
npm run build # Generate production versions of static assets
npm run dev # Start webpack in "watch" mode so that the assets are automatically compiled when a file changes
```

## :book: Documentation

* Coming soon...

## Todo

1. Make widget src runnable
1. Add PHP boilerplate files
1. Make wordpress.org compatible (readme, banners, etc.)
1. Add Babel polyfill
1. Add `generate` script to generate a new plugin from the boilerplate
1. Add documentation generation
1. Add redux
1. Add script to create a build folder that holds only plugin relevant files (Grunt) (must be own repo which clones and installs)
1. Add hot loading of both React components and css

## Licensing / Credits
This boilerplate is MIT licensed. Originally this boilerplate is a fork of [gcorne/wp-react-boilerplate](https://github.com/gcorne/wp-react-boilerplate).