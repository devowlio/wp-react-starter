# WordPress ReactJS Boilerplate

This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin. 

## Features
* ReactJS v16 with babel `env` preset
* webpack v3 build for assets
* SASS stylesheets compiler (`.scss` files)
* Admin backend components, in this case a own page with a button (`public/src/admin.js`)
* Frontend backend components, in this case a simple widget (`public/src/widget.js`)

## Getting Started

1. Make sure that you have [Node.js](https://nodejs.org/en/) installed.
2. Navigate to the plugin directory, install `npm` dependencies, and run the dev build command:

```
cd /path/to/wordpress/wp-content/plugins/wp-react-boilerplate
npm install
npm run build
npm run dev
```

## Commands

* `npm install` - Install dependencies
* `npm run build` – Generate production versions of static assets.
* `npm run dev` - Start webpack in "watch" mode so that the assets are automatically compiled when a file changes.

## Documentation

* [Writing Code](./docs/writing-code.md)

## Todo

1. Add Bourbon.io
1. Add Babel polyfill
1. Add `generate` script to generate a new plugin from the boilerplate
1. Add documentation generation
1. Add redux
1. Add script to create a build folder that holds only plugin relevant files
1. Add hot loading of both React components and css

## Licensing / Credits
This boilerplate is MIT licensed. Originally this boilerplate is a fork of [gcorne/wp-react-boilerplate](https://github.com/gcorne/wp-react-boilerplate).