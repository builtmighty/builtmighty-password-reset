<?php
/*
Plugin Name: 🔑 Built Mighty Password Reset
Plugin URI: https://builtmighty.com
Description: Require users to reset their password on login or set a bulk reset for a specific user role.
Version: 1.1.0
Author: Built Mighty
Author URI: https://builtmighty.com
Copyright: Built Mighty
Text Domain: built-password-reset
Copyright © 2024 Built Mighty. All Rights Reserved.
*/

/**
 * Disallow direct access.
 * 
 * @since   1.0.0
 */
if( ! defined( 'WPINC' ) ) { die; }

/**
 * Check if WooCommerce is active.
 * 
 * @since   1.0.0
 */
if( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

/**
 * Constants.
 * 
 * @since   1.0.0
 */
define( 'BUILTPASS_VERSION', date( 'YmdHis' ) );
define( 'BUILTPASS_NAME', 'built-password-reset' );
define( 'BUILTPASS_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'BUILTPASS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'BUILTPASS_DOMAIN', 'built-password-reset' );

/** 
 * On activation.
 * 
 * @since   1.0.0
 */
register_activation_hook( __FILE__, 'builtpass_activation' );
function builtpass_activation() {

    // Flush rewrite rules.
    flush_rewrite_rules();

}

/**
 * On deactivation.
 * 
 * @since   1.0.0
 */
register_deactivation_hook( __FILE__, 'builtpass_deactivation' );
function builtpass_deactivation() {

    // Flush rewrite rules.
    flush_rewrite_rules();

}

/**
 * Load plugin.
 * 
 * @since   1.0.0
 */
require BUILTPASS_PATH . 'inc/class-plugin.php';

/**
 * Run plugin.
 * 
 * @since   1.0.0
 */
function run_builtpass_plugin() {

    // Get plugin.
    $plugin = new builtpassPlugin();

    // Run.
    $plugin->run();

}
run_builtpass_plugin();

/**
 * Plugin Updates. 
 * 
 * @since   1.0.0
 */
require BUILTPASS_PATH . 'updates/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$updates = PucFactory::buildUpdateChecker(
	'https://github.com/builtmighty/builtmighty-password-reset',
	__FILE__,
	'builtmighty-password-reset'
);
$updates->setBranch( 'main' );