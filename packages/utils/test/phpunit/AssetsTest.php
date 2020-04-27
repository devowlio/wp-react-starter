<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use MatthiasWeb\Utils\Assets;
use MatthiasWeb\Utils\PluginReceiver;
use MatthiasWeb\Utils\Service;
use Mockery;
use Mockery\MockInterface;
use ReflectionMethod;
use TestCaseUtils;
use WP_Mock;
use WP_Mock\Tools\TestCase;

use function Patchwork\always;
use function Patchwork\redefine;

if (!class_exists(__NAMESPACE__ . '\\AssetsImpl')) {
    class AssetsImpl {
        use Assets;
        use PluginReceiver;

        public function overrideLocalizeScript($context) {
            // Silence is golden.
        }

        public function enqueue_scripts_and_styles($type, $hook_suffix = null) {
            // Silence is golden.
        }
    }
}

if (!class_exists(__NAMESPACE__ . 'WP_Scripts')) {
    class WP_Scripts {
        public function query($handle) {
            switch ($handle) {
                case 'react':
                    return (object) ['ver' => '16.8'];
                case 'phpunit-admin':
                    return (object) ['ver' => PHPUNIT_VERSION, 'src' => 'admin.js'];
                    break;
                default:
                    return false;
            }
        }
    }
}

final class AssetsTest extends TestCase {
    use TestCaseUtils;

    /** @var MockInterface|AssetsImpl */
    private $assets;

    public function setUp(): void {
        parent::setUp();
        $this->assets = Mockery::mock(AssetsImpl::class);
        $this->assets->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');
    }

    public function testLocalizeScript() {
        $getUrl = 'http://localhost/wp-json/test/v1';
        $getNamespace = 'test/v1';
        $get_rest_url = 'http://localhost/wp-json/';
        $nonce = 'fjDjl2$dDS';
        $public_url = 'http://localhost/wp-content/plugins/phpunit/public';
        $should = [
            'slug' => PHPUNIT_SLUG,
            'textDomain' => PHPUNIT_TD,
            'version' => PHPUNIT_VERSION,
            'restUrl' => $getUrl,
            'restNamespace' => $getNamespace,
            'restRoot' => $get_rest_url,
            'restQuery' => ['_v' => PHPUNIT_VERSION],
            'restNonce' => $nonce,
            'publicUrl' => $public_url . '/',
            'others' => []
        ];

        $this->assets->shouldReceive('localizeScript')->passthru();

        redefine(Service::class . '::getUrl', function () use ($getUrl) {
            return $getUrl;
        });
        redefine(Service::class . '::getNamespace', function () use ($getNamespace) {
            return $getNamespace;
        });

        $this->assets
            ->shouldReceive('getAsciiUrl')
            ->with($getUrl)
            ->andReturnArg(0);

        WP_Mock::userFunction('get_rest_url', [
            'return' => $get_rest_url
        ]);

        $this->assets
            ->shouldReceive('getAsciiUrl')
            ->with($get_rest_url)
            ->andReturnArg(0);

        WP_Mock::userFunction('wp_installing', ['return' => false]);
        WP_Mock::userFunction('wp_create_nonce', ['args' => ['wp_rest'], 'return' => $nonce]);
        WP_Mock::userFunction('plugins_url', [
            'args' => ['public', PHPUNIT_FILE],
            'return' => $public_url
        ]);
        WP_Mock::userFunction('trailingslashit', ['args' => $public_url, 'return' => $public_url . '/']);

        $this->assets
            ->shouldReceive('overrideLocalizeScript')
            ->with(Assets::$TYPE_ADMIN)
            ->andReturn([]);

        $actual = $this->assets->localizeScript(Assets::$TYPE_ADMIN);

        $this->assertEquals($should, $actual);
    }

