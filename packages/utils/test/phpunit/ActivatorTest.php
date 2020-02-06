<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use MatthiasWeb\Utils\Activator;
use MatthiasWeb\Utils\PluginReceiver;
use Mockery;
use Mockery\MockInterface;
use TestCaseUtils;
use WP_Mock;
use WP_Mock\Tools\TestCase;
use wpdb;

use function Patchwork\redefine;

if (!class_exists(__NAMESPACE__ . '\\ActivatorImpl')) {
    class ActivatorImpl {
        use Activator;
        use PluginReceiver;

        public function dbDelta($errorlevel) {
            // Silence is golden.
        }
    }
}

final class ActivatorTest extends TestCase {
    use TestCaseUtils;

    /** @var MockInterface|ActivatorImpl */
    private $activator;

    public function setUp(): void {
        parent::setUp();
        $this->activator = Mockery::mock(ActivatorImpl::class);
        $this->activator->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');
    }

    public function testInstall() {
        global $wpdb;

        /** @var MockInterface|wpdb */
        $wpdb = $wpdb;

        $this->expectCallbacksReached(['disableErrorReporting', 'restoreErrorReporting']);

        $this->activator->shouldReceive('install')->passthru();
        $wpdb
            ->shouldReceive('show_errors', 'suppress_errors')
            ->once()
            ->with(false)
            ->andReturn(1);

        redefine('error_reporting', function ($level = null) {
            if ($level === 0) {
                $this->addCallbackReached('disableErrorReporting');
                return 1;
            } elseif ($level === 1) {
                $this->addCallbackReached('restoreErrorReporting');
                return 0;
            }
        });

        $this->activator->shouldReceive('dbDelta')->once();

        $wpdb
            ->shouldReceive('show_errors', 'suppress_errors')
            ->once()
            ->with(1);

        WP_Mock::userFunction('update_option', [
            'args' => [PHPUNIT_OPT_PREFIX . '_db_version', PHPUNIT_VERSION]
        ]);

        $this->activator->install();

        $this->assertCallbacksReached();
    }

    public function testInstallWithErrorLevel() {
        global $wpdb;

        /** @var MockInterface|wpdb */
        $wpdb = $wpdb;

        $this->activator->shouldReceive('install')->passthru();
        $this->activator->shouldReceive('dbDelta')->once();

        $wpdb->shouldNotReceive('show_errors', 'suppress_errors');

        WP_Mock::userFunction('update_option', [
            'args' => [PHPUNIT_OPT_PREFIX . '_db_version', PHPUNIT_VERSION]
        ]);

        $this->activator->install(true);

        $this->addToAssertionCount(1);
    }

    public function testInstallWithOwnCallable() {
        $callbackToReceive = 'test';

        $this->expectCallbacksReached(['customCallback']);

        $this->activator->shouldReceive('install')->passthru();
        $this->activator->shouldNotReceive('dbDelta');

        redefine('call_user_func', function ($callback) use ($callbackToReceive) {
            $this->addCallbackReached('customCallback', $callbackToReceive === $callback);
        });

        WP_Mock::userFunction('update_option', [
            'times' => 0
        ]);

        $this->activator->install(false, $callbackToReceive);

        $this->assertCallbacksReached();
    }
}
