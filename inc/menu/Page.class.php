<?php
namespace MatthiasWeb\WPRJSS\menu;
use MatthiasWeb\WPRJSS\base;
use MatthiasWeb\WPRJSS\general;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Creates a WordPress backend menu page and demontrates a React component (public/src/admin.js).
 */
class Page extends base\Base {
    const COMPONENT_ID = WPRJSS_SLUG . '-component';

    public function admin_menu() {
        $pluginName = general\Core::getInstance()->getPluginData()['Name'];
        add_menu_page($pluginName, $pluginName, 'manage_options', self::COMPONENT_ID, [
            $this,
            'render_component_library'
        ]);
    }

    public function render_component_library() {
        echo '<div id="' . self::COMPONENT_ID . '" class="wrap"></div>';
    }
}
