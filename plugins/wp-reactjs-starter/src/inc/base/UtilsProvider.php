<?php
namespace MatthiasWeb\WPRJSS\base;

use MatthiasWeb\Utils\Base;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * To make the composer package in packages/utils work we need to
 * make the constant variables be passed to the High-Order class.
 *
 * Put this trait in all your classes! Note also not to use the
 * below methods by your plugin, instead use direct access to the constant.
 * It just is for composer packages which needs to access dynamically the plugin!
 */
trait UtilsProvider {
    use Base;

    /**
     * Get the prefix of this plugin so composer packages can dynamically
     * build other constant values on it.
     *
     * @return string
     * @codeCoverageIgnore It only returns a string with the constant prefix
     */
    public function getPluginConstantPrefix() {
        return 'WPRJSS';
    }
}
