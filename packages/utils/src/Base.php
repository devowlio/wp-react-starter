<?php
namespace MatthiasWeb\Utils;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Base trait for all available classes in your plugin. The trait itself should not
 * be directly used, use the UtilsProvider trait instead in your plugin! But you can
 * still use the methods defined there.
 */
trait Base {
    use PluginReceiver;

    /**
     * Simple-to-use error_log debug log. This debug is only printed out when
     * you define _DEBUG constant in wp-config.php
     *
     * @param mixed $message The message
     * @param string $methodOrFunction __METHOD__ or __FUNCTION__
     * @return string
     */
    public function debug($message, $methodOrFunction = null) {
        if ($this->getPluginConstant(self::$PLUGIN_CONST_DEBUG)) {
            $log =
                (empty($methodOrFunction) ? '' : '(' . $methodOrFunction . ')') .
                ': ' .
                (is_string($message) ? $message : json_encode($message));
            $log = $this->getPluginConstant() . '_DEBUG ' . $log;
            error_log($log);
            return $log;
        }
        return '';
    }

    /**
     * Get a plugin relevant table name depending on the _DB_PREFIX constant.
     *
     * @param string $name Append this name to the plugins relevant table with _{$name}.
     * @return string
     */
    public function getTableName($name = '') {
        global $wpdb;
        return $wpdb->prefix .
            $this->getPluginConstant(self::$PLUGIN_CONST_DB_PREFIX) .
            (empty($name) ? '' : '_' . $name);
    }
}
