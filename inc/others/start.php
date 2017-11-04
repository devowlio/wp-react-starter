<?php
// We have now ensured that we are running the minimum PHP version.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); // Avoid direct file request

// Load core
require_once(WPRJSS_INC . '/general/Core.class.php');
call_user_func(array(WPRJSS_NS . '\\general\\Core', 'getInstance'));
?>