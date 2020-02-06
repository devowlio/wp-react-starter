<?php
namespace MatthiasWeb\WPRJSS\view\menu;
use MatthiasWeb\WPRJSS\base\UtilsProvider;

// @codeCoverageIgnoreStart
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request
// @codeCoverageIgnoreEnd

/**
 * Creates a WordPress backend menu page and demontrates a React component (public/ts/admin.tsx).
 *
 * @codeCoverageIgnore Example implementations gets deleted the most time after plugin creation!
 */
class Page {
    use UtilsProvider;

    const COMPONENT_ID = WPRJSS_SLUG . '-component';

    /**
     * C'tor.
     */
    private function __construct() {
        // Silence is golden.
    }

    /**
     * Add new menu page.
     */
    public function admin_menu() {
        $pluginName = $this->getCore()->getPluginData()['Name'];
        add_menu_page($pluginName, $pluginName, 'manage_options', self::COMPONENT_ID, [
            $this,
            'render_component_library'
        ]);
    }

    /**
     * Render the content of the menu page.
     */
    public function render_component_library() {
        echo '<div id="' . self::COMPONENT_ID . '" class="wrap"></div>';
    }

    /**
     * New instance.
     */
    public static function instance() {
        return new Page();
    }
}
