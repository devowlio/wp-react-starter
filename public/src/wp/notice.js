import React from 'react';
import classNames from 'classnames';

export default ({ type, children }) => {
	const classes = classNames({
		notice: true,
		'notice-error': type === 'error',
		'notice-info': type === 'info',
		'notice-success': type === 'success',
	});

	return (
		<div className={ classes }>
			<p>{ children }</p>
		</div>
	);
};