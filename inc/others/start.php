<?php
// We have now ensured that we are running the minimum PHP version.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

// Check minimum WordPress version
global $wp_version;
if (version_compare($wp_version, WPRJSS_MIN_WP, '>=')) {
    $load_core = false;
    
    // Check minimum WordPress REST API
    if (version_compare($wp_version, '4.7.0', '>=')) {
        $load_core = true;
    }else{
        // Check WP REST API plugin is active
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $load_core = is_plugin_active('rest-api/plugin.php');
    }
    
    // Load core
    if ($load_core) {
        require_once(WPRJSS_INC . 'general/Core.class.php');
        call_user_func(array(WPRJSS_NS . '\\general\\Core', 'getInstance'));
    }else{
        // WP REST API version not reached
        require_once(WPRJSS_INC . 'others/fallback-rest-api.php');
    }
}else{
    // Min WP version not reached
    require_once(WPRJSS_INC . 'others/fallback-wp-version.php');
}
?>