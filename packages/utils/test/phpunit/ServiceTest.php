<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use MatthiasWeb\Utils\Service;
use Mockery;
use Mockery\MockInterface;
use WP_Mock;
use WP_Mock\Tools\TestCase;

use function Patchwork\always;
use function Patchwork\redefine;

final class ServiceTest extends TestCase {
    /** @var MockInterface|Service */
    private $service;

    public function setUp(): void {
        parent::setUp();
        $this->service = Mockery::mock(Service::class);
        $this->service->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');
    }

    public function testRestApiInit() {
        $this->service->shouldReceive('rest_api_init')->passthru();

        $this->service->shouldReceive('getCore')->andReturnNull();
        redefine(Service::class . '::getNamespace', always('test/v1'));

        WP_Mock::userFunction('register_rest_route', [
            'args' => [
                'test/v1',
                '/plugin',
                [
                    'methods' => 'GET',
                    'callback' => [Mockery::self(), 'routePlugin'],
                    'permission_callback' => '__return_true'
                ]
            ]
        ]);

        $this->service->rest_api_init();

        $this->addToAssertionCount(1);
    }

    public function testRoutePlugin() {
        Mockery::mock('WP_REST_Response');
        $this->service->shouldReceive('routePlugin')->passthru();

        $mockCore = Mockery::mock(CoreImpl::class);
        $mockCore->shouldReceive('getPluginData')->andReturn([]);
        $this->service->shouldReceive('getCore')->andReturn($mockCore);

        $this->service->routePlugin();

        $this->addToAssertionCount(1);
    }

    public function testAdminNotices() {
        $this->service->shouldReceive('admin_notices')->passthru();

        $this->service
            ->shouldReceive('getSecurityPlugins')
            ->once()
            ->andReturn([]);

        WP_Mock::userFunction('__', ['return' => '__']);

        $this->expectOutputString(
            '<div id="notice-corrupt-rest-api" class="hidden notice notice-warning"><p>__</p><ul></ul><p>__</p></div>'
        );

        $this->service->admin_notices();

        // Only output once
        $this->service->admin_notices();
    }

    public function testGetSecurityPlugins() {
        $should = ['Hide My WP'];
        $this->service->shouldReceive('getSecurityPlugins')->passthru();

        WP_Mock::userFunction('get_option', [
            'times' => 1,
            'args' => ['active_plugins'],
            'return' => ['phpunit-plugin/index.php', 'hide-my-wp/index.php']
        ]);

        redefine('constant', function ($arg) {
            return $arg;
        });

        WP_Mock::userFunction('get_plugin_data', [
            'times' => 1,
            'args' => ['WP_PLUGIN_DIR/hide-my-wp/index.php'],
            'return' => ['Name' => 'Hide My WP']
        ]);

        $actual = $this->service->getSecurityPlugins();

        $this->assertEquals($actual, $should);
    }

    public function testGetUrl() {
        $should = 'http://localhost/wp-json/test/v1/';

        $mockCore = Mockery::mock(CoreImpl::class);
        $mockCore->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');

        WP_Mock::userFunction('rest_url', ['return' => $should, 'args' => ['test/v1/']]);

        redefine(Service::class . '::getNamespace', always('test/v1'));

        $actual = Service::getUrl($mockCore);

        $this->assertEquals($should, $actual);
    }

    public function testGetUrlWithCustomNamespace() {
        $should = 'http://localhost/wp-json/another/v2/';

        $mockCore = Mockery::mock(CoreImpl::class);
        $mockCore->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');

        WP_Mock::userFunction('rest_url', ['return' => $should, 'args' => ['another/v2/']]);

        $actual = Service::getUrl($mockCore, 'another/v2');

        $this->assertEquals($should, $actual);
    }

    public function testGetUrlWithEndpoint() {
        $should = 'http://localhost/wp-json/test/v1/user/1';

        $mockCore = Mockery::mock(CoreImpl::class);
        $mockCore->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');

        WP_Mock::userFunction('rest_url', ['return' => $should, 'args' => ['test/v1/user/1']]);

        redefine(Service::class . '::getNamespace', always('test/v1'));

        $actual = Service::getUrl($mockCore, null, 'user/1');

        $this->assertEquals($should, $actual);
    }

    public function testGetNamespace() {
        $should = 'test/v1';

        $mockCore = Mockery::mock(CoreImpl::class);
        $mockCore->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');

        $actual = Service::getNamespace($mockCore);

        $this->assertEquals($should, $actual);
    }

    public function testGetNamespaceWithVersion() {
        $version = 'v2';
        $should = 'test/' . $version;

        $mockCore = Mockery::mock(CoreImpl::class);
        $mockCore->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');

        $actual = Service::getNamespace($mockCore, $version);

        $this->assertEquals($should, $actual);
    }
}
