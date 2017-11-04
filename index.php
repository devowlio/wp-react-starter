<?php
/**
 * @wordpress-plugin
 * Plugin Name: 	WP ReactJS Starter
 * Plugin URI:		https://matthias-web.com/wordpress
 * Description: 	This WordPress plugin demonstrates how to setup a plugin that uses React and ES6 in a WordPress plugin.
 * Author:			Matthias Guenter
 * Version: 		0.1.0
 * Author URI:		https://matthias-web.com
 * Text Domain:		wp-reactjs-starter
 * Domain Path:		/languages
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

/**
 * Plugin constants. This file is procedural coding style for initialization of
 * the plugin core and definition of plugin configuration.
 */
if (defined('WPRJSS_PATH')) return;
define('WPRJSS_FILE',		__FILE__);
define('WPRJSS_PATH',		dirname(WPRJSS_FILE));
define('WPRJSS_INC',		trailingslashit(path_join(WPRJSS_PATH, 'inc')));
define('WPRJSS_MIN_PHP',	'5.3.0'); // Minimum of PHP 5.3 required for autoloading and namespacing
define('WPRJSS_NS',			'MatthiasWeb\\WPRJSS');
define('WPRJSS_DB_PREFIX',	'wprjss'); // The table name prefix wp_{prefix}
define('WPRJSS_OPT_PREFIX', 'wprjss'); // The option name prefix in wp_options
//define('WPRJSS_TD',		''); This constant is defined in the core class. Use this constant in all your __() methods
//define('WPRJSS_VERSION',	''); This constant is defined in the core class
//define('WPRJSS_DEBUG',	true); This constant should be defined in wp-config.php to enable the Base::debug() method

// Check PHP Version and print notice if minimum not reached, otherwise start the plugin core
if (version_compare(phpversion(), WPRJSS_MIN_PHP, ">=")) {
    require_once(WPRJSS_INC . "others/start.php");
}else{
    if (!function_exists("wprjss_skip_php_admin_notice")) {
    	/**
    	 * Show an admin notice to administrators when the minimum PHP version
    	 * could not be reached. The error message is only in english available.
    	 */
        function wprjss_skip_php_admin_notice() {
            if (current_user_can('install_plugins')) {
            	extract(get_plugin_data(WPRJSS_FILE, true, false));
            	echo "<div class=\"notice notice-error\">
					<p><strong>$Name</strong> could not be initialized because you need minimum PHP version " . WPRJSS_MIN_PHP . " ... you are running: " . phpversion() . ".
				</div>";
            }
        }
    }
    add_action('admin_notices', 'wprjss_skip_php_admin_notice');
}