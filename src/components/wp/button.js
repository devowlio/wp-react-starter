import React from 'react';
import classNames from 'classnames';

export default function( props ) {
	const className = classNames( props.className, {
		'button-primary': props.type === 'primary',
		'button-secondary': props.type === 'secondary'
	} );
	return <button className={ className }>{ props.children }</button>;
}
