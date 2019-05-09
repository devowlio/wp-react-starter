<?php
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

if (!function_exists('wprjss_skip_php_admin_notice')) {
    /**
     * Show an admin notice to administrators when the minimum PHP version
     * could not be reached. The error message is only in english available.
     */
    function wprjss_skip_php_admin_notice() {
        if (current_user_can('install_plugins')) {
            extract(get_plugin_data(WPRJSS_FILE, true, false));
            echo '<div class=\'notice notice-error\'>
				<p><strong>' .
                $Name .
                '</strong> could not be initialized because you need minimum PHP version ' .
                WPRJSS_MIN_PHP .
                ' ... you are running: ' .
                phpversion() .
                '.
			</div>';
        }
    }
}
add_action('admin_notices', 'wprjss_skip_php_admin_notice');
