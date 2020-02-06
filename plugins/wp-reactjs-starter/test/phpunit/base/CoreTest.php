<?php
declare(strict_types=1);
namespace MatthiasWeb\WPRJSS\Test\base;

use MatthiasWeb\WPRJSS\base\Core;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use TestCaseUtils;
use WP_Mock\Tools\TestCase;

use function Patchwork\always;
use function Patchwork\redefine;

if (!class_exists(__NAMESPACE__ . '\\CoreImpl')) {
    class CoreImpl extends Core {
    }
}

final class CoreTest extends TestCase {
    use TestCaseUtils;

    /** @var MockInterface|CoreImpl */
    private $core;

    public function setUp(): void {
        parent::setUp();
        $this->core = Mockery::mock(Core::class);
    }

    public function testConstruct() {
        $this->expectCallbacksReached(['td', 'version']);

        $this->core
            ->shouldReceive('getPluginData')
            ->with('TextDomain')
            ->andReturn(PHPUNIT_TD);
        $this->core
            ->shouldReceive('getPluginData')
            ->with('Version')
            ->andReturn(PHPUNIT_VERSION);
        $this->core->shouldAllowMockingProtectedMethods()->shouldReceive('construct');

        redefine('define', function ($name) {
            $this->addCallbackReached('td', strpos($name, '_TD') !== false);
            $this->addCallbackReached('version', strpos($name, '_VERSION') !== false);
        });

        $ctor = (new ReflectionClass(Core::class))->getConstructor();
        $ctor->setAccessible(true);
        $ctor->invoke($this->core);

        $this->assertCallbacksReached();
    }
}
