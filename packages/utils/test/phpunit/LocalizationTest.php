<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use MatthiasWeb\Utils\Assets;
use MatthiasWeb\Utils\Localization;
use MatthiasWeb\Utils\PluginReceiver;
use Mockery;
use Mockery\MockInterface;
use TestCaseUtils;
use WP_Mock;
use WP_Mock\Tools\TestCase;

use function Patchwork\always;
use function Patchwork\redefine;

if (!class_exists(__NAMESPACE__ . '\\LocalizationImpl')) {
    class LocalizationImpl {
        use Localization;
        use PluginReceiver;

        public function override($locale) {
            // Silence is golden.
        }

        public function getPackageInfo($type) {
            // Silence is golden.
        }
    }
}

final class LocalizationTest extends TestCase {
    use TestCaseUtils;

    /** @var MockInterface|LocalizationImpl */
    private $localization;

    public function setUp(): void {
        parent::setUp();
        $this->localization = Mockery::mock(LocalizationImpl::class);
        $this->localization->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');
    }

    public function testPackageInfoParserForOverridesBackend() {
        $domain = 'phpunit';
        $packageInfo = ['/var/www/html/wp-content/plugins/phpunit/languages', $domain];
        $resolve = '/var/www/html/wp-content/plugins/phpunit/languages/phpunit-de_DE_formal.mo';
        $should = [$resolve, $packageInfo[0], $domain, $domain];

        $this->localization->shouldReceive('packageInfoParserForOverrides')->passthru();
        $this->localization
            ->shouldReceive('getPackageInfo')
            ->with(Localization::$PACKAGE_INFO_BACKEND)
            ->andReturn($packageInfo);
        $this->localization
            ->shouldReceive('resolveSymlinks')
            ->once()
            ->andReturn($resolve);
        $this->localization
            ->shouldReceive('resolveSymlinks')
            ->once()
            ->andReturn($packageInfo[0]);

        $actual = $this->localization->packageInfoParserForOverrides(
            Localization::$PACKAGE_INFO_BACKEND,
            $resolve,
            $domain
        );
        $this->assertEquals($should, $actual);
    }

    public function testPackageInfoParserForOverridesWithFilePrefix() {
        $domain = 'phpunit';
        $filePrefix = 'another';
        $packageInfo = ['/var/www/html/wp-content/plugins/phpunit/languages', $domain, $filePrefix];
        $resolve = '/var/www/html/wp-content/plugins/phpunit/languages/phpunit-de_DE_formal.mo';
        $should = [$resolve, $packageInfo[0], $domain, $filePrefix];

        $this->localization->shouldReceive('packageInfoParserForOverrides')->passthru();
        $this->localization->shouldReceive('getPackageInfo')->andReturn($packageInfo);
        $this->localization
            ->shouldReceive('resolveSymlinks')
            ->once()
            ->andReturn($resolve);
        $this->localization
            ->shouldReceive('resolveSymlinks')
            ->once()
            ->andReturn($packageInfo[0]);

        $actual = $this->localization->packageInfoParserForOverrides(
            Localization::$PACKAGE_INFO_BACKEND,
            $resolve,
            $domain
        );
        $this->assertEquals($should, $actual);
    }

    public function testLoadScriptTranslationFile() {
        $file = '/var/www/html/wp-content/plugins/phpunit/public/languages/json/phpunit-en_US-phpunit-admin.json';
        $path = '/var/www/html/wp-content/plugins/phpunit/public/languages/json';
        $domain = PHPUNIT_TD;
        $parsedPackageInfo = [$file, $path, $domain, $domain];
        $handle = 'phpunit-admin';
        $currentLocale = 'en_US';

        $this->localization->shouldReceive('load_script_translation_file')->passthru();
        $this->localization
            ->shouldReceive('packageInfoParserForOverrides')
            ->with(Localization::$PACKAGE_INFO_FRONTEND, $file, $domain)
            ->andReturn($parsedPackageInfo);

        $this->localization
            ->shouldReceive('getLanguageFromFile')
            ->once()
            ->with($file)
            ->andReturn($currentLocale);

        WP_Mock::userFunction('path_join', ['return' => $path, 'args' => [PHPUNIT_PATH, Mockery::any()]]);

        redefine('is_readable', always(false));

        $this->localization
            ->shouldReceive('override')
            ->once()
            ->with($currentLocale)
            ->andReturn('de_DE');

        WP_Mock::userFunction('wp_scripts', ['return' => new WP_Scripts()]);
        WP_Mock::userFunction('path_join', [
            'args' => [$path, 'phpunit-de_DE-d6c7c71371fa4fbe7cc75f0a20f23d0e.json']
        ]);

        $this->localization->load_script_translation_file($file, $handle, $domain);

        $this->addToAssertionCount(1);
    }

