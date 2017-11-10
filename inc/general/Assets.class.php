<?php
namespace MatthiasWeb\WPRJSS\general;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

/**
 * Asset management for frontend scripts and styles.
 */
class Assets extends AssetsBase {
    
    /**
     * Enqueue scripts and styles depending on the type. This function is called
     * from both admin_enqueue_scripts and wp_enqueue_scripts. You can check the
     * type through the $type parameter. In this function you can include your
     * external libraries from public/lib, too.
     * 
     * @param string $type The type (see Assets constants)
     */
    public function enqueue_scripts_and_styles($type) {
        $publicFolder = $this->getPublicFolder();
        
        // Your assets implementation here... See AssetsBase for enqueue* methods.
        
        if ($type === AssetsBase::TYPE_ADMIN) {
            // @TODO variable name from generate
            $this->enqueueScript('wp-reactjs-starter', 'admin.js', array(), true);
		    $this->enqueueStyle('wp-reactjs-starter', 'admin.css');
		    wp_localize_script('wp-reactjs-starter', 'wprjssOpts', $this->adminLocalizeScript());
        }
    }
    
    /**
     * Localize the WordPress admin backend.
     * 
     * @returns array
     */
    public function adminLocalizeScript() {
        return array(
            'textDomain' => WPRJSS_TD    
        );
    }
    
    /**
     * Enqueue scripts and styles for admin pages.
     */
    public function admin_enqueue_scripts() {
        $this->enqueue_scripts_and_styles(AssetsBase::TYPE_ADMIN);
    }
    
    /**
     * Enqueue scripts and styles for frontend pages.
     */
    public function wp_enqueue_scripts() {
        $this->enqueue_scripts_and_styles(AssetsBase::TYPE_FRONTEND);
    }
}