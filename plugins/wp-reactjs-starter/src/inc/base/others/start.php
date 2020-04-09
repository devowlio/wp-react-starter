<?php
// We have now ensured that we are running the minimum PHP version.

use MatthiasWeb\WPRJSS\Core;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

// Check minimum WordPress version
global $wp_version;
if (version_compare($wp_version, WPRJSS_MIN_WP, '>=')) {
    $load_core = false;

    // Check minimum WordPress REST API
    if (version_compare($wp_version, '4.7.0', '>=')) {
        $load_core = true;
    } else {
        // Check WP REST API plugin is active
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $load_core = is_plugin_active('rest-api/plugin.php');
    }

    // Load core
    if ($load_core) {
        // Composer autoload
        $composer_path = path_join(WPRJSS_PATH, 'vendor/autoload.php');
        if (file_exists($composer_path)) {
            require_once $composer_path;
        }

        // Dependents scoper autoload (PHP Scoper)
        $depAutoloaders = glob(path_join(WPRJSS_PATH, 'vendor/*/*/vendor/scoper-autoload.php'));
        foreach ($depAutoloaders as $composer_path) {
            require_once $composer_path;
        }

        // Dependents autoload
        $depAutoloaders = glob(path_join(WPRJSS_PATH, 'vendor/' . WPRJSS_ROOT_SLUG . '/*/vendor/autoload.php'));
        foreach ($depAutoloaders as $composer_path) {
            require_once $composer_path;
        }

        Core::getInstance();
    } else {
        // WP REST API version not reached
        require_once WPRJSS_INC . 'base/others/fallback-rest-api.php';
    }
} else {
    // Min WP version not reached
    require_once WPRJSS_INC . 'base/others/fallback-wp-version.php';
}