    public function testLocalizeScriptWhileInstallingInNonMultisite() {
        $getUrl = 'http://localhost/wp-json/test/v1';
        $getNamespace = 'test/v1';
        $get_rest_url = 'http://localhost/wp-json/';
        $public_url = 'http://localhost/wp-content/plugins/phpunit/public';
        $should = [
            'slug' => PHPUNIT_SLUG,
            'textDomain' => PHPUNIT_TD,
            'version' => PHPUNIT_VERSION,
            'restUrl' => $getUrl,
            'restNamespace' => $getNamespace,
            'restRoot' => $get_rest_url,
            'restQuery' => ['_v' => PHPUNIT_VERSION],
            'restNonce' => '',
            'publicUrl' => $public_url . '/',
            'others' => []
        ];

        $this->assets->shouldReceive('localizeScript')->passthru();

        redefine(Service::class . '::getUrl', function () use ($getUrl) {
            return $getUrl;
        });
        redefine(Service::class . '::getNamespace', function () use ($getNamespace) {
            return $getNamespace;
        });

        $this->assets
            ->shouldReceive('getAsciiUrl')
            ->with($getUrl)
            ->andReturnArg(0);

        WP_Mock::userFunction('get_rest_url', [
            'return' => $get_rest_url
        ]);

        $this->assets
            ->shouldReceive('getAsciiUrl')
            ->with($get_rest_url)
            ->andReturnArg(0);

        WP_Mock::userFunction('wp_installing', ['return' => true]);
        WP_Mock::userFunction('is_multisite', ['return' => false]);
        WP_Mock::userFunction('wp_create_nonce', ['times' => 0]);
        WP_Mock::userFunction('plugins_url', [
            'args' => ['public', PHPUNIT_FILE],
            'return' => $public_url
        ]);
        WP_Mock::userFunction('trailingslashit', ['args' => $public_url, 'return' => $public_url . '/']);

        $this->assets
            ->shouldReceive('overrideLocalizeScript')
            ->with(Assets::$TYPE_ADMIN)
            ->andReturn([]);

        $actual = $this->assets->localizeScript(Assets::$TYPE_ADMIN);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueReact() {
        $this->assets->shouldReceive('enqueueReact')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnFalse();

        WP_Mock::userFunction('wp_scripts', ['return' => new WP_Scripts()]);
        WP_Mock::userFunction('plugins_url', ['times' => 0]);

        $this->assets
            ->shouldReceive('enqueueLibraryScript')
            ->once()
            ->with(Assets::$HANDLE_REACT, Mockery::any());
        $this->assets
            ->shouldReceive('enqueueLibraryScript')
            ->once()
            ->with(Assets::$HANDLE_REACT_DOM, Mockery::any(), Assets::$HANDLE_REACT);

        $this->assets->enqueueReact();

        $this->addToAssertionCount(1);
    }

    public function testEnqueueReactLower168() {
        $this->assets->shouldReceive('enqueueReact')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnFalse();

        redefine(WP_Scripts::class . '::query', always((object) ['ver' => '16.7']));

        WP_Mock::userFunction('wp_scripts', ['return' => new WP_Scripts()]);

        $this->assets->shouldReceive('getPublicFolder')->with(true);

        WP_Mock::userFunction('plugins_url', [
            'times' => 1,
            'args' => ['react/umd/react.production.min.js', PHPUNIT_FILE]
        ]);
        WP_Mock::userFunction('plugins_url', [
            'times' => 1,
            'args' => ['react-dom/umd/react-dom.production.min.js', PHPUNIT_FILE]
        ]);

        $this->assets->shouldNotReceive('enqueueLibraryScript')->with(Assets::$HANDLE_REACT, Mockery::any());
        $this->assets
            ->shouldNotReceive('enqueueLibraryScript')
            ->with(Assets::$HANDLE_REACT_DOM, Mockery::any(), Assets::$HANDLE_REACT);

        $this->assets->enqueueReact();

        $this->addToAssertionCount(1);
    }

    public function testEnqueueReactLower168Dev() {
        $this->assets->shouldReceive('enqueueReact')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();

        redefine(WP_Scripts::class . '::query', always((object) ['ver' => '16.7']));

        WP_Mock::userFunction('wp_scripts', ['return' => new WP_Scripts()]);

        $this->assets->shouldReceive('getPublicFolder')->with(true);

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'times' => 1,
            'args' => ['react/umd/react.development.js', PHPUNIT_FILE]
        ]);
        WP_Mock::userFunction('plugins_url', [
            'times' => 1,
            'args' => ['react-dom/umd/react-dom.development.js', PHPUNIT_FILE]
        ]);

        $this->assets->shouldNotReceive('enqueueLibraryScript')->with(Assets::$HANDLE_REACT, Mockery::any());
        $this->assets
            ->shouldNotReceive('enqueueLibraryScript')
            ->with(Assets::$HANDLE_REACT_DOM, Mockery::any(), Assets::$HANDLE_REACT);

        $this->assets->enqueueReact();

