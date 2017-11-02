import React from 'react';
import AdminHeader from 'components/wp/admin-header';
import Button from 'components/wp/button';
import Notice from 'components/wp/notice';

require( './style.scss' );

export default React.createClass({

	render() {
		return (
				<div className="wp-styleguide">
					<AdminHeader>
						WP React Component Library
					</AdminHeader>

					<Notice type="info">The is an informative notice</Notice>
					<Notice type="success">Your action was successful</Notice>
					<Notice type="error">An unexpected error has occurred</Notice>

					<div className="wp-styleguide--buttons">
						<h2>Buttons</h2>
						<p>
							<Button type="primary">Primary Button</Button>
						</p>
						<p>
							<Button type="secondary">Secondary Button</Button>
						</p>
					</div>

				</div>
		);
	}

});
