Writing Code
============

The WP React Boilerplate proposes an alternative approach to writing complex UIs in WordPress. The idea is that the approach makes it easier for developers to create a great experience for users. At this stage, the idea is little more than a seed that will need lots of water, work, and time to mature.

It takes a modern approach to writing and organizing JavaScript that utilizes `npm` and Node.js tools to generate a script from a series modules (files). The modules are written using [ES2015](http://www.ecma-international.org/ecma-262/6.0/) (ES6) syntax which is the next version of JavaScript that was standardized in 2015. Because browsers only have implemented some of the specification and in order to use JSX, WP React Boilerplate uses Webpack and Babel to transform the `src` files into a single script in the `build` directory. This is a workflow that may be familiar to you. For example, WordPress uses [grunt](http://gruntjs.com/) and [browserify](http://browserify.org) to compile the CommonJS JavaScript modules for the media codebase into a series of scripts.

## Compiling the script

There are two different commands for compiling the script.

When developing, run `npm run build-dev` from somewhere in the plugin directory. This command will watch the files in the `src` directory and then automatically recompile when a file changes.

When creating a release, use the `npm run build` command. This command removes some of the debugging and performance tracking code from React and also minifies the file, which reduces the file size of the scripts.

## Modules

The basic JavaScript building block is the module. A module is a single file that includes `import` statements that import dependencies and `export` statements that specify the functions and objects that should be available when the module is imported elsewhere. The first time that a module is imported the code is executed, so if you're coming from PHP it is sort of like `require_once` but with one major difference—code in a module is not in the global scope. This avoids the issues with pollution of the global namespace and encourages folks writing a module to define a clear interface for developers that are using a module.

Here is an example:


src/components/message/index.js
```js
// bring in React as a dependency
import React from 'react';

// Export React component class.
// React.createClass() is called when this module is imported
export default React.createClass({

	displayName: 'Message',

	render() {
		return (
			<h3>{ this.props.text }</h3>
		);
	}

});
```

src/components/dashboard/index.js
```js
import React from 'react';

// Import the message component that we created above
import Message from 'components/message';

// Export React component class.
export default React.createClass({

	displayName: 'Dashboard',

	render() {
		return (
			<div>
				<Message text="Hello, there" />
			</div>
		);
	}

});
```

You can also use modules from `npm`. There is a growing ecosystem of React components and JavaScript utilities that work in the browser and have been published to `npm`. Webpack makes it easy import them as dependencies. Let's say that we want to use [React Select](http://jedwatson.github.io/react-select/).

First, we need to install the dependency.

```
$ cd /path/to/wordpress/wp-content/plugins/my-plugin
$ npm install react-select --save
```

Installing the dependency adds the code to `node_modules` and `--save` includes the dependency to `package.json`.

Once the dependency is installed, we can use it in our project.

src/components/dashboard/index.js
```js
import React from 'react';
import Message from 'components/message';

// Importing an external module works the same as importing an external module
import Select from 'react-select';

// Export React component class.
export default React.createClass({

	displayName: 'Dashboard',

	onChange( val ) {
		console.log( val );
	},

	render() {
		const options = [
			{ value: 'one', label: 'One' },
			{ value: 'two', label: 'Two' }
		];

		return (
			<div>
				<Message text="Hello, there" />
				<label>Select a number:</label>
				<Select options={ options } value="one" onChange={ this.onChange } />
			</div>
		);
	}

});
```

## Writing components

Coming soon…


## Handling data

Coming soon…

## Embedding a Single Page App (SPA)

Coming soon…

## Resources

The Calypso project has [compiled](https://github.com/Automattic/wp-calypso/blob/master/docs/guide/tech-behind-calypso.md) a solid set of resources for learning how to write and organize JavaScript in ways that take advantage of the full capabilities that are available to us today. Taking some time to explore them and to consider "why" the techniques make life better for both developers and users.

If you're a developer that has written jQuery, [React.js Introduction For People Who Know Just Enough jQuery To Get By](http://reactfordesigners.com/labs/reactjs-introduction-for-people-who-know-just-enough-jquery-to-get-by/) might be a good starting point.


