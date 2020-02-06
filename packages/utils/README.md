# `@wp-reactjs-multi-starter/utils`

Some utility functionality for your WordPress plugins.

## TypeScript

TypeScript coding should be placed in `lib`. It is not compiled from TS to ES6 code because it is directly consumed through the `babel-loader` in your webpack configuration. If you want to add functionality to this module be sure that this is needed for all your plugins - otherwise put it directly to the plugin.

## PHP

PHP coding should be placed in `src`.
