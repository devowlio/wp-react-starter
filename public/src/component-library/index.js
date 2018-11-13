import React from 'react';
import Notice from 'wp/notice';
import Todo from './Todo';
import TodoStore from '../store';
import { Provider } from 'mobx-react';
require('./style.scss');

// Craete default store
const todoStore = TodoStore.create();

// Note: window.wprjssOpts can also be registered as external in webpack.config.js.
export default () => {
	return (
		<div className="wp-styleguide">
			<h1>WP React Component Library</h1>
			
			<Notice type="info">The text domain of the plugin is: "{ window.wprjssOpts.textDomain }" (localized variable)</Notice>
			<Notice type="info">The WP REST API URL of the plugin is: <a href={ window.wprjssOpts.restUrl + 'plugin' } target="_blank">{window.wprjssOpts.restUrl}</a> (localized variable)</Notice>
			<Notice type="info">The is an informative notice</Notice>
			<Notice type="success">Your action was successful</Notice>
			<Notice type="error">An unexpected error has occurred</Notice>
			
			{ /* @see https://mobx.js.org/refguide/observer-component.html#connect-components-to-provided-stores-using-inject */ }
			<Provider store={ todoStore }>
				<Todo />
			</Provider>
		</div>
	);
};
