<?php
/**
 * Plugin Name: E2E Autologin
 * Plugin URI: https://matthias-web.com
 * Description: This plugin is only installed and activated when e2e tests are running so we can skip the login form.
 * Version: 1.0.0
 * Author: Matthias Günter <support@matthias-web.com>
 * Author URI: https://matthias-web.com
 */

function autologin() {
    if (isset($_GET['autologin']) && $_GET['autologin'] === 'wordpress') {
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
