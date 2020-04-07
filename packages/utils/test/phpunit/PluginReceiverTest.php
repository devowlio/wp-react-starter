<?php
declare(strict_types=1);
namespace MatthiasWeb\Utils\Test;

use MatthiasWeb\Utils\PluginReceiver;
use Mockery;
use Mockery\MockInterface;
use WP_Mock\Tools\TestCase;

use function Patchwork\redefine;

if (!class_exists(__NAMESPACE__ . '\\PluginReceiverImpl')) {
    class PluginReceiverImpl {
        use PluginReceiver;
    }
}

final class PluginReceiverTest extends TestCase {
    /** @var MockInterface|PluginReceiverImpl */
    private $pluginReceiver;

    public function setUp(): void {
        parent::setUp();
        $this->pluginReceiver = Mockery::mock(PluginReceiverImpl::class);
        $this->pluginReceiver->shouldReceive('getPluginConstantPrefix')->passthru();
    }

    public function testGetPluginConstant() {
        $should = CONSTANT_PREFIX;

        $this->pluginReceiver->shouldReceive('getPluginConstant')->passthru();

        $actual = $this->pluginReceiver->getPluginConstant();

        $this->assertEquals($should, $actual);
    }

    public function testGetPluginConstantsByName() {
        $this->pluginReceiver->shouldReceive('getPluginConstant')->passthru();

        // Assets multiple constant in one single test
        $this->assertEquals(PHPUNIT_FILE, $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_FILE));
        $this->assertEquals(PHPUNIT_INC, $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_INC));
        $this->assertEquals(PHPUNIT_PATH, $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_PATH));
        $this->assertEquals(PHPUNIT_SLUG, $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_SLUG));
        $this->assertEquals(
            PHPUNIT_TD,
            $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_TEXT_DOMAIN)
        );
        $this->assertEquals(
            PHPUNIT_DB_PREFIX,
            $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_DB_PREFIX)
        );
        $this->assertEquals(
            PHPUNIT_OPT_PREFIX,
            $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_OPT_PREFIX)
        );
        $this->assertEquals(
            PHPUNIT_VERSION,
            $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_VERSION)
        );
        $this->assertEquals(PHPUNIT_NS, $this->pluginReceiver->getPluginConstant(PluginReceiver::$PLUGIN_CONST_NS));
    }

    public function testGetPluginClassInstance() {
        $should = __NAMESPACE__ . '\PluginReceiverTest';

        $this->pluginReceiver->shouldReceive('getPluginClassInstance')->passthru();
        $this->pluginReceiver
            ->shouldReceive('getPluginConstant')
            ->with(PluginReceiver::$PLUGIN_CONST_NS)
            ->andReturn(__NAMESPACE__);

        $actual = $this->pluginReceiver->getPluginClassInstance('PluginReceiverTest', self::class);

        $this->assertInstanceOf($should, $actual);
    }

    public function testGetCore() {
        $should = [['PhpUnit\\Test\\Core', 'getInstance']];

        $this->pluginReceiver->shouldReceive('getCore')->passthru();
        $this->pluginReceiver
            ->shouldReceive('getPluginConstant')
            ->with(PluginReceiver::$PLUGIN_CONST_NS)
            ->andReturn('PhpUnit\\Test');

        redefine('call_user_func', function () {
            return func_get_args();
        });

        $actual = $this->pluginReceiver->getCore();

        $this->assertEquals($should, $actual);
    }

    public function testGetPluginConstantPrefix() {
        $should = CONSTANT_PREFIX;

        $actual = (new PluginReceiverImpl())->getPluginConstantPrefix();

        $this->assertEquals($should, $actual);
    }
}
