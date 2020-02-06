<?php
// Bootstrap PHPUnit

define('PHPUNIT_FILE', __FILE__);
define('CONSTANT_PREFIX_PLUGIN_FILE', __FILE__);
require_once __DIR__ . '/../../../common/phpunit.base.php';

Mockery::mock('WP_Widget');