    public function testLoadScriptTranslationFileNoMatchingLanguage() {
        $file = '/var/www/html/wp-content/plugins/phpunit/public/languages/json/phpunit--phpunit-admin.json';
        $path = '/var/www/html/wp-content/plugins/phpunit/public/languages/json';
        $domain = PHPUNIT_TD;
        $parsedPackageInfo = [$file, $path, $domain, $domain];
        $handle = 'phpunit-admin';

        $this->localization->shouldReceive('load_script_translation_file')->passthru();
        $this->localization
            ->shouldReceive('packageInfoParserForOverrides')
            ->with(Localization::$PACKAGE_INFO_FRONTEND, $file, $domain)
            ->andReturn($parsedPackageInfo);

        $this->localization
            ->shouldReceive('getLanguageFromFile')
            ->once()
            ->with($file)
            ->andReturn(false);
        $this->localization->shouldNotReceive('override');

        $actual = $this->localization->load_script_translation_file($file, $handle, $domain);

        $this->assertEquals($file, $actual);
    }

    public function testLoadScriptTranslationFileNotMatchingDomain() {
        $file = '/var/www/html/wp-content/plugins/phpunit/public/languages/json/phpunit-en_US-phpunit-admin.json';
        $path = '/var/www/html/wp-content/plugins/phpunit/public/languages/json';
        $domain = PHPUNIT_TD;
        $anotherDomain = 'another-domain';
        $parsedPackageInfo = [$file, $path, $domain, $domain];
        $handle = 'phpunit-admin';
        $currentLocale = 'en_US';

        $this->localization->shouldReceive('load_script_translation_file')->passthru();
        $this->localization
            ->shouldReceive('packageInfoParserForOverrides')
            ->with(Localization::$PACKAGE_INFO_FRONTEND, $file, $anotherDomain)
            ->andReturn($parsedPackageInfo);

        WP_Mock::userFunction('path_join', ['return' => $path, 'args' => [PHPUNIT_PATH, Mockery::any()]]);

        $this->localization->shouldNotReceive('override');

        $actual = $this->localization->load_script_translation_file($file, $handle, $anotherDomain);

        $this->assertEquals($file, $actual);
    }

    public function testLoadScriptTranslationFileNotMatchingPlugin() {
        $file = '/var/www/html/wp-content/plugins/foo/public/languages/json/foo-en_US-phpunit-admin.json';
        $handle = 'phpunit-admin';
        $domain = PHPUNIT_TD;
        $currentLocale = 'en_CA';
        $path = '/var/www/html/wp-content/plugins/phpunit/public/languages/json';
        $parsedPackageInfo = [$file, $path, $domain, $domain];

        $this->localization->shouldReceive('load_script_translation_file')->passthru();
        $this->localization
            ->shouldReceive('packageInfoParserForOverrides')
            ->with(Localization::$PACKAGE_INFO_FRONTEND, $file, $domain)
            ->andReturn($parsedPackageInfo);

        WP_Mock::userFunction('path_join', [
            'return' => $path,
            'args' => [PHPUNIT_PATH, Assets::$PUBLIC_JSON_I18N]
        ]);

        redefine('is_readable', always(false));

        $this->localization->shouldNotReceive('override');

        $actual = $this->localization->load_script_translation_file($file, $handle, $domain);

        $this->assertEquals($file, $actual);
    }

    public function testGetLanguageFromFile() {
        $should = 'en_US';
        $file = '/var/www/html/wp-content/plugins/phpunit/public/languages/json/phpunit-en_US-phpunit-admin.json';

        $this->localization->shouldReceive('getLanguageFromFile')->passthru();

        WP_Mock::userFunction('get_available_languages', ['times' => 1, 'return' => ['de_DE', 'de_DE_formal']]);

        $actual = $this->localization->getLanguageFromFile($file);

        $this->assertEquals($should, $actual);
    }

    public function testGetLanguageFromFileNoMatch() {
        $file = '/var/www/html/wp-content/plugins/phpunit/public/languages/json/phpunit--phpunit-admin.json';

        $this->localization->shouldReceive('getLanguageFromFile')->passthru();

        WP_Mock::userFunction('get_available_languages', ['times' => 1, 'return' => ['de_DE', 'de_DE_formal']]);

        $actual = $this->localization->getLanguageFromFile($file);

        $this->assertFalse($actual);
    }

    public function testGetLanguageFromFileNoInstalledLanguages() {
        $should = 'en_US';
        $file = '/var/www/html/wp-content/plugins/phpunit/public/languages/json/phpunit-en_US-phpunit-admin.json';

        $this->localization->shouldReceive('getLanguageFromFile')->passthru();

        WP_Mock::userFunction('get_available_languages', ['times' => 1, 'return' => []]);

        $actual = $this->localization->getLanguageFromFile($file);

        $this->assertEquals($should, $actual);
    }

