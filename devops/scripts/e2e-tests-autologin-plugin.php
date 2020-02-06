<?php
/**
 * Plugin Name: E2E Autologin
 * Plugin URI: https://example.com
 * Description: This plugin is only installed and activated when e2e tests are running so we can skip the login form.
 * Version: 1.0.0
 * Author: John Tests
 * Author URI: https://example.com
 */

function autologin() {
    if (isset($_GET['autologin']) && strtolower($_GET['autologin']) === strtolower('WordPress')) {
        $autologin_user = wp_signon(
            [
                'user_login' => 'wordpress',
                'user_password' => 'wordpress',
                'remember' => true
            ],
            false
        );

        if (!is_wp_error($autologin_user)) {
            header('Location: wp-admin');
        }
    }
}
add_action('after_setup_theme', 'autologin');
