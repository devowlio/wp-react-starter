<?php
namespace MatthiasWeb\WPRJSS\rest;

use MatthiasWeb\Utils\Service;
use MatthiasWeb\WPRJSS\base\UtilsProvider;
use WP_REST_Response;

// @codeCoverageIgnoreStart
defined('ABSPATH') or die('No script kiddies please!'); // Avoid direct file request
// @codeCoverageIgnoreEnd

/**
 * Create an example REST Service.
 *
 * @codeCoverageIgnore Example implementations gets deleted the most time after plugin creation!
 */
class HelloWorld {
    use UtilsProvider;

    /**
     * C'tor.
     */
    private function __construct() {
        // Silence is golden.
    }

    /**
     * Register endpoints.
     */
    public function rest_api_init() {
        $namespace = Service::getNamespace($this);
        register_rest_route($namespace, '/hello', [
            'methods' => 'GET',
            'callback' => [$this, 'routeHello'],
            'permission_callback' => '__return_true'
        ]);
    }

    /**
     * See API docs.
     *
     * @api {get} /wp-reactjs-starter/v1/hello Say hello
     * @apiHeader {string} X-WP-Nonce
     * @apiName SayHello
     * @apiGroup HelloWorld
     *
     * @apiSuccessExample {json} Success-Response:
     * {
     *     "hello": "world"
     * }
     * @apiVersion 0.1.0
     */
    public function routeHello() {
        return new WP_REST_Response(['hello' => 'world']);
    }

    /**
     * New instance.
     */
    public static function instance() {
        return new HelloWorld();
    }
}
