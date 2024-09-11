<?php
/*
Plugin Name: Never Let Me Go
Plugin URI: https://wordpress.org/plugins/never-let-me-go/
Author: Takahashi Fumiki
Author URI: https://takahashifumiki.com/
Version: nightly
Description: If someone wants to leave your WordPress, let them go.
Text Domain: never-let-me-go
Domain Path: /language/
License: GPL 3.0 or later
*/

defined( 'ABSPATH' ) || die( 'Do not load directly!' );

$info = get_file_data( __FILE__, [
	'version' => 'Version',
	'domain' => 'Text Domain',
	'php_version' => 'PHP Version',
] );

// Base file name.
define( 'NLMG_BASE_FILE', __FILE__ );

// Version.
define( 'NLMG_VERSION', $info['version'] );

/**
 * Initialize plugin
 *
 * @internal
 */
function nlmg_plugins_loaded() {
	// Register Domain.
	load_plugin_textdomain( 'never-let-me-go', false, basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'language' );
	$auto_loader = __DIR__ . '/vendor/autoload.php';
	if ( file_exists( $auto_loader ) ) {
		require $auto_loader;
		\NeverLetMeGo::getInstance();
	}
}
add_action( 'plugins_loaded', 'nlmg_plugins_loaded' );