    public function testOverrideLoadTextdomain() {
        $mofile = '/var/www/html/wp-content/plugins/phpunit/languages/phpunit-en_US.mo';
        $domain = PHPUNIT_TD;
        $path = '/var/www/html/wp-content/plugins/phpunit/languages';
        $parsedPackageInfo = [$mofile, $path, $domain, $domain];

        $this->localization->shouldReceive('override_load_textdomain')->passthru();
        $this->localization
            ->shouldReceive('packageInfoParserForOverrides')
            ->with(Localization::$PACKAGE_INFO_BACKEND, $mofile, $domain)
            ->andReturn($parsedPackageInfo);

        WP_Mock::userFunction('path_join', ['return' => $path, 'args' => [PHPUNIT_PATH, 'languages']]);

        redefine('is_readable', function ($file) use ($mofile) {
            return $file !== $mofile;
        });

        $this->localization
            ->shouldReceive('override')
            ->once()
            ->with('en_US')
            ->andReturn('de_DE');

        WP_Mock::userFunction('path_join', [
            'args' => [Mockery::any(), $domain . '-de_DE.mo']
        ]);
        WP_Mock::userFunction('load_textdomain', ['args' => [$domain, Mockery::any()]]);

        $actual = $this->localization->override_load_textdomain(false, $domain, $mofile);

        $this->assertTrue($actual);
    }

    public function testOverrideLoadTextdomainNotMatchingDomainAfterOverriden() {
        $mofile = '/var/www/html/wp-content/plugins/phpunit/languages/phpunit-en_US.mo';
        $domain = 'another-domain';
        $path = '/var/www/html/wp-content/plugins/phpunit/languages';
        $parsedPackageInfo = [$mofile, $path, $domain, $domain];
        $anotherDomain = 'another-domain';

        $this->localization->shouldReceive('override_load_textdomain')->passthru();
        $this->localization
            ->shouldReceive('packageInfoParserForOverrides')
            ->with(Localization::$PACKAGE_INFO_BACKEND, $mofile, $anotherDomain)
            ->andReturn($parsedPackageInfo);

        WP_Mock::userFunction('path_join', ['return' => $path, 'args' => [dirname($mofile), 'another-domain-.mo']]);

        redefine('is_readable', function ($file) use ($mofile) {
            return $file === $mofile;
        });

        $this->localization->shouldNotReceive('override');

        $actual = $this->localization->override_load_textdomain(true, $domain, $mofile);

        $this->assertTrue($actual);
    }

    public function testOverrideLoadTextdomainNotMatchingPlugin() {
        $domain = 'another-domain';
        $mofile = '/var/www/html/wp-content/plugins/foo/languages/phpunit-en_US.mo';
        $path = '/var/www/html/wp-content/plugins/phpunit/languages';
        $parsedPackageInfo = [$mofile, $path, $domain, $domain];

        $this->localization->shouldReceive('override_load_textdomain')->passthru();
        $this->localization
            ->shouldReceive('packageInfoParserForOverrides')
            ->with(Localization::$PACKAGE_INFO_BACKEND, $mofile, $domain)
            ->andReturn($parsedPackageInfo);

        WP_Mock::userFunction('path_join', ['return' => $path, 'args' => [PHPUNIT_PATH, 'languages']]);

        redefine('is_readable', function ($file) use ($mofile) {
            return $file !== $mofile;
        });

        $this->localization->shouldNotReceive('override');

        $actual = $this->localization->override_load_textdomain(false, $domain, $mofile);

        $this->assertFalse($actual);
    }

    public function testHooks() {
        $this->localization->shouldReceive('hooks')->passthru();

        WP_Mock::expectFilterAdded(
            'override_load_textdomain',
            [$this->localization, 'override_load_textdomain'],
            10,
            3
        );
        WP_Mock::expectFilterAdded(
            'load_script_translation_file',
            [$this->localization, 'load_script_translation_file'],
            10,
            3
        );

        $this->localization->hooks();
        $this->assertHooksAdded();
    }

    public function testResolveSymlinks() {
        $should = __FILE__;

        $this->expectCallbacksReached(['realpath']);
        $this->localization->shouldReceive('resolveSymlinks')->passthru();
        redefine('realpath', function ($path) {
            $this->addCallbackReached('realpath');
            return $path;
        });

        $actual = $this->localization->resolveSymlinks(__FILE__);

        $this->assertEquals($should, $actual);
        $this->assertCallbacksReached();
    }
}
