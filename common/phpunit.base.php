<?php
// Base PHPUnit bootstrap implementation

require_once dirname(PHPUNIT_FILE) . '/../vendor/antecedent/patchwork/Patchwork.php';
require_once dirname(PHPUNIT_FILE) . '/../vendor/autoload.php';

// Simulate package
define('CONSTANT_PREFIX', 'PHPUNIT');
define('PHPUNIT_PATH', dirname(PHPUNIT_FILE));
define('PHPUNIT_ROOT_SLUG', 'phpunit-root');
define('PHPUNIT_SLUG', basename(PHPUNIT_PATH));
define('PHPUNIT_TD', 'phpunit');
define('PHPUNIT_INC', PHPUNIT_PATH . '/inc/');
define('PHPUNIT_MIN_PHP', '7.0.0');
define('PHPUNIT_MIN_WP', '5.2.0');
define('PHPUNIT_NS', 'PhpUnit\\Test');
define('PHPUNIT_DB_PREFIX', 'phpunit');
define('PHPUNIT_OPT_PREFIX', 'phpunit');
define('PHPUNIT_SLUG_CAMELCASE', lcfirst(str_replace('-', '', ucwords(PHPUNIT_SLUG, '-'))));
define('PHPUNIT_VERSION', '1.0.0');

// Simulate alternative plugin (for tests in plugins)
if (defined('CONSTANT_PREFIX_PLUGIN_FILE')) {
    // Read constant prefix from plugin file
    $indexPhp = file_get_contents(realpath(dirname(CONSTANT_PREFIX_PLUGIN_FILE) . '/../src/index.php'));
    preg_match_all('/define\(\'(.*)_FILE\'/m', $indexPhp, $matches);
    define('CONSTANT_PREFIX_PLUGIN', $matches[1][0]);

    // Copy constants so non-PHPUnit's are also available
    define(CONSTANT_PREFIX_PLUGIN . '_FILE', PHPUNIT_FILE);
    define(CONSTANT_PREFIX_PLUGIN . '_PATH', PHPUNIT_PATH);
    define(CONSTANT_PREFIX_PLUGIN . '_ROOT_SLUG', PHPUNIT_ROOT_SLUG);
    define(CONSTANT_PREFIX_PLUGIN . '_SLUG', PHPUNIT_SLUG);
    define(CONSTANT_PREFIX_PLUGIN . '_TD', PHPUNIT_TD);
    define(CONSTANT_PREFIX_PLUGIN . '_INC', PHPUNIT_INC);
    define(CONSTANT_PREFIX_PLUGIN . '_MIN_PHP', PHPUNIT_MIN_PHP);
    define(CONSTANT_PREFIX_PLUGIN . '_MIN_WP', PHPUNIT_MIN_WP);
    define(CONSTANT_PREFIX_PLUGIN . '_NS', PHPUNIT_NS);
    define(CONSTANT_PREFIX_PLUGIN . '_DB_PREFIX', PHPUNIT_DB_PREFIX);
    define(CONSTANT_PREFIX_PLUGIN . '_OPT_PREFIX', PHPUNIT_OPT_PREFIX);
    define(CONSTANT_PREFIX_PLUGIN . '_SLUG_CAMELCASE', PHPUNIT_SLUG_CAMELCASE);
    define(CONSTANT_PREFIX_PLUGIN . '_VERSION', PHPUNIT_VERSION);
}

global $wpdb;
$wpdb = Mockery::mock('\WPDB');
$wpdb->prefix = 'wp_';

// Now call the bootstrap method of WP Mock
WP_Mock::setUsePatchwork(true);
WP_Mock::bootstrap();

/**
 * Mock implementation for getting constants.
 *
 * @param string $name
 * @return string
 */
function mockGetPluginConstant($name) {
    return $name === null ? CONSTANT_PREFIX : constant(CONSTANT_PREFIX . '_' . $name);
}

/**
 * Use utility functionality in your test cases.
 */
trait TestCaseUtils {
    private $currentlyReached;

    private $shouldCallbackReached;

    /**
     * Put this before your patchwork redefines or function overrides
     * so you can use addCallbackReached(). Before your test ends make sure
     * to assert with assertCallbacksReached().
     *
     * Note: If you are redefining a class instance you need to pass this
     * return values (save as $self) to the anonymous function closure via `using`.
     *
     * @param string[] $callbacks
     * @return self
     */
    protected function expectCallbacksReached($callbacks) {
        $this->currentlyReached = [];
        $this->shouldCallbackReached = $callbacks;
        return $this;
    }

    /**
     * Assert if all callbacks reached and throw an error, if not.
     *
     * @throws Exception
     */
    protected function assertCallbacksReached() {
        $cntCurrently = count($this->currentlyReached);
        $cntShould = count($this->shouldCallbackReached);
        if ($cntCurrently !== $cntShould) {
            throw new Exception(
                sprintf(
                    'Expected to reach %d callbacks, instead reached %d, perhaps you have too few or too many callbacks added. Not reached: %s.',
                    $cntShould,
                    $cntCurrently,
                    join(',', array_diff($this->shouldCallbackReached, $this->currentlyReached))
                )
            );
        } else {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Indicate that a given callback is reached.
     *
     * @param string $callback
     * @param boolean $onlyIf Pass a condition if you want to avoid one-line if's
     */
    public function addCallbackReached($callback, $onlyIf = true) {
        if ($onlyIf) {
            $this->currentlyReached[] = $callback;
        }
    }

    /**
     * Check if a given callback was reached.
     *
     * @param string $callback
     * @return boolean
     */
    public function hasCallbackReached($callback) {
        return in_array($callback, $this->currentlyReached, true);
    }
}
