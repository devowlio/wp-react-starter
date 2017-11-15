<?php
namespace MatthiasWeb\WPRJSS\menu;
use MatthiasWeb\WPRJSS\base;
use MatthiasWeb\WPRJSS\general;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

/**
 * Creates a WordPress backend menu page and demontrates a React component (public/src/admin.js).
 */
class Page extends base\Base {
    public function admin_menu() {
        $pluginName = general\Core::getInstance()->getPluginData()['Name'];
        add_menu_page(
            $pluginName,
            $pluginName,
            'manage_options',
            'wp-react-component-library',
            array($this, 'render_component_library')
		);
    }
    
    public function render_component_library() {
		echo '<div id="wp-react-component-library" class="wrap"></div>';
	}
}