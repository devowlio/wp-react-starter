<?php
namespace MatthiasWeb\WPRJSS\menu;
use MatthiasWeb\WPRJSS\general;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

/**
 * Creates a WordPress backend menu page and demontrates a React component (public/src/admin.js).
 */
class Page extends general\Base {
    public function admin_menu() {
        add_menu_page(
            'WP React Component Library',
            'Component Library',
            'manage_options',
            'wp-react-component-library',
            array($this, 'render_component_library')
		);
    }
    
    public function render_component_library() {
		echo '<div id="wp-react-component-library" class="wrap"></div>';
	}
}