        $this->addToAssertionCount(1);
    }

    public function testEnqueueReactLower168DevNotExisting() {
        $this->assets->shouldReceive('enqueueReact')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();

        redefine(WP_Scripts::class . '::query', always((object) ['ver' => '16.7']));

        WP_Mock::userFunction('wp_scripts', ['return' => new WP_Scripts()]);

        $this->assets->shouldReceive('getPublicFolder')->with(true);

        redefine('file_exists', always(false));

        WP_Mock::userFunction('plugins_url', [
            'times' => 1,
            'args' => ['react/umd/react.production.min.js', PHPUNIT_FILE]
        ]);
        WP_Mock::userFunction('plugins_url', [
            'times' => 1,
            'args' => ['react-dom/umd/react-dom.production.min.js', PHPUNIT_FILE]
        ]);

        $this->assets->shouldNotReceive('enqueueLibraryScript')->with(Assets::$HANDLE_REACT, Mockery::any());
        $this->assets
            ->shouldNotReceive('enqueueLibraryScript')
            ->with(Assets::$HANDLE_REACT_DOM, Mockery::any(), Assets::$HANDLE_REACT);

        $this->assets->enqueueReact();

        $this->addToAssertionCount(1);
    }

    public function testEnqueueReactLower168NoReactDom() {
        $this->assets->shouldReceive('enqueueReact')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnFalse();

        redefine(WP_Scripts::class . '::query', function ($handle) {
            if ($handle === 'react') {
                return (object) ['ver' => '16.7'];
            } else {
                return false;
            }
        });

        WP_Mock::userFunction('wp_scripts', ['return' => new WP_Scripts()]);

        $this->assets->shouldReceive('getPublicFolder')->with(true);

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'times' => 1,
            'args' => ['react/umd/react.production.min.js', PHPUNIT_FILE]
        ]);

        $this->assets->shouldNotReceive('enqueueLibraryScript')->with(Assets::$HANDLE_REACT, Mockery::any());
        $this->assets
            ->shouldReceive('enqueueLibraryScript')
            ->once()
            ->with(Assets::$HANDLE_REACT_DOM, Mockery::any(), Assets::$HANDLE_REACT);

        $this->assets->enqueueReact();

        $this->addToAssertionCount(1);
    }

    public function testEnqueueMobx() {
        $this->assets->shouldReceive('enqueueMobx')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnFalse();
        $this->assets
            ->shouldReceive('enqueueLibraryScript')
            ->once()
            ->with(Assets::$HANDLE_MOBX, Mockery::any());

        $this->assets->enqueueMobx();

        $this->addToAssertionCount(1);
    }

    public function testProbablyEnqueueChunk() {
        $deps = ['jquery'];
        $this->assets
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('probablyEnqueueChunk')
            ->passthru();
        $this->assets
            ->shouldReceive('enqueue')
            ->once()
            ->with('vendor~admin', 'vendor~admin.js', $deps, false, 'script', true, 'all')
            ->andReturn('vendor~admin');

        $method = new ReflectionMethod(AssetsImpl::class, 'probablyEnqueueChunk');
        $method->setAccessible(true);
        $method->invokeArgs($this->assets, ['admin', false, 'admin.js', &$deps, true, 'all']);

        $this->assertCount(2, $deps);
    }

    public function testProbablyEnqueueChunkFromLib() {
        $deps = ['jquery'];
        $this->assets
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('probablyEnqueueChunk')
            ->passthru();
        $this->assets->shouldNotReceive('enqueue');

        $method = new ReflectionMethod(AssetsImpl::class, 'probablyEnqueueChunk');
        $method->setAccessible(true);
        $method->invokeArgs($this->assets, ['admin', true, 'admin.js', &$deps, true, 'all']);

        $this->assertCount(1, $deps);
    }

    public function testProbablyEnqueueChunkFailing() {
        $deps = ['jquery'];
        $this->assets
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('probablyEnqueueChunk')
            ->passthru();
        $this->assets
            ->shouldReceive('enqueue')
            ->once()
            ->with('vendor~admin', 'vendor~admin.js', $deps, false, 'script', true, 'all')
            ->andReturnFalse();

        $method = new ReflectionMethod(AssetsImpl::class, 'probablyEnqueueChunk');
        $method->setAccessible(true);
        $method->invokeArgs($this->assets, ['admin', false, 'admin.js', &$deps, true, 'all']);

        $this->assertCount(1, $deps);
    }

    // enqueue start
    public function testEnqueue() {
        $handle = 'admin';
        $should = PHPUNIT_SLUG . '-' . $handle;
        $publicFolder = 'public/dev/';
        $script = 'admin.js';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/public/dev/admin.js';
        $version = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->passthru();
        $this->assets->shouldReceive('getPublicFolder')->andReturn($publicFolder);

        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $publicFolder . $script]]);

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'return' => $plugins_url,
            'args' => [$publicFolder . $script, PHPUNIT_FILE]
        ]);

        $this->assets
            ->shouldReceive('getCachebusterVersion')
            ->with($publicFolder . $script, false)
            ->andReturn($version);
        $this->assets
            ->shouldReceive('probablyEnqueueChunk')
            ->once()
            ->with($should, false, $script, [], true, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $version, true]
        ]);
        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, Assets::$PUBLIC_JSON_I18N]]);

        $this->assets
            ->shouldReceive('setLazyScriptTranslations')
            ->once()
            ->with($should, PHPUNIT_TD, null);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueue');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, $script);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueNonExistingFile() {
        $handle = 'admin';
        $should = false;
        $publicFolder = 'public/dev/';
        $script = 'admin.js';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->passthru();
        $this->assets->shouldReceive('getPublicFolder')->andReturn($publicFolder);

        redefine('file_exists', always(false));

        WP_Mock::userFunction('path_join');
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 0]);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueue');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, $script);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueWithLib() {
        $handle = 'admin';
        $should = $handle;
        $publicFolder = 'public/lib/';
        $script = 'mobx/index.js';
        $isLib = true;
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/public/lib/mobx/index.js';
        $version = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->passthru();
        $this->assets
            ->shouldReceive('getPublicFolder')
            ->with($isLib)
            ->andReturn($publicFolder);

        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $publicFolder . $script]]);

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'return' => $plugins_url,
            'args' => [$publicFolder . $script, PHPUNIT_FILE]
        ]);

        $this->assets
            ->shouldReceive('getCachebusterVersion')
            ->with($publicFolder . $script, $isLib)
            ->andReturn($version);
        $this->assets
            ->shouldReceive('probablyEnqueueChunk')
            ->once()
            ->with($handle, true, $script, [], true, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $version, true]
        ]);
        WP_Mock::userFunction('path_join');

        $this->assets->shouldNotReceive('setLazyScriptTranslations');

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueue');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, $script, [], $isLib);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueWithSrcArray() {
        $handle = 'admin';
        $should = PHPUNIT_SLUG . '-' . $handle;
        $publicFolder = 'public/dev/';
        $script = 'admin.js';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/public/dev/admin.js';
        $version = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->passthru();
        $this->assets->shouldReceive('getPublicFolder')->andReturn($publicFolder);

        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $publicFolder . $script]]);

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'return' => $plugins_url,
            'args' => [$publicFolder . $script, PHPUNIT_FILE]
        ]);

        $this->assets
            ->shouldReceive('getCachebusterVersion')
            ->with($publicFolder . $script, false)
            ->andReturn($version);

        $this->assets
            ->shouldReceive('probablyEnqueueChunk')
            ->once()
            ->with($should, false, $script, [], true, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $version, true]
        ]);
        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, Assets::$PUBLIC_JSON_I18N]]);

        $this->assets
            ->shouldReceive('setLazyScriptTranslations')
            ->once()
            ->with($should, PHPUNIT_TD, null);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueue');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, [$script]);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueWithComplexSrcArray() {
        $handle = 'admin';
        $should = PHPUNIT_SLUG . '-' . $handle;
        $publicFolder = 'public/dev/';
        $script = 'admin.js';
        $nonLoadingScript = 'admin.min.js';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/public/dev/admin.js';
        $version = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->passthru();
        $this->assets->shouldReceive('getPublicFolder')->andReturn($publicFolder);

        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $publicFolder . $script]]);

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'return' => $plugins_url,
            'args' => [$publicFolder . $script, PHPUNIT_FILE]
        ]);

        $this->assets
            ->shouldReceive('getCachebusterVersion')
            ->with($publicFolder . $script, false)
            ->andReturn($version);
        $this->assets
            ->shouldReceive('probablyEnqueueChunk')
            ->once()
            ->with($should, false, $script, [], true, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $version, true]
        ]);
        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, Assets::$PUBLIC_JSON_I18N]]);

        $this->assets
            ->shouldReceive('setLazyScriptTranslations')
            ->once()
            ->with($should, PHPUNIT_TD, null);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueue');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, [[false, $nonLoadingScript], $script]);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueWithStyle() {
        $handle = 'admin';
        $should = PHPUNIT_SLUG . '-' . $handle;
        $publicFolder = 'public/dev/';
        $script = 'admin.css';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/public/dev/admin.css';
        $version = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->passthru();
        $this->assets->shouldReceive('getPublicFolder')->andReturn($publicFolder);

        WP_Mock::userFunction('path_join');

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'return' => $plugins_url
        ]);

        $this->assets->shouldReceive('getCachebusterVersion')->andReturn($version);

        WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $version, 'all']
        ]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 0]);

        $this->assets->shouldNotReceive('setLazyScriptTranslations');

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueue');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, $script, [], false, 'style');

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueWithScriptParameters() {
        $handle = 'admin';
        $should = PHPUNIT_SLUG . '-' . $handle;
        $publicFolder = 'public/dev/';
        $script = 'admin.js';
        $deps = [Assets::$HANDLE_REACT];
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/public/dev/admin.js';
        $version = '1.0.0';
        $in_footer = false;

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->passthru();
        $this->assets->shouldReceive('getPublicFolder')->andReturn($publicFolder);

        WP_Mock::userFunction('path_join');

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'return' => $plugins_url
        ]);

        $this->assets->shouldReceive('getCachebusterVersion')->andReturn($version);
        $this->assets
            ->shouldReceive('probablyEnqueueChunk')
            ->once()
            ->with($should, false, $script, $deps, $in_footer, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, $deps, $version, $in_footer]
        ]);

        $this->assets->shouldReceive('setLazyScriptTranslations');

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueue');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, $script, $deps, false, 'script', $in_footer);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueWithStyleParameters() {
        $handle = 'admin';
        $should = PHPUNIT_SLUG . '-' . $handle;
        $publicFolder = 'public/dev/';
        $script = 'admin.css';
        $deps = [Assets::$HANDLE_REACT];
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/public/dev/admin.css';
        $version = '1.0.0';
        $media = 'print';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->passthru();
        $this->assets->shouldReceive('getPublicFolder')->andReturn($publicFolder);

        WP_Mock::userFunction('path_join');

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', [
            'return' => $plugins_url
        ]);

        $this->assets->shouldReceive('getCachebusterVersion')->andReturn($version);

        WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 1,
            'args' => [$should, $plugins_url, $deps, $version, $media]
        ]);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueue');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, $script, $deps, false, 'style', null, $media);

        $this->assertEquals($should, $actual);
    }
    // enqueue end

    public function testEnqueueScript() {
        $handle = 'phpunit-admin';
        $script = 'admin.js';

        $this->assets->shouldReceive('enqueueScript')->passthru();
        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->with($handle, $script, [], false, 'script', true);

        $this->assets->enqueueScript($handle, $script);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueScriptWithParameters() {
        $handle = 'phpunit-admin';
        $script = 'admin.js';
        $deps = [Assets::$HANDLE_REACT];
        $in_footer = true;
        $isLib = true;

        $this->assets->shouldReceive('enqueueScript')->passthru();
        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->with($handle, $script, $deps, $isLib, 'script', $in_footer);

        $this->assets->enqueueScript($handle, $script, $deps, $in_footer, $isLib);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueLibraryScript() {
        $handle = 'phpunit-admin';
        $script = 'admin.js';

        $this->assets->shouldReceive('enqueueLibraryScript')->passthru();
        $this->assets->shouldReceive('enqueueScript')->with($handle, $script, [], false, true);

        $this->assets->enqueueLibraryScript($handle, $script);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueLibraryScriptWithParameters() {
        $handle = 'phpunit-admin';
        $script = 'admin.js';
        $deps = [Assets::$HANDLE_REACT];
        $in_footer = true;

        $this->assets->shouldReceive('enqueueLibraryScript')->passthru();
        $this->assets->shouldReceive('enqueueScript')->with($handle, $script, $deps, $in_footer, true);

        $this->assets->enqueueLibraryScript($handle, $script, $deps, $in_footer);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueStyle() {
        $handle = 'phpunit-admin';
        $script = 'admin.css';

        $this->assets->shouldReceive('enqueueStyle')->passthru();
        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->with($handle, $script, [], false, 'style', null, 'all');

        $this->assets->enqueueStyle($handle, $script);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueStyleWithParameters() {
        $handle = 'phpunit-admin';
        $script = 'admin.css';
        $deps = [Assets::$HANDLE_REACT];
        $media = 'all';
        $isLib = true;

        $this->assets->shouldReceive('enqueueStyle')->passthru();
        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueue')->with($handle, $script, $deps, $isLib, 'style', null, $media);

        $this->assets->enqueueStyle($handle, $script, $deps, $media, $isLib);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueLibraryStyle() {
        $handle = 'phpunit-admin';
        $script = 'admin.css';

        $this->assets->shouldReceive('enqueueLibraryStyle')->passthru();
        $this->assets->shouldReceive('enqueueStyle')->with($handle, $script, [], 'all', true);

        $this->assets->enqueueLibraryStyle($handle, $script);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueLibraryStyleWithParameters() {
        $handle = 'phpunit-admin';
        $script = 'admin.css';
        $deps = [Assets::$HANDLE_REACT];
        $media = 'print';

        $this->assets->shouldReceive('enqueueLibraryStyle')->passthru();
        $this->assets->shouldReceive('enqueueStyle')->with($handle, $script, $deps, $media, true);

        $this->assets->enqueueLibraryStyle($handle, $script, $deps, $media);

        $this->addToAssertionCount(1);
    }

    public function testProbablyEnqueueComposerChunk() {
        $deps = ['jquery'];
        $this->assets
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('probablyEnqueueComposerChunk')
            ->passthru();
        $this->assets
            ->shouldReceive('enqueueComposer')
            ->once()
            ->with('utils', 'vendor~index.js', $deps, 'script', true, 'all', 'vendor~' . PHPUNIT_ROOT_SLUG . '-utils')
            ->andReturn('phpunit-vendor~admin');

        $method = new ReflectionMethod(AssetsImpl::class, 'probablyEnqueueComposerChunk');
        $method->setAccessible(true);
        $method->invokeArgs($this->assets, ['utils', 'index.js', &$deps, true, 'all']);

        $this->assertCount(2, $deps);
    }

    public function testProbablyEnqueueComposerChunkFailing() {
        $deps = ['jquery'];
        $this->assets
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('probablyEnqueueComposerChunk')
            ->passthru();
        $this->assets
            ->shouldReceive('enqueueComposer')
            ->once()
            ->andReturnFalse();

        $method = new ReflectionMethod(AssetsImpl::class, 'probablyEnqueueComposerChunk');
        $method->setAccessible(true);
        $method->invokeArgs($this->assets, ['utils', 'index.js', &$deps, true, 'all']);

        $this->assertCount(1, $deps);
    }

    // enqueueComposer start
    public function testEnqueueComposer() {
        $handle = 'utils';
        $should = PHPUNIT_ROOT_SLUG . '-' . $handle;
        $packageDir = 'vendor/' . PHPUNIT_ROOT_SLUG . '/' . $handle . '/';
        $packageSrc = 'vendor/phpunit-root/utils/dev/index.js';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/' . $packageSrc;
        $ts = time();
        $packageJsonVersion = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();
        $self = $this->expectCallbacksReached(['devDirExists', 'isLernaRepo']);

        redefine('is_dir', function($dir) use ($self, $packageDir) {
            $self->addCallbackReached('devDirExists', $dir === PHPUNIT_PATH . '/' . $packageDir . 'dev');
            return true;
        });

        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $packageSrc]]);
        WP_Mock::userFunction('path_join', [
            'times' => 1,
            'args' => [WP_CONTENT_DIR, 'packages/' . $handle . '/tsconfig.json']
        ]);

        redefine('file_exists', function () {
            if (!$this->hasCallbackReached('isLernaRepo')) {
                $this->addCallbackReached('isLernaRepo');
                return false;
            }
            return true;
        });

        WP_Mock::userFunction('plugins_url', ['args' => [$packageSrc, PHPUNIT_FILE], 'return' => $plugins_url]);
        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $packageDir . 'package.json']]);

        redefine('filemtime', always($ts));
        redefine('file_get_contents', always('{"version": "' . $packageJsonVersion . '"}'));

        $this->assets
            ->shouldReceive('probablyEnqueueComposerChunk')
            ->once()
            ->with($handle, 'index.js', [], true, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $packageJsonVersion, true]
        ]);
        WP_Mock::userFunction('path_join', [
            'times' => 1,
            'args' => [PHPUNIT_PATH, $packageDir . 'languages/frontend/json']
        ]);

        $this->assets
            ->shouldReceive('setLazyScriptTranslations')
            ->once()
            ->with($should, $should, null);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueueComposer');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle);

        $this->assertEquals($should, $actual);
        $this->assertCallbacksReached();
    }

    public function testEnqueueComposerDevFolderNotExists() {
        $handle = 'utils';
        $should = PHPUNIT_ROOT_SLUG . '-' . $handle;
        $packageDir = 'vendor/' . PHPUNIT_ROOT_SLUG . '/' . $handle . '/';
        $packageSrc = 'vendor/phpunit-root/utils/dist/index.js';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/' . $packageSrc;
        $ts = time();
        $packageJsonVersion = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();
        $self = $this->expectCallbacksReached(['devDirExists', 'isLernaRepo']);

        redefine('is_dir', function($dir) use ($self, $packageDir) {
            $self->addCallbackReached('devDirExists', $dir === PHPUNIT_PATH . '/' . $packageDir . 'dev');
            return false;
        });

        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $packageSrc]]);
        WP_Mock::userFunction('path_join', [
            'times' => 1,
            'args' => [WP_CONTENT_DIR, 'packages/' . $handle . '/tsconfig.json']
        ]);

        redefine('file_exists', function () {
            if (!$this->hasCallbackReached('isLernaRepo')) {
                $this->addCallbackReached('isLernaRepo');
                return false;
            }
            return true;
        });

        WP_Mock::userFunction('plugins_url', ['args' => [$packageSrc, PHPUNIT_FILE], 'return' => $plugins_url]);
        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $packageDir . 'package.json']]);

        redefine('filemtime', always($ts));
        redefine('file_get_contents', always('{"version": "' . $packageJsonVersion . '"}'));

        $this->assets
            ->shouldReceive('probablyEnqueueComposerChunk')
            ->once()
            ->with($handle, 'index.js', [], true, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $packageJsonVersion, true]
        ]);
        WP_Mock::userFunction('path_join', [
            'times' => 1,
            'args' => [PHPUNIT_PATH, $packageDir . 'languages/frontend/json']
        ]);

        $this->assets
            ->shouldReceive('setLazyScriptTranslations')
            ->once()
            ->with($should, $should, null);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueueComposer');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle);

        $this->assertEquals($should, $actual);
        $this->assertCallbacksReached();
    }

    public function testEnqueueComposerInLernaRepo() {
        $handle = 'utils';
        $should = PHPUNIT_ROOT_SLUG . '-' . $handle;
        $packageSrc = 'vendor/phpunit-root/utils/dev/index.js';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/' . $packageSrc;
        $ts = time();
        $packageJsonVersion = $ts;

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();

        redefine('is_dir', always(true));

        WP_Mock::userFunction('path_join');

        redefine('file_exists', always(true));

        WP_Mock::userFunction('plugins_url', ['return' => $plugins_url]);

        redefine('filemtime', always($ts));
        redefine('file_get_contents', always('{"version": "' . $packageJsonVersion . '"}'));

        $this->assets
            ->shouldReceive('probablyEnqueueComposerChunk')
            ->once()
            ->with($handle, 'index.js', [], true, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $packageJsonVersion, true]
        ]);

        $this->assets->shouldReceive('setLazyScriptTranslations');

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueueComposer');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueComposerWithNonExistingFile() {
        $handle = 'utils';
        $should = false;

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();

        redefine('is_dir', always(true));

        WP_Mock::userFunction('path_join');

        redefine('file_exists', always(false));

        WP_Mock::userFunction('plugins_url', ['times' => 0]);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueueComposer');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle);

        $this->assertEquals($should, $actual);
    }

    public function testEnqueueComposerWithStyle() {
        $handle = 'utils';
        $should = PHPUNIT_ROOT_SLUG . '-' . $handle;
        $packageDir = 'vendor/' . PHPUNIT_ROOT_SLUG . '/' . $handle . '/';
        $packageSrc = 'vendor/phpunit-root/utils/dev/index.css';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/' . $packageSrc;
        $ts = time();
        $packageJsonVersion = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();
        $this->expectCallbacksReached(['isLernaRepo']);

        redefine('is_dir', always(true));

        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $packageSrc]]);
        WP_Mock::userFunction('path_join', [
            'times' => 1,
            'args' => [WP_CONTENT_DIR, 'packages/' . $handle . '/tsconfig.json']
        ]);

        redefine('file_exists', function () {
            if (!$this->hasCallbackReached('isLernaRepo')) {
                $this->addCallbackReached('isLernaRepo');
                return false;
            }
            return true;
        });

        WP_Mock::userFunction('plugins_url', ['args' => [$packageSrc, PHPUNIT_FILE], 'return' => $plugins_url]);
        WP_Mock::userFunction('path_join', ['times' => 1, 'args' => [PHPUNIT_PATH, $packageDir . 'package.json']]);

        redefine('filemtime', always($ts));
        redefine('file_get_contents', always('{"version": "' . $packageJsonVersion . '"}'));

        WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 1,
            'args' => [$should, $plugins_url, [], $packageJsonVersion, 'all']
        ]);
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 0]);

        $this->assets->shouldNotReceive('setLazyScriptTranslations');

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueueComposer');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, 'index.css', [], 'style');

        $this->assertEquals($should, $actual);
        $this->assertCallbacksReached();
    }

    public function testEnqueueComposerWithStyleParameters() {
        $handle = 'utils';
        $src = 'another.css';
        $deps = [Assets::$HANDLE_REACT];
        $media = 'print';
        $should = PHPUNIT_ROOT_SLUG . '-' . $handle . '-another';
        $packageSrc = 'vendor/phpunit-root/utils/dev/another.css';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/' . $packageSrc;
        $ts = time();
        $packageJsonVersion = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();
        $this->expectCallbacksReached(['isLernaRepo']);

        redefine('is_dir', always(true));

        WP_Mock::userFunction('path_join');

        redefine('file_exists', function () {
            if (!$this->hasCallbackReached('isLernaRepo')) {
                $this->addCallbackReached('isLernaRepo');
                return false;
            }
            return true;
        });

        WP_Mock::userFunction('plugins_url', ['return' => $plugins_url]);

        redefine('filemtime', always($ts));
        redefine('file_get_contents', always('{"version": "' . $packageJsonVersion . '"}'));

        WP_Mock::userFunction('wp_enqueue_style', [
            'times' => 1,
            'args' => [$should, $plugins_url, $deps, $packageJsonVersion, $media]
        ]);

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueueComposer');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, $src, $deps, 'style', null, $media);

        $this->assertEquals($should, $actual);
        $this->assertCallbacksReached();
    }

    public function testEnqueueComposerWithScriptParameters() {
        $handle = 'utils';
        $src = 'another.js';
        $deps = [Assets::$HANDLE_REACT];
        $in_footer = false;
        $should = PHPUNIT_ROOT_SLUG . '-' . $handle . '-another';
        $packageSrc = 'vendor/phpunit-root/utils/dev/another.js';
        $plugins_url = 'http://localhost/wp-content/plugins/phpunit/' . $packageSrc;
        $ts = time();
        $packageJsonVersion = '1.0.0';

        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();
        $this->expectCallbacksReached(['isLernaRepo']);

        redefine('is_dir', always(true));

        WP_Mock::userFunction('path_join');

        redefine('file_exists', function () {
            if (!$this->hasCallbackReached('isLernaRepo')) {
                $this->addCallbackReached('isLernaRepo');
                return false;
            }
            return true;
        });

        WP_Mock::userFunction('plugins_url', ['return' => $plugins_url]);

        redefine('filemtime', always($ts));
        redefine('file_get_contents', always('{"version": "' . $packageJsonVersion . '"}'));

        $this->assets
            ->shouldReceive('probablyEnqueueComposerChunk')
            ->once()
            ->with($handle, $src, $deps, $in_footer, 'all');

        WP_Mock::userFunction('wp_enqueue_script', [
            'times' => 1,
            'args' => [$should, $plugins_url, $deps, $packageJsonVersion, $in_footer]
        ]);

        $this->assets->shouldReceive('setLazyScriptTranslations');

        $method = new ReflectionMethod(AssetsImpl::class, 'enqueueComposer');
        $method->setAccessible(true);
        $actual = $method->invoke($this->assets, $handle, $src, $deps, 'script', $in_footer);

        $this->assertEquals($should, $actual);
        $this->assertCallbacksReached();
    }
    // enqueueComposer end

    public function testEnqueueComposerScript() {
        $handle = 'utils';

        $this->assets->shouldReceive('enqueueComposerScript')->passthru();
        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->with($handle, 'index.js', [], 'script', true);

        $this->assets->enqueueComposerScript($handle);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueComposerScriptWithParameters() {
        $handle = 'utils';
        $deps = [Assets::$HANDLE_REACT];
        $src = 'utils.js';
        $in_footer = false;

        $this->assets->shouldReceive('enqueueComposerScript')->passthru();
        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->with($handle, $src, $deps, 'script', $in_footer);

        $this->assets->enqueueComposerScript($handle, $deps, $src, $in_footer);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueComposerStyle() {
        $handle = 'utils';

        $this->assets->shouldReceive('enqueueComposerStyle')->passthru();
        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->with($handle, 'index.css', [], 'style', null, 'all');

        $this->assets->enqueueComposerStyle($handle);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueComposerStyleWithParameters() {
        $handle = 'utils';
        $deps = [Assets::$HANDLE_REACT];
        $src = 'utils.css';
        $media = 'all';

        $this->assets->shouldReceive('enqueueComposerStyle')->passthru();
        $this->assets->shouldAllowMockingProtectedMethods();
        $this->assets->shouldReceive('enqueueComposer')->with($handle, $src, $deps, 'style', null, $media);

        $this->assets->enqueueComposerStyle($handle, $deps, $src, $media);

        $this->addToAssertionCount(1);
    }

    public function testAdminEnqueueScripts() {
        $hook_suffix = 'settings-page';
        $this->assets->shouldReceive('admin_enqueue_scripts')->passthru();
        $this->assets->shouldReceive('enqueue_scripts_and_styles')->with(Assets::$TYPE_ADMIN, $hook_suffix);

        $this->assets->admin_enqueue_scripts($hook_suffix);

        $this->addToAssertionCount(1);
    }

    public function testWpEnqueueScripts() {
        $this->assets->shouldReceive('wp_enqueue_scripts')->passthru();
        $this->assets->shouldReceive('enqueue_scripts_and_styles')->with(Assets::$TYPE_FRONTEND);

        $this->assets->wp_enqueue_scripts();

        $this->addToAssertionCount(1);
    }

    public function testSetLazyScriptTranslations() {
        $path = PHPUNIT_PATH . '/public/languages/json';
        $script = <<<JS
(function(domain, translations) {
    var localeData = translations.locale_data[domain] || translations.locale_data.messages;
    localeData[""].domain = domain;
    window.wpi18nLazy=window.wpi18nLazy || {};
    window.wpi18nLazy[domain] = window.wpi18nLazy[domain] || [];
    window.wpi18nLazy[domain].push(localeData);
})("phpunit", []);
JS;

        $this->assets->shouldReceive('setLazyScriptTranslations')->passthru();

        WP_Mock::userFunction('load_script_textdomain', [
            'times' => 1,
            'args' => [PHPUNIT_SLUG, PHPUNIT_TD, $path],
            'return' => '[]'
        ]);
        WP_Mock::userFunction('wp_add_inline_script', ['times' => 1, 'args' => [PHPUNIT_SLUG, $script, 'before']]);

        $this->assets->setLazyScriptTranslations(PHPUNIT_SLUG, PHPUNIT_TD, $path);

        $this->addToAssertionCount(1);
    }

    public function testSetLazyScriptTranslationsNoJson() {
        $path = PHPUNIT_PATH . '/public/languages/json';

        $this->assets->shouldReceive('setLazyScriptTranslations')->passthru();

        WP_Mock::userFunction('load_script_textdomain', ['times' => 1, 'return' => false]);
        WP_Mock::userFunction('wp_add_inline_script', ['times' => 0]);

        $this->assets->setLazyScriptTranslations(PHPUNIT_SLUG, PHPUNIT_TD, $path);

        $this->addToAssertionCount(1);
    }

    public function testGetCachebusterVersion() {
        $script = 'public/dev/admin.js';
        $version = '1.0.5';
        $should = $version;

        $this->assets->shouldReceive('getCachebusterVersion')->passthru();

        redefine('file_exists', always(true));
        redefine('include', always(['src/public/dev/admin.js' => $version]));

        $actual = $this->assets->getCachebusterVersion($script);

        $this->assertEquals($should, $actual);
    }

    public function testGetCachebusterVersionLibrary() {
        $script = 'public/lib/mobx/dist/mobx.min.js';
        $version = '1.0.5';
        $should = $version;

        $this->assets->shouldReceive('getCachebusterVersion')->passthru();

        redefine('file_exists', always(true));
        redefine('include', always(['mobx' => $version]));

        $actual = $this->assets->getCachebusterVersion($script, true);

        $this->assertEquals($should, $actual);
    }

    public function testGetCachebusterVersionDefault() {
        $script = 'public/dev/admin.js';
        $should = PHPUNIT_VERSION;

        $this->assets->shouldReceive('getCachebusterVersion')->passthru();

        redefine('file_exists', always(false));

        $actual = $this->assets->getCachebusterVersion($script);

        $this->assertEquals($should, $actual);
    }

    public function testGetPluginsUrl() {
        $script = 'admin.js';
        $getPublicFolder = 'public/dev/';

        $this->assets->shouldReceive('getPluginsUrl')->passthru();
        $this->assets
            ->shouldReceive('getPublicFolder')
            ->with(false)
            ->andReturn($getPublicFolder);

        WP_Mock::userFunction('plugins_url', ['args' => [$getPublicFolder . $script, PHPUNIT_FILE]]);

        $this->assets->getPluginsUrl($script);

        $this->addToAssertionCount(1);
    }

    public function testGetPluginsUrlLibrary() {
        $script = 'admin.js';
        $getPublicFolder = 'public/lib/';

        $this->assets->shouldReceive('getPluginsUrl')->passthru();
        $this->assets
            ->shouldReceive('getPublicFolder')
            ->with(true)
            ->andReturn($getPublicFolder);

        WP_Mock::userFunction('plugins_url', ['args' => [$getPublicFolder . $script, PHPUNIT_FILE]]);

        $this->assets->getPluginsUrl($script, true);

        $this->addToAssertionCount(1);
    }

    public function testGetPublicFolderDev() {
        $should = 'public/dev/';

        $this->assets->shouldReceive('getPublicFolder')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();

        redefine('is_dir', always(true));

        $actual = $this->assets->getPublicFolder();

        $this->assertEquals($should, $actual);
    }

    public function testGetPublicFolderDevButDoesNotExist() {
        $should = 'public/dist/';

        $this->assets->shouldReceive('getPublicFolder')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnTrue();

        redefine('is_dir', always(false));

        $actual = $this->assets->getPublicFolder();

        $this->assertEquals($should, $actual);
    }

    public function testGetPublicFolderDist() {
        $should = 'public/dist/';

        $this->assets->shouldReceive('getPublicFolder')->passthru();
        $this->assets->shouldReceive('useNonMinifiedSources')->andReturnFalse();

        $actual = $this->assets->getPublicFolder();

        $this->assertEquals($should, $actual);
    }

    public function testGetPublicFolderLib() {
        $should = 'public/lib/';

        $this->assets->shouldReceive('getPublicFolder')->passthru();
        $this->assets->shouldNotReceive('useNonMinifiedSources');

        $actual = $this->assets->getPublicFolder(true);

        $this->assertEquals($should, $actual);
    }

    public function testUseNonMinifiedSources() {
        $this->assets->shouldReceive('useNonMinifiedSources')->passthru();

        redefine('defined', always(true));
        redefine('constant', always(true));

        $actual = $this->assets->useNonMinifiedSources();

        $this->assertTrue($actual);
    }

    public function testUseNonMinifiedSourcesFalse() {
        $this->assets->shouldReceive('useNonMinifiedSources')->passthru();

        redefine('defined', always(false));

        $actual = $this->assets->useNonMinifiedSources();

        $this->assertFalse($actual);
    }

    public function testIsScreenBaseNonExistingFunction() {
        $this->assets->shouldReceive('isScreenBase')->passthru();

        $actual = $this->assets->isScreenBase('edit');

        $this->assertFalse($actual);
    }

    public function testIsScreenBase() {
        $this->assets->shouldReceive('isScreenBase')->passthru();
        WP_Mock::userFunction('get_current_screen', ['return' => (object) ['base' => 'edit']]);

        $actual = $this->assets->isScreenBase('edit');

        $this->assertTrue($actual);
    }

    public function testIsScreenBaseFalse() {
        $this->assets->shouldReceive('isScreenBase')->passthru();
        WP_Mock::userFunction('get_current_screen', ['return' => (object) ['base' => 'post']]);

        $actual = $this->assets->isScreenBase('edit');

        $this->assertFalse($actual);
    }

    public function testIsScreenBaseNoScreen() {
        $this->assets->shouldReceive('isScreenBase')->passthru();
        WP_Mock::userFunction('get_current_screen', ['return' => null]);

        $actual = $this->assets->isScreenBase('edit');

        $this->assertFalse($actual);
    }
}
