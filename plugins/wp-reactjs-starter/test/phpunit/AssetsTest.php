<?php
declare(strict_types=1);
namespace MatthiasWeb\WPRJSS\Test;

use MatthiasWeb\WPRJSS\Assets;
use Mockery;
use Mockery\MockInterface;
use WP_Mock;
use WP_Mock\Tools\TestCase;

final class AssetsTest extends TestCase {
    /** @var MockInterface|Assets */
    private $assets;

    public function setUp(): void {
        parent::setUp();
        $this->assets = Mockery::mock(Assets::class);
    }

    public function testEnqueueScriptsAndStylesAdmin() {
        $handle = 'admin';
        $usedHandle = PHPUNIT_SLUG . '-admin';

        $this->assets->shouldReceive('enqueue_scripts_and_styles')->passthru();
        $this->assets->shouldReceive('enqueueUtils')->once()->andReturn(['react']);
        $this->assets->shouldReceive('enqueueComposerScript')->with('utils', Mockery::any());
        $this->assets
            ->shouldReceive('enqueueScript')
            ->with($handle, 'admin.js', Mockery::any())
            ->andReturn($usedHandle);
        $this->assets->shouldReceive('enqueueStyle')->with($handle, 'admin.css');

        WP_Mock::userFunction('path_join', ['args' => [PHPUNIT_PATH, Assets::$PUBLIC_JSON_I18N]]);
        WP_Mock::userFunction('wp_set_script_translations', ['args' => [$usedHandle, PHPUNIT_TD, Mockery::any()]]);

        $this->assets->shouldReceive('localizeScript')->with(Assets::$TYPE_ADMIN);

        WP_Mock::userFunction('wp_localize_script', ['args' => [$usedHandle, PHPUNIT_SLUG_CAMELCASE, Mockery::any()]]);

        $this->assets->enqueue_scripts_and_styles(Assets::$TYPE_ADMIN);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueScriptsAndStylesFrontend() {
        $handle = 'widget';
        $usedHandle = PHPUNIT_SLUG . '-widget';

        $this->assets->shouldReceive('enqueue_scripts_and_styles')->passthru();
        $this->assets->shouldReceive('enqueueUtils')->once()->andReturn(['react']);
        $this->assets->shouldReceive('enqueueComposerScript')->with('utils', Mockery::any());
        $this->assets
            ->shouldReceive('enqueueScript')
            ->with($handle, 'widget.js', Mockery::any())
            ->andReturn($usedHandle);
        $this->assets->shouldReceive('enqueueStyle')->with($handle, 'widget.css');

        WP_Mock::userFunction('path_join', ['args' => [PHPUNIT_PATH, Assets::$PUBLIC_JSON_I18N]]);
        WP_Mock::userFunction('wp_set_script_translations', ['args' => [$usedHandle, PHPUNIT_TD, Mockery::any()]]);

        $this->assets->shouldReceive('localizeScript')->with(Assets::$TYPE_FRONTEND);

        WP_Mock::userFunction('wp_localize_script', ['args' => [$usedHandle, PHPUNIT_SLUG_CAMELCASE, Mockery::any()]]);

        $this->assets->enqueue_scripts_and_styles(Assets::$TYPE_FRONTEND);

        $this->addToAssertionCount(1);
    }

    public function testEnqueueScriptsAndStylesCustomType() {
        $this->assets->shouldReceive('enqueue_scripts_and_styles')->passthru();
        $this->assets->shouldNotReceive(
            'enqueueReact',
            'enqueueMobx',
            'enqueueComposerScript',
            'enqueueScript',
            'enqueueStyle'
        );

        WP_Mock::userFunction('path_join', ['args' => [PHPUNIT_PATH, Assets::$PUBLIC_JSON_I18N]]);
        WP_Mock::userFunction('wp_set_script_translations', ['times' => 0]);
        WP_Mock::userFunction('wp_localize_script', ['times' => 0]);

        $this->assets->enqueue_scripts_and_styles('another');

        $this->addToAssertionCount(1);
    }

    public function testOverrideLocalizeScriptAdmin() {
        $should = ['_' => '_'];

        $this->assets->shouldReceive('overrideLocalizeScript')->passthru();

        $actual = $this->assets->overrideLocalizeScript(Assets::$TYPE_ADMIN);

        $this->assertEquals($should, $actual);
    }

    public function testOverrideLocalizeScriptFrontend() {
        $should = ['_' => '_'];

        $this->assets->shouldReceive('overrideLocalizeScript')->passthru();

        $actual = $this->assets->overrideLocalizeScript(Assets::$TYPE_FRONTEND);

        $this->assertEquals($should, $actual);
    }

    public function testOverrideLocalizeScriptCustomType() {
        $should = [];

        $this->assets->shouldReceive('overrideLocalizeScript')->passthru();

        $actual = $this->assets->overrideLocalizeScript('another');

        $this->assertEquals($should, $actual);
    }
}
