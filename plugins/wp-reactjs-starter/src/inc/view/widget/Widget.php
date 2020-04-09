<?php
namespace MatthiasWeb\WPRJSS\view\widget;
use MatthiasWeb\WPRJSS\base\UtilsProvider;
use WP_Widget;

// @codeCoverageIgnoreStart
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request
// @codeCoverageIgnoreEnd

/**
 * Simple widget that creates an HTML element with React rendering.
 *
 * @codeCoverageIgnore Example implementations gets deleted the most time after plugin creation!
 */
class Widget extends WP_Widget {
    use UtilsProvider;

    /**
     * C'tor.
     */
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

    /**
     * Output the widget content.
     *
     * @param mixed $args
     * @param array $instance
     */
    public function widget($args, $instance) {
        echo $args['before_widget']; ?>
<div class="react-demo-wrapper"></div>
        <?php echo $args['after_widget'];
    }

    /**
     * Updates a particular instance of a widget.
     *
     * @param array $new_instance
     * @param array $old_instance
     */
    public function update($new_instance, $old_instance) {
        // Silence is golden.
    }

    /**
     * Outputs the settings update form.
     *
     * @param array $instance
     * @return string
     */
    public function form($instance) {
        return 'noform';
    }
}
