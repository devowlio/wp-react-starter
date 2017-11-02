<?php

/**
 * Simple widget that creates an HTML element into which React renders
 */
class React_Demo_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'description' => __( 'A widget that demonstrates using React.')
		);
		parent::__construct( 'react-demo', __('React Demo'), $widget_ops);
	}

	function widget( $args, $instance ) {
		echo $args['before_widget'];
		?>
			<div class="react-demo-wrapper"></div>
		<?php
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {

	}

	function form( $instance ) {

	}

}
