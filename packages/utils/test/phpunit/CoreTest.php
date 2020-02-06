<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use MatthiasWeb\Utils\Core;
use MatthiasWeb\Utils\PackageLocalization;
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

if (!class_exists(__NAMESPACE__ . '\\CoreImpl')) {
    class CoreImpl {
        use Core;
        use PluginReceiver;
    }
}

final class CoreTest extends TestCase {
    use TestCaseUtils;

    /** @var MockInterface|CoreImpl */
    private $core;

    public function setUp(): void {
        parent::setUp();

        $this->core = Mockery::mock(CoreImpl::class);
        $this->core->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');
    }

    public function testConstruct() {
        $this->core->shouldAllowMockingProtectedMethods();
        $this->core->shouldReceive('construct')->passthru();

        $this->expectCallbacksReached(['packageLocalizationInstance']);

        /** @var MockInterface|ActivatorImpl */
        $mockActivator = Mockery::mock(ActivatorImpl::class);
        $this->core
            ->shouldReceive('getPluginClassInstance')
            ->with(PluginReceiver::$PLUGIN_CLASS_ACTIVATOR)
            ->andReturn($mockActivator);
        $this->core->shouldReceive('getActivator')->andReturn($mockActivator);

        /** @var MockInterface|AssetsImpl */
        $mockAssets = Mockery::mock(AssetsImpl::class);
        $this->core
            ->shouldReceive('getPluginClassInstance')
            ->with(PluginReceiver::$PLUGIN_CLASS_ASSETS)
            ->andReturn($mockAssets);
        $this->core->shouldReceive('getAssets')->andReturn($mockAssets);

        /** @var MockInterface|Service */
        $mockService = Mockery::mock();
        redefine(Service::class . '::instance', always($mockService));
        $this->core->shouldReceive('getService')->andReturn($mockService);

        WP_Mock::expectActionAdded('plugins_loaded', [$this->core, 'i18n']);
        WP_Mock::expectActionAdded('plugins_loaded', [$this->core, 'updateDbCheck']);
        WP_Mock::expectActionAdded('init', [$this->core, 'init']);
        WP_Mock::expectActionAdded('rest_api_init', [$mockService, 'rest_api_init']);

        /** @var MockInterface|LocalizationImpl */
        $mockLocalization = Mockery::mock(LocalizationImpl::class);
        $this->core
            ->shouldReceive('getPluginClassInstance')
            ->with(PluginReceiver::$PLUGIN_CLASS_LOCALIZATION)
            ->andReturn($mockLocalization);
        $mockLocalization->shouldReceive('hooks');

        /** @var MockInterface|LocalizationImpl */
        $mockLocalizationPackage = Mockery::mock(LocalizationImpl::class);
        redefine(PackageLocalization::class . '::instance', function ($rootSlug, $packageDir) use (
            $mockLocalizationPackage
        ) {
            $this->addCallbackReached(
                'packageLocalizationInstance',
                $rootSlug === PHPUNIT_ROOT_SLUG && $packageDir === dirname(dirname(__DIR__))
            );
            return $mockLocalizationPackage;
        });
        $mockLocalizationPackage->shouldReceive('hooks');

        WP_Mock::userFunction('register_activation_hook', [
            'times' => 1,
            'args' => [PHPUNIT_FILE, [$mockActivator, 'activate']]
        ]);
        WP_Mock::userFunction('register_deactivation_hook', [
            'times' => 1,
            'args' => [PHPUNIT_FILE, [$mockActivator, 'deactivate']]
        ]);

        $sut = new ReflectionMethod(get_class($this->core), 'construct');
        $sut->setAccessible(true);
        $sut->invoke($this->core);

        $this->assertHooksAdded();
        $this->assertCallbacksReached();
    }

