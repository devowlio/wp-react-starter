<?php
namespace MatthiasWeb\WPRJSS\base;

use MatthiasWeb\Utils\Core as UtilsCore;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Base class for the applications Core class.
 */
abstract class Core {
    use UtilsProvider;
    use UtilsCore;

    /**
     * The constructor handles the core startup mechanism.
     *
     * The constructor is protected because a factory method should only create
     * a Core object.
     *
     * @codeCoverageIgnore
     */
    protected function __construct() {
        // Define lazy constants
        define('WPRJSS_TD', $this->getPluginData('TextDomain'));
        define('WPRJSS_VERSION', $this->getPluginData('Version'));
        $this->construct();
    }
}
