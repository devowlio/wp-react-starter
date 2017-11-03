import React from 'react';
import ReactDOM from 'react-dom';
import Widget from './components/widget';

// Query DOM for all widget wrapper divs
let widgets = document.querySelectorAll( 'div.react-demo-wrapper' );
widgets = Array.prototype.slice.call( widgets );

// Iterate over the DOM nodes and render a React component into each node
widgets.forEach( function( wrapper ) {
	ReactDOM.render(
		<Widget />,
		wrapper
	);
} );