    public function testI18n() {
        $locale = 'de_DE';
        $textdomain = PHPUNIT_ROOT_SLUG . '-utils';

        $this->core->shouldReceive('i18n')->passthru();

        $this->core
            ->shouldReceive('getPluginData')
            ->with('DomainPath')
            ->andReturn('/languages');

        WP_Mock::userFunction('plugin_basename', [
            'args' => __DIR__ . '.bootstrap.php'
        ]);

        WP_Mock::userFunction('load_plugin_textdomain', [
            'args' => [PHPUNIT_TD, false, '/languages']
        ]);

        $this->core->shouldReceive('getInternalPackages')->andReturn(['utils' => 'packages/utils']);

        WP_Mock::userFunction('determine_locale', ['return' => $locale]);
        WP_Mock::expectFilter('plugin_locale', $locale, $textdomain);
        WP_Mock::userFunction('load_textdomain', ['args' => [$textdomain, 'packages/utils-de_DE.mo']]);

        $this->core->i18n();

        $this->addToAssertionCount(1);
    }

    public function testUpdateDbCheck() {
        $this->core->shouldReceive('updateDbCheck')->passthru();
        $this->core->shouldNotReceive('debug');

        WP_Mock::userFunction('get_option', [
            'args' => [PHPUNIT_OPT_PREFIX . '_db_version'],
            'return' => PHPUNIT_VERSION
        ]);

        $this->core->updateDbCheck();

        $this->addToAssertionCount(1);
    }

    public function testGetInternalPackages() {
        $should = ['some' => 'languages/some.pot', 'some2' => 'languages/some2.pot'];
        $this->core->shouldReceive('getInternalPackages')->passthru();

        WP_Mock::userFunction('path_join', PHPUNIT_PATH, 'vendor/' . PHPUNIT_ROOT_SLUG . '/*/languages/backend/*.pot');

        redefine('glob', always(['languages/some.pot', 'languages/some2.pot']));

        $actual = $this->core->getInternalPackages();

        $this->assertEquals($should, $actual);
    }

    public function testUpdateDbCheckInstall() {
        $this->core->shouldReceive('updateDbCheck')->passthru();
        $this->core->shouldReceive('debug')->once();

        WP_Mock::userFunction('get_option', [
            'args' => [PHPUNIT_OPT_PREFIX . '_db_version'],
            'return' => '0.0.0'
        ]);

        /** @var MockInterface|ActivatorImpl */
        $mockActivator = Mockery::mock(ActivatorImpl::class);
        $this->core->shouldReceive('getActivator')->andReturn($mockActivator);
        $mockActivator->shouldReceive('install')->once();

        $this->core->updateDbCheck();

        $this->addToAssertionCount(1);
    }

    public function testPluginData() {
        $should = ['Name' => 'My PHP Unit plugin'];

        $this->core->shouldReceive('getPluginData')->passthru();
        WP_Mock::userFunction('get_plugin_data', [
            'times' => 1,
            'return' => $should,
            'args' => [PHPUNIT_FILE, true, false]
        ]);

        // Call twice due to the fact we are using caching mechanism
        $this->assertEquals($should, $this->core->getPluginData());
        $this->assertEquals($should, $this->core->getPluginData());
    }

    public function testPluginDataWithInvalidKey() {
        $should = ['Name' => 'My PHP Unit plugin'];

        $this->core->shouldReceive('getPluginData')->passthru();
        WP_Mock::userFunction('get_plugin_data', [
            'times' => 1,
            'return' => $should,
            'args' => [PHPUNIT_FILE, true, false]
        ]);

        $actual = $this->core->getPluginData('Another Key');

        $this->assertNull($actual);
    }

    public function testPluginDataWithValidKey() {
        $should = ['Name' => 'My PHP Unit plugin'];

        $this->core->shouldReceive('getPluginData')->passthru();
        WP_Mock::userFunction('get_plugin_data', [
            'times' => 1,
            'return' => $should,
            'args' => [PHPUNIT_FILE, true, false]
        ]);

        $actual = $this->core->getPluginData('Name');

        $this->assertEquals($should['Name'], $actual);
    }
}
