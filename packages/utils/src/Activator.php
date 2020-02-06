<?php
namespace MatthiasWeb\Utils;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request.

/**
 * The activator class handles the plugin relevant activation hooks: Uninstall, activation,
 * deactivation and installation. The "installation" means installing needed database tables.
 */
trait Activator {
    /**
     * Install tables, stored procedures or whatever in the database.
     * This method is always called when the version bumps up or for
     * the first initial activation.
     *
     * @param boolean $errorlevel If true throw errors.
     */
    abstract public function dbDelta($errorlevel);

    /**
     * Run an installation or dbDelta within a callable.
     *
     * @param boolean $errorlevel Set true to throw errors.
     * @param callable $installThisCallable Set a callable to install this one instead of the default.
     */
    public function install($errorlevel = false, $installThisCallable = null) {
        global $wpdb;

        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_FILE')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        // @codeCoverageIgnoreEnd

        // Avoid errors printed out.
        if ($errorlevel === false) {
            $show_errors = $wpdb->show_errors(false);
            $suppress_errors = $wpdb->suppress_errors(false);
            $errorLevel = error_reporting(0);
        }

        if ($installThisCallable === null) {
            $this->dbDelta($errorlevel);
        } else {
            call_user_func($installThisCallable);
        }

        if ($errorlevel === false) {
            $wpdb->show_errors($show_errors);
            $wpdb->suppress_errors($suppress_errors);
            error_reporting($errorLevel);
        }

        if ($installThisCallable === null) {
            update_option(
                $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_OPT_PREFIX) . '_db_version',
                $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_VERSION)
            );
        }
    }
}
