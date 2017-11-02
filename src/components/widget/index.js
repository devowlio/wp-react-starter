import React from 'react';

require( './style.scss' );

export default React.createClass({

	displayName: 'Widget',

	render() {
		return (
			<div className="react-boilerplate-widget">
				<h3>Hello, World!</h3>
				<p>I made this thing.</p>
			</div>
		);
	}
});
