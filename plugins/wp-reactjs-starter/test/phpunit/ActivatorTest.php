<?php
declare(strict_types=1);
namespace MatthiasWeb\WPRJSS\Test;

use MatthiasWeb\WPRJSS\Activator;
use Mockery;
use Mockery\MockInterface;
use WP_Mock\Tools\TestCase;

final class ActivatorTest extends TestCase {
    /** @var MockInterface|Activator */
    private $activator;

    public function setUp(): void {
        parent::setUp();
        $this->activator = Mockery::mock(Activator::class);
    }

    public function testActivate() {
        $this->activator->shouldReceive('activate')->passthru();

        $this->activator->activate();

        $this->addToAssertionCount(1);
    }

    public function testDeactivate() {
        $this->activator->shouldReceive('deactivate')->passthru();

        $this->activator->deactivate();

        $this->addToAssertionCount(1);
    }

    public function testDbDelta() {
        $this->activator->shouldReceive('dbDelta')->passthru();

        $this->activator->dbDelta(false);

        $this->addToAssertionCount(1);
    }
}
