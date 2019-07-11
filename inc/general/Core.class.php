<?php
namespace MatthiasWeb\WPRJSS\general;
use MatthiasWeb\WPRJSS\base;
use MatthiasWeb\WPRJSS\menu;
use MatthiasWeb\WPRJSS\rest;
use MatthiasWeb\WPRJSS\widget;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

// Include files, where autoloading is not possible, yet
require_once WPRJSS_INC . 'base/Core.class.php';

/**
 * Singleton core class which handles the main system for plugin. It includes
 * registering of the autoloader, all hooks (actions & filters) (see base\Core class).
 */
class Core extends base\Core {
    /**
     * Singleton instance.
     */
    private static $me;

    /**
     * The main service.
     *
     * @see rest\Service
     */
    private $service;

    /**
     * Application core constructor.
     */
    protected function __construct() {
        parent::__construct();

        // Register all your before init hooks here
        add_action('plugins_loaded', [$this, 'updateDbCheck']);
        add_action('widgets_init', [$this, 'widgets_init']);
    }

    /**
     * The init function is fired even the init hook of WordPress. If possible
     * it should register all hooks to have them in one place.
     */
    public function init() {
        $this->service = new rest\Service();

        // Register all your hooks here
        add_action('rest_api_init', [$this->getService(), 'rest_api_init']);
        add_action('admin_enqueue_scripts', [$this->getAssets(), 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this->getAssets(), 'wp_enqueue_scripts']);
        add_action('admin_menu', [new menu\Page(), 'admin_menu']);

        add_filter('override_load_textdomain', [new Localization(), 'override_load_textdomain'], 10, 3);
        add_filter('load_script_translation_file', [new Localization(), 'load_script_translation_file'], 10, 3);
    }

    /**
     * Register widgets.
     */
    public function widgets_init() {
        register_widget(WPRJSS_NS . '\\widget\\Widget');
    }

    /**
     * Get the service.
     *
     * @returns rest\Service
     */
    public function getService() {
        return $this->service;
    }

    /**
     * Get singleton core class.
     *
     * @returns Core
     */
    public static function getInstance() {
        return !isset(self::$me) ? (self::$me = new Core()) : self::$me;
    }
}
