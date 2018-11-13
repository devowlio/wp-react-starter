import ReactDOM from 'react-dom';
import React from 'react';
import ComponentLibrary from 'component-library';
import 'setimmediate'; // Polyfill for yielding

let widget = document.getElementById( 'wp-react-component-library' );

if (widget) {
	ReactDOM.render(
		<ComponentLibrary />,
		widget
	);
}