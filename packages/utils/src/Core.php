<?php
namespace MatthiasWeb\Utils;

defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request

/**
 * Base class for the applications Core class.
 */
trait Core {
    /**
     * The stored plugin data.
     */
    private $plugin_data;

    /**
     * The plugins activator class.
     *
     * @see Activator
     */
    private $activator;

    /**
     * The plugins asset class.
     *
     * @see Assets
     */
    private $assets;

    /**
     * The utils service class.
     *
     * @see Service
     */
    private $service;

    /**
     * The constructor handles the core startup mechanism.
     *
     * The constructor is protected because a factory method should only create
     * a Core object.
     */
    protected function construct() {
        // Define lazy constants
        $pluginFile = $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE);

        // Register immediate actions and filters
        $this->activator = $this->getPluginClassInstance(PluginReceiver::$PLUGIN_CLASS_ACTIVATOR);
        $this->assets = $this->getPluginClassInstance(PluginReceiver::$PLUGIN_CLASS_ASSETS);
        $this->service = Service::instance($this);

        add_action('plugins_loaded', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'updateDbCheck']);
        add_action('init', [$this, 'init']);
        add_action('rest_api_init', [$this->getService(), 'rest_api_init']);

        // Localize the plugin and package itself
        $this->getPluginClassInstance(PluginReceiver::$PLUGIN_CLASS_LOCALIZATION)->hooks();
        PackageLocalization::instance(
            $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_ROOT_SLUG),
            dirname(__DIR__)
        )->hooks();

        register_activation_hook($pluginFile, [$this->getActivator(), 'activate']);
        register_deactivation_hook($pluginFile, [$this->getActivator(), 'deactivate']);
    }

    /**
     * The plugin is loaded. Start to register the localization (i18n) files.
     * Also respect packages in vendor dir.
     */
    public function i18n() {
        load_plugin_textdomain(
            $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_TEXT_DOMAIN),
            false,
            dirname(plugin_basename($this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE))) .
                $this->getPluginData('DomainPath')
        );

        // Include text domains of packages
        $packages = $this->getInternalPackages();
        $locale = determine_locale();
        foreach ($packages as $package => $path) {
            $textdomain = $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_ROOT_SLUG) . '-' . $package;
            $locale = apply_filters('plugin_locale', $locale, $textdomain);
            $mofile = dirname($path) . '/' . $package . '-' . $locale . '.mo';
            load_textdomain($textdomain, $mofile);
        }
    }

    /**
     * Updates the database version in the options table.
     * It also installs the needed database tables.
     */
    public function updateDbCheck() {
        $installed = get_option($this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_OPT_PREFIX) . '_db_version');
        if ($installed !== $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_VERSION)) {
            $this->debug('(Re)install the database tables', __FUNCTION__);
            $this->getActivator()->install();
        }
    }

    /**
     * Get a list of internal packages (our own, symlinked from the monorepo).
     *
     * @return object
     */
    public function getInternalPackages() {
        $result = [];

        $globPath = path_join(
            $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_PATH),
            'vendor/' . $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_ROOT_SLUG) . '/*/languages/backend/*.pot'
        );
        foreach (glob($globPath) as $path) {
            $package = pathinfo($path, PATHINFO_FILENAME);
            $result[$package] = $path;
        }

        return $result;
    }

    /**
     * Gets the plugin data.
     *
     * @param string $key The key of the data to return
     * @return string[]|string|null
     * @see https://developer.wordpress.org/reference/functions/get_plugin_data/
     */
    public function getPluginData($key = null) {
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_FILE')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }
        // @codeCoverageIgnoreEnd

        $data = isset($this->plugin_data)
            ? $this->plugin_data
            : ($this->plugin_data = get_plugin_data(
                $this->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE),
                true,
                false
            ));
        return $key === null ? $data : (isset($data[$key]) ? $data[$key] : null);
    }

    /**
     * Getter.
     *
     * @return Activator
     * @codeCoverageIgnore
     */
    public function getActivator() {
        return $this->activator;
    }

    /**
     * Getter
     *
     * @return Assets
     * @codeCoverageIgnore
     */
    public function getAssets() {
        return $this->assets;
    }

    /**
     * Getter
     *
     * @return Service
     * @codeCoverageIgnore
     */
    public function getService() {
        return $this->service;
    }
}
