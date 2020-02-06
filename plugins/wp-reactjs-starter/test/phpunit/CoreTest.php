<?php
declare(strict_types=1);
namespace MatthiasWeb\WPRJSS\Test;

use TestCaseUtils;
use MatthiasWeb\WPRJSS\base\Core as BaseCore;
use MatthiasWeb\WPRJSS\Core;
use MatthiasWeb\WPRJSS\rest\HelloWorld;
use MatthiasWeb\WPRJSS\view\menu\Page;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use WP_Mock;
use WP_Mock\Tools\TestCase;

use function Patchwork\always;
use function Patchwork\redefine;

final class CoreTest extends TestCase {
    use TestCaseUtils;

    /** @var MockInterface|Core */
    private $core;

    public function setUp(): void {
        parent::setUp();
        $this->core = Mockery::mock(Core::class);
    }

    public function testConstruct() {
        $self = $this->expectCallbacksReached(['parentConstructor']);

        redefine(BaseCore::class . '::__construct', function () use ($self) {
            $self->addCallbackReached('parentConstructor');
        });

        WP_Mock::expectActionAdded('widgets_init', [Mockery::self(), 'widgets_init']);

        $ctor = (new ReflectionClass(Core::class))->getConstructor();
        $ctor->setAccessible(true);
        $ctor->invoke($this->core);

        $this->assertHooksAdded();
        $this->assertCallbacksReached();
    }

    public function testInit() {
        $this->core->shouldReceive('init')->passthru();
        $this->core->shouldReceive('getAssets')->andReturnNull();

        redefine(Page::class . '::instance', always(null));
        redefine(HelloWorld::class . '::instance', always(null));

        WP_Mock::expectActionAdded('rest_api_init', [null, 'rest_api_init']);
        WP_Mock::expectActionAdded('admin_enqueue_scripts', [null, 'admin_enqueue_scripts']);
        WP_Mock::expectActionAdded('wp_enqueue_scripts', [null, 'wp_enqueue_scripts']);
        WP_Mock::expectActionAdded('admin_menu', [null, 'admin_menu']);

        $this->core->init();

        $this->assertHooksAdded();
    }

    public function testGetInstance() {
        $self = $this->expectCallbacksReached(['constructor']);

        redefine(Core::class . '::__construct', function () use ($self) {
            $self->addCallbackReached('constructor');
        });

        $actual = Core::getInstance();

        $this->assertEquals(Core::class, get_class($actual));
        $this->assertCallbacksReached();
    }
}
