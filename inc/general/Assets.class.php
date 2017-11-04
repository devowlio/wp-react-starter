<?php
namespace MatthiasWeb\WPRJSS\general;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

/**
 * Asset management for frontend scripts and styles.
 */
class Assets extends Base {
    /**
     * Enqueue scripts and styles in admin pages.
     */
    const TYPE_ADMIN = 'admin_enqueue_scripts';
    
    /**
     * Enqueue scripts and styles in frontend pages.
     */
    const TYPE_FRONTEND = 'wp_enqueue_scripts';
    
    /**
     * Enqueue scripts and styles depending on the type. This function is called
     * from both admin_enqueue_scripts and wp_enqueue_scripts. You can check the
     * type through the $type parameter. In this function you can include your
     * external libraries from public/lib, too.
     * 
     * @param string $type The type (see Assets constants)
     */
    public function enqueue_scripts($type) {
        $publicFolder = $this->getPublicFolder();
        
        if ($type === Assets::TYPE_ADMIN) {
            wp_enqueue_script('wp-reactjs-starter',  $this->pluginsUrl("admin.js"), array(), WPRJSS_VERSION, true);
		    wp_enqueue_style('wp-reactjs-starter',   $this->pluginsUrl("admin.css"), array(), WPRJSS_VERSION);
        }
    }
    
    /**
     * Enqueue scripts and styles for admin pages.
     */
    public function admin_enqueue_scripts() {
        $this->enqueue_scripts(Assets::TYPE_ADMIN);
    }
    
    /**
     * Enqueue scripts and styles for frontend pages.
     */
    public function wp_enqueue_scripts() {
        $this->enqueue_scripts(Assets::TYPE_FRONTEND);
    }
    
    /**
     * Wrapper for plugins_url. It respects the public folder depending on the SCRIPTS_DEBUG constant.
     * 
     * @param string $asset The file name relative to the public folder path (dist or dev)
     * @returns string
     * @see getPublicFolder()
     */
    public function pluginsUrl($asset) {
        return plugins_url($this->getPublicFolder() . $asset, WPRJSS_FILE);
    }
    
    /**
     * Gets the public folder depending on the debug mode relative to the plugins folder.
     * 
     * @returns string
     */
    public function getPublicFolder() {
        return "public/" . ($this->isDebug() ? "dev" : "dist") . "/";
    }
    
    /**
     * Check if SCRIPT_DEBUG is set to true.
     */
    public function isDebug() {
        return defined('SCRIPT_DEBUG') && SCRIPT_DEBUG === true;
    }
}