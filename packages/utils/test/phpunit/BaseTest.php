<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use MatthiasWeb\Utils\Base;
use MatthiasWeb\Utils\PluginReceiver;
use Mockery;
use Mockery\MockInterface;
use TestCaseUtils;
use WP_Mock\Tools\TestCase;

use function Patchwork\always;
use function Patchwork\redefine;

if (!class_exists(__NAMESPACE__ . '\\BaseImpl')) {
    class BaseImpl {
        use Base;
    }
}

final class BaseTest extends TestCase {
    use TestCaseUtils;

    /** @var MockInterface|BaseImpl */
    private $base;

    public function setUp(): void {
        parent::setUp();
        $this->base = Mockery::mock(BaseImpl::class);
    }

    public function testDebug() {
        $should = '';

        $this->base->shouldReceive('debug')->passthru();
        $this->base
            ->shouldReceive('getPluginConstant')
            ->with(PluginReceiver::$PLUGIN_CONST_DEBUG)
            ->andReturnFalse();

        $actual = $this->base->debug('Hello World');

        $this->assertEquals($should, $actual);
    }

    public function testDebugWithConstant() {
        $should = CONSTANT_PREFIX . '_DEBUG : Hello World';

        $this->expectCallbacksReached(['errorLog']);

        $this->base->shouldReceive('debug')->passthru();
        $this->base
            ->shouldReceive('getPluginConstant')
            ->with(PluginReceiver::$PLUGIN_CONST_DEBUG)
            ->andReturnTrue();
        $this->base
            ->shouldReceive('getPluginConstant')
            ->withNoArgs()
            ->andReturn(CONSTANT_PREFIX);

        redefine('error_log', function () {
            $this->addCallbackReached('errorLog');
        });

        $actual = $this->base->debug('Hello World');

        $this->assertEquals($should, $actual);
        $this->assertCallbacksReached();
    }

    public function testDebugWithMethod() {
        $should = CONSTANT_PREFIX . '_DEBUG (' . __METHOD__ . '): Hello World';

        $this->expectCallbacksReached(['errorLog']);

        $this->base->shouldReceive('debug')->passthru();
        $this->base
            ->shouldReceive('getPluginConstant')
            ->with(PluginReceiver::$PLUGIN_CONST_DEBUG)
            ->andReturnTrue();
        $this->base
            ->shouldReceive('getPluginConstant')
            ->withNoArgs()
            ->andReturn(CONSTANT_PREFIX);

        redefine('error_log', function () {
            $this->addCallbackReached('errorLog');
        });

        $actual = $this->base->debug('Hello World', __METHOD__);

        $this->assertEquals($should, $actual);

        $this->assertCallbacksReached();
    }

    public function testGetTableName() {
        /** @var MockInterface|wpdb */
        global $wpdb;

        $should = 'wp_' . PHPUNIT_DB_PREFIX;

        $this->base->shouldReceive('getTableName')->passthru();
        $this->base->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');

        $actual = $this->base->getTableName();

        $this->assertEquals($should, $actual);
    }

    public function testGetTableNameWithSuffix() {
        $should = 'wp_' . PHPUNIT_DB_PREFIX . '_mytable';

        $this->base->shouldReceive('getTableName')->passthru();
        $this->base->shouldReceive('getPluginConstant')->andReturnUsing('mockGetPluginConstant');

        $actual = $this->base->getTableName('mytable');

        $this->assertEquals($should, $actual);
    }
}
