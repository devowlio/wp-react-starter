<p align="center"># :sparkling_heart: WordPress ReactJS Boilerplate

This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin.</p>

---

* [**ReactJS**](https://reactjs.org/) v16 with babel `env` preset
* [**webpack**](https://webpack.js.org/) v3 build for assets
* CSS and JS [**Sourcemap**](https://www.html5rocks.com/en/tutorials/developertools/sourcemaps/) generation for debugging purposes
* [**SASS**](http://sass-lang.com/) stylesheets compiler (`.scss` files)
* [**Bourbon**](http://bourbon.io/) mixins for SASS
* [**PostCSS**](http://postcss.org/) for transforming CSS with JavaScript (including autoprefixing)
* Generation of **minified** sources for production (JS, CSS)
* Admin backend components, in this case an own page with a button (`public/src/admin.js`)
* Frontend components, in this case a simple widget (`public/src/widget.js`)

## :book: Getting Started

Make sure that you have [**Node.js**](https://nodejs.org/en/) installed. Navigate to the plugin directory, install `npm` dependencies, and run the dev build command:

```sh
cd /path/to/wordpress/wp-content/plugins/wp-react-boilerplate
npm install # Install dependencies
npm run build # Generate production versions of static assets
npm run dev # Start webpack in "watch" mode so that the assets are automatically compiled when a file changes
```

## :mountain_bicyclist: Documentation

* [Writing Code](./docs/writing-code.md)

## Todo

1. Add PHP boilerplate files
1. Add Babel polyfill
1. Add `generate` script to generate a new plugin from the boilerplate
1. Add documentation generation
1. Add redux
1. Add script to create a build folder that holds only plugin relevant files (Grunt)
1. Add hot loading of both React components and css

## Licensing / Credits
This boilerplate is MIT licensed. Originally this boilerplate is a fork of [gcorne/wp-react-boilerplate](https://github.com/gcorne/wp-react-boilerplate).