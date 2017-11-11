<?php
namespace MatthiasWeb\WPRJSS\rest;
use MatthiasWeb\WPRJSS\base;
use MatthiasWeb\WPRJSS\general;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

/**
 * Create a REST Service.
 */
class Service extends base\Base {
    
    /**
     * The namespace for this service.
     * 
     * @see Service::getUrl()
     */
    const SERVICE_NAMESPACE = 'wprjss/v1'; // @TODO generate
    
    /**
     * Register endpoints.
     */
    public function rest_api_init() {
        register_rest_route(Service::SERVICE_NAMESPACE, '/plugin', array(
            'methods' => 'GET',
            'callback' => array($this, 'routePlugin')
        ));
    }
    
    /**
     * Get plugin data.
     */
    public function routePlugin() {
        return new \WP_REST_Response(general\Core::getInstance()->getPluginData());
    } 
    
    /**
     * Get the wp-json URL for a defined REST service.
     * 
     * @param string $namespace The prefix for REST service
     * @param string $endpoint The path appended to the prefix
     * @returns String Example: https://example.com/wp-json
     * @example Service::url(Service::SERVICE_NAMESPACE) // => main path
     */
    public static function getUrl($namespace, $endpoint = '') {
        return site_url(rest_get_url_prefix()) . '/' . $namespace . '/' . $endpoint;
    }
}