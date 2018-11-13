import React from 'react';
import classNames from 'classnames';

export default ({ className, type, children, ...rest }) => {
	const _className = classNames( className, {
		'button-primary': type === 'primary',
		'button-secondary': type === 'secondary'
	} );
	return <button className={ _className } { ...rest }>{ children }</button>;
};