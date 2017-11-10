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
define('WPRJSS_FILE', __FILE__);
define('WPRJSS_PATH', dirname(WPRJSS_FILE));
define('WPRJSS_INC', trailingslashit(path_join(WPRJSS_PATH, 'inc')));
define('WPRJSS_MIN_PHP', '5.3.0'); // Minimum of PHP 5.3 required for autoloading and namespacing
define('WPRJSS_NS', 'MatthiasWeb\\WPRJSS');
define('WPRJSS_DB_PREFIX', 'wprjss'); // The table name prefix wp_{prefix}
define('WPRJSS_OPT_PREFIX', 'wprjss'); // The option name prefix in wp_options
//define('WPRJSS_TD', ''); This constant is defined in the core class. Use this constant in all your __() methods
//define('WPRJSS_VERSION', ''); This constant is defined in the core class.
//define('WPRJSS_DEBUG', true); This constant should be defined in wp-config.php to enable the Base::debug() method

// Check PHP Version and print notice if minimum not reached, otherwise start the plugin core
require_once(WPRJSS_INC . "others/" . (version_compare(phpversion(), WPRJSS_MIN_PHP, ">=") ? "start.php" : "phpfallback.php"));