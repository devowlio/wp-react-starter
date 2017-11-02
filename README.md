# WordPress ReactJS Boilerplate

This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin. It is forked from [gcorne/wp-react-boilerplate](https://github.com/gcorne/wp-react-boilerplate) and adjusted to MatthiasWeb's plugins.

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

1. Add `generate` script to generate a new plugin from the boilerplate
1. Add code for bootstrapping data via a JSON object written into a script tag
1. Add example that integrates the REST API
1. Add redux and react-router
1. Add admin components that use classes that match up with the classes used in WP Admin
1. Incorporate the webpack-dev-server so that when developing the browser will wait while the assets are being regenerated
1. Add script for publishing plugin to wordpress.org
1. Add hot loading of both React components and css

## Licensing / Credits
This boilerplate is MIT licensed. Originally this boilerplate is a fork of [gcorne/wp-react-boilerplate](https://github.com/gcorne/wp-react-boilerplate).