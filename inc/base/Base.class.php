<?php
namespace MatthiasWeb\WPRJSS\base;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Base class for all available classes for the plugin.
 */
abstract class Base {
    /**
     * Simple-to-use error_log debug log. This debug is only outprintted when
     * you define WPRJSS_DEBUG=true constant in wp-config.php
     *
     * @param mixed $message The message
     * @param string $methodOrFunction __METHOD__ OR __FUNCTION__
     */
    public function debug($message, $methodOrFunction = null) {
        if (defined('WPRJSS_DEBUG') && WPRJSS_DEBUG) {
            $log =
                (empty($methodOrFunction) ? '' : '(' . $methodOrFunction . ')') .
                ': ' .
                (is_string($message) ? $message : json_encode($message));
            error_log('WPRJSS_DEBUG ' . $log);
        }
    }

    /**
     * Get a plugin relevant table name depending on the WPRJSS_DB_PREFIX constant.
     *
     * @param string $name Append this name to the plugins relevant table with _{$name}.
     * @returns string
     */
    public function getTableName($name = '') {
        global $wpdb;
        return $wpdb->prefix . WPRJSS_DB_PREFIX . ($name == '' ? '' : '_' . $name);
    }
}
