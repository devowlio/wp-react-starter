<?php
namespace MatthiasWeb\WPRJSS\widget;
use MatthiasWeb\WPRJSS\base;
use MatthiasWeb\WPRJSS\general;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Simple widget that creates an HTML element into which React renders
 */
class Widget extends \WP_Widget {
    public function __construct() {
        $widget_ops = [
            // Just for fun _n() usage demonstrating prulars i18n
            'description' => _n(
                'A widget that demonstrates using React.',
                'Widgets that demonstrates using React.',
                1,
                WPRJSS_TD
            )
        ];
        parent::__construct(WPRJSS_TD . 'react-demo', 'React Demo Widget', $widget_ops);
    }

    public function widget($args, $instance) {
        echo $args['before_widget']; ?>
			<div class="react-demo-wrapper"></div>
		<?php echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance) {
        // Silence is golden.
    }

    public function form($instance) {
        // Silence is golden.
    }
}
