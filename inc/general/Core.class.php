<?php
namespace MatthiasWeb\WPRJSS\general;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

// Include files, where autoloading is not possible, yet
require_once("Base.class.php");

/**
 * Singleton core class which handles the main system for plugin. It includes
 * registering of the autoloader, all hooks (actions & filters) and triggering
 * of the migration system.
 */
class Core extends Base {
    
    private static $me = null;
    
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
     * The stored plugin data.
     * 
     * @see getPluginData()
     */
    private $plugin_data;
    
    /**
     * The constructor is called only once and handles the core startup mechanism.
     */
    private function __construct() {
        // Define lazy constants
        define('WPRJSS_TD', $this->getPluginData("TextDomain"));
        define('WPRJSS_VERSION', $this->getPluginData("Version"));

        // Register autoload
        spl_autoload_register(array($this, 'autoloadRegister'));
        
        $this->activator = new Activator();
        $this->assets = new Assets();
        
        // Register immediate actions and filters
        add_action('plugins_loaded', array($this, 'i18n'));
        add_action('init', array($this, 'init'));
        register_activation_hook(WPRJSS_FILE, array($this->getActivator(), 'activate'));
        register_deactivation_hook(WPRJSS_FILE, array($this->getActivator(), 'deactivate'));
    }
    
    /**
     * The init function is fired even the init hook of WordPress. If possible
     * it should register all hooks to have them in one place.
     */
    public function init() {
        // Start migration check
        $this->updateDbCheck();
        
        // Register all your hooks here
        add_action('admin_enqueue_scripts', array($this->getAssets(), 'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this->getAssets(), 'wp_enqueue_scripts'));
    }
    
    /**
     * Autoload PHP files (classes and interfaces). It respects the given namespace
     * within the inc/ folder. Each subnamespace must be available as own folder.
     * Class files should be defined as ClassName.class.php. Interface files should
     * be defined as IName.interface.php.
     * 
     * @param string $className Full qualified class name
     */
    public function autoloadRegister($className) {
        $namespace = WPRJSS_NS . "\\";
        if (0 === strpos($className, $namespace)) {
            $name = substr($className, strlen($namespace));
            $last = explode("\\", $name);
            $isInterface = substr($last[count($last) - 1], 0, 1) === "I";
            $filename = WPRJSS_INC . str_replace('\\', '/', $name) . '.' . ($isInterface ? 'interface' : 'class') . '.php';
            if (file_exists($filename)) {
                require_once($filename);
            }
        }
    }
    
    /**
     * The plugin is loaded. Start to register the localization (i18n) files.
     */
    public function i18n() {
        load_plugin_textdomain( WPRJSS_TD, FALSE, WPRJSS_PATH . $this->getPluginData("DomainPath") );
    }
    
    /**
     * Updates the database version in the options and runs the migration system.
     * It also installs the needed database tables.
     * 
     * @see Migration
     */
    public function updateDbCheck() {
        $installed = get_option( WPRJSS_OPT_PREFIX . '_db_version' );
        if ($installed != WPRJSS_VERSION) {
            $this->debug("(Re)install the database tables", __FUNCTION__);
            $this->getActivator()->install();
        }
    }
    
    /**
     * Gets the plugin data.
     * 
     * @param string $key The key of the data to return
     * @returns string[]|string
     * @see https://developer.wordpress.org/reference/functions/get_plugin_data/
     */
    public function getPluginData($key = null) {
        require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        $data = isset($this->plugin_data) ? $this->plugin_data : $this->plugin_data = get_plugin_data(WPRJSS_FILE, true, false);
        return $key === null ? $data : $data[$key];
    }
    
    /**
     * @returns Activator
     */
    public function getActivator() {
        return $this->activator;
    }
    
    /**
     * @returns Assets
     */
    public function getAssets() {
        return $this->assets;
    }
    
    /**
     * Get singleton core class.
     * 
     * @returns Core
     */
    public static function getInstance() {
        return self::$me === null ? self::$me = new Core() : self::$me;
    }
}