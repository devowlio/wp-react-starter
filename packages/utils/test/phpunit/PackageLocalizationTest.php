<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use MatthiasWeb\Utils\Localization;
use MatthiasWeb\Utils\PackageLocalization;
use Mockery;
use Mockery\MockInterface;
use ReflectionMethod;
use WP_Mock;
use WP_Mock\Tools\TestCase;

use function Patchwork\always;
use function Patchwork\redefine;

final class PackageLocalizationTest extends TestCase {
    /** @var MockInterface|PackageLocalization */
    private $packageLocalization;

    public function setUp(): void {
        parent::setUp();
        $this->packageLocalization = Mockery::mock(PackageLocalization::class);
        $this->packageLocalization->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');
    }

    public function testOverride() {
        $locale = 'de_DE';
        $should = 'de_DE';

        $this->packageLocalization
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('override')
            ->passthru();

        $method = new ReflectionMethod(PackageLocalization::class, 'override');
        $method->setAccessible(true);
        $actual = $method->invoke($this->packageLocalization, $locale);

        $this->assertEquals($should, $actual);
    }

    public function testOverrideDeFormal() {
        $locale = 'de_DE_formal';
        $should = 'de_DE';

        $this->packageLocalization
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('override')
            ->passthru();

        $method = new ReflectionMethod(PackageLocalization::class, 'override');
        $method->setAccessible(true);
        $actual = $method->invoke($this->packageLocalization, $locale);

        $this->assertEquals($should, $actual);
    }

    public function testGetPackageInfoBackend() {
        $rootSlug = PHPUNIT_ROOT_SLUG;
        $package = 'utils';
        $textdomain = $rootSlug . '-' . $package;
        $package = 'utils';
        $packageDir = dirname(dirname(__DIR__));
        $should = [null, $textdomain, $package];

        $this->packageLocalization->shouldReceive('getRootSlug')->andReturn($rootSlug);
        $this->packageLocalization->shouldReceive('getPackage')->andReturn($package);
        $this->packageLocalization->shouldReceive('getPackageDir')->andReturn($packageDir);

        WP_Mock::userFunction('path_join', [
            'args' => [$packageDir, 'languages/backend'],
            'return' => null
        ]);

        $method = new ReflectionMethod(PackageLocalization::class, 'getPackageInfo');
        $method->setAccessible(true);
        $actual = $method->invoke($this->packageLocalization, Localization::$PACKAGE_INFO_BACKEND);

        $this->assertEquals($should, $actual);
    }

    public function testGetPackageInfoFrontend() {
        $rootSlug = PHPUNIT_ROOT_SLUG;
        $package = 'utils';
        $textdomain = $rootSlug . '-' . $package;
        $package = 'utils';
        $packageDir = dirname(dirname(__DIR__));
        $should = [null, $textdomain, $package];

        $this->packageLocalization->shouldReceive('getRootSlug')->andReturn($rootSlug);
        $this->packageLocalization->shouldReceive('getPackage')->andReturn($package);
        $this->packageLocalization->shouldReceive('getPackageDir')->andReturn($packageDir);

        WP_Mock::userFunction('path_join', [
            'args' => [$packageDir, 'languages/frontend/json'],
            'return' => null
        ]);

        $method = new ReflectionMethod(PackageLocalization::class, 'getPackageInfo');
        $method->setAccessible(true);
        $actual = $method->invoke($this->packageLocalization, Localization::$PACKAGE_INFO_FRONTEND);

        $this->assertEquals($should, $actual);
    }

    public function testGetPackage() {
        $should = 'utils';
        $packageDir = dirname(dirname(__DIR__));

        $this->packageLocalization->shouldReceive('getPackage')->passthru();
        $this->packageLocalization->shouldReceive('getPackageDir')->andReturn($packageDir);

        redefine('basename', always('utils'));

        $actual = $this->packageLocalization->getPackage();
        $this->assertEquals($should, $actual);
    }
}
