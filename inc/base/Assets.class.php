<?php
namespace MatthiasWeb\WPRJSS\base;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

/**
 * Base asset management class for frontend scripts and styles.
 */
abstract class Assets extends Base {
    
    /**
     * Enqueue scripts and styles in admin pages.
     */
    const TYPE_ADMIN = 'admin_enqueue_scripts';
    
    /**
     * Enqueue scripts and styles in frontend pages.
     */
    const TYPE_FRONTEND = 'wp_enqueue_scripts';
    
    /**
     * The regex to get the library folder name of public/lib files.
     */
    const LIB_CACHEBUSTER_REGEX = '/^public\/lib\/([^\/]+)/';
    
    /**
     * Enqueue scripts and styles depending on the type. You can check the
     * type through the $type parameter. In this function you can include your
     * external libraries from public/lib, too.
     * 
     * @param string $type The type (see Assets constants)
     */
    abstract public function enqueue_scripts_and_styles($type);
    
    /**
     * Registers the script if $src provided (does NOT overwrite), and enqueues it. Use this wrapper
     * method instead of wp_enqueue_script if you want to use the cachebuster for the given src. If the
     * src is not found in the cachebuster (inc/others/cachebuster.php) it falls back to WPRJSS_VERSION.
     * 
     * @param string $handle Name of the script. Should be unique.
     * @param string $src The src relative to public/dist or public/dev folder (when $isLib is false)
     * @param array $deps An array of registered script handles this script depends on.
     * @param boolean $in_footer Whether to enqueue the script before </body> instead of in the <head>.
     * @param boolean $isLib If true the public/lib/ folder is used.
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/ For parameters
     */
    public function enqueueScript($handle, $src = '', $deps = array(), $in_footer = false, $isLib = false) {
        $src = $this->getPublicFolder($isLib) . $src;
        wp_enqueue_script($handle, plugins_url($src, WPRJSS_FILE), array(), $this->getCachebusterVersion($src, $isLib), true);
    }
    
    /**
     * Wrapper for Assets::enqueueScript() method with $isLib = true.
     * 
     * @see Assets::enqueueScript()
     */
    public function enqueueLibraryScript($handle, $src = '', $deps = array(), $in_footer = false) {
        $this->enqueueScript($handle, $src, $deps, $in_footer, true);
    }
    
    /**
     * Enqueue a CSS stylesheet. Use this wrapper method instead of wp_enqueue_style if you want 
     * to use the cachebuster for the given src. If the src is not found in the cachebuster (inc/others/cachebuster.php)
     * it falls back to WPRJSS_VERSION.
     * 
     * @param string $handle Name of the style. Should be unique.
     * @param string $src The src relative to public/dist or public/dev folder (when $isLib is false)
     * @param array $deps An array of registered style handles this style depends on.
     * @param string $media The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
     * @param boolean $isLib If true the public/lib/ folder is used.
     * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/ For parameters
     */
    public function enqueueStyle($handle, $src = '', $deps = array(), $media = 'all', $isLib = false) {
        $src = $this->getPublicFolder($isLib) . $src;
        wp_enqueue_style($handle, plugins_url($src, WPRJSS_FILE), array(), $this->getCachebusterVersion($src, $isLib), $media);
    }
    
    /**
     * Wrapper for Assets::enqueueStyle() method with $isLib = true.
     * 
     * @see Assets::enqueueStyle()
     */
    public function enqueueLibraryStyle($handle, $src = '', $deps = array(), $media = 'all') {
        $this->enqueueStyle($handle, $src, $deps, $media, true);
    }
    
    /**
     * Get the cachebuster entry for a given file. If the $src begins with public/lib/ it
     * will use the inc/others/cachebuster-lib.php cachebuster instead of inc/others/cachebuster.php.
     * 
     * @param string $src The src relative to public/ folder
     * @param boolean $isLib If true the cachebuster-lib.php cachebuster is used
     * @see inc/others/cachebuster.php
     * @returns string WPRJSS_VERSION or cachebuster timestamp
     */
    public function getCachebusterVersion($src, $isLib = false) {
        $default = WPRJSS_VERSION;
        $path = WPRJSS_INC . 'others/';
        $path_lib = $path . 'cachebuster-lib.php';
        $path = $path . 'cachebuster.php';
        if ($isLib) {
            // Library cachebuster
            if (file_exists($path_lib)) {
                static $cachebuster_lib = null;
                if ($cachebuster_lib === null) {
                    $cachebuster_lib = include($path_lib);
                }
                
                // Parse module
                preg_match(Assets::LIB_CACHEBUSTER_REGEX, $src, $matches);
                if (is_array($matches) && isset($matches[1]) && ($module = $matches[1]) && 
                    is_array($cachebuster_lib) && array_key_exists($module, $cachebuster_lib)) {
                    // Valid cachebuster
                    return $cachebuster_lib[$module];
                }
            }
        }else{
            // Main cachebuster
            if (file_exists($path)) {
                // Store cachebuster once
                static $cachebuster = null;
                if ($cachebuster === null) {
                    $cachebuster = include($path);
                }
                
                if (is_array($cachebuster) && array_key_exists($src, $cachebuster)) {
                    // Valid cachebuster
                    return $cachebuster[$src];
                }
            }
        }
        return $default;
    }
    
    /**
     * Wrapper for plugins_url. It respects the public folder depending on the SCRIPTS_DEBUG constant.
     * 
     * @param string $asset The file name relative to the public folder path (dist or dev)
     * @param boolean $isLib If true the public/lib/ folder is used.
     * @returns string
     * @see getPublicFolder()
     */
    public function getPluginsUrl($asset, $isLib = false) {
        return plugins_url($this->getPublicFolder($isLib) . $asset, WPRJSS_FILE);
    }
    
    /**
     * Gets a public folder depending on the debug mode relative to the plugins folder with trailing slash.
     * 
     * @param boolean $isLib If true the public/lib/ folder is returned.
     * @returns string
     */
    public function getPublicFolder($isLib = false) {
        return "public/" . ($isLib ? 'lib' : ($this->isScriptDebug() ? 'dev' : 'dist')) . "/";
    }
    
    /**
     * Check if SCRIPT_DEBUG is set to true.
     * 
     * @returns boolean
     */
    public function isScriptDebug() {
        return defined('SCRIPT_DEBUG') && SCRIPT_DEBUG === true;
    }
}