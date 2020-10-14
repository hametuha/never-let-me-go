<?php
/*
Plugin Name: Never Let Me Go
Plugin URI: https://wordpress.org/plugins/never-let-me-go/
Author: Takahashi Fumiki
Author URI: https://takahashifumiki.com/
Version: 1.2.1
PHP Version: 5.6.0
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
	if ( version_compare( phpversion(), '5.3.0', '>=' ) ) {
		$auto_loader = __DIR__ . '/vendor/autoload.php';
		if ( file_exists( $auto_loader ) ) {
			require $auto_loader;
			call_user_func( array( 'NeverLetMeGo', 'getInstance' ) );
		} else {
			trigger_error( __( 'Composer auto loader is missing. Did you run composer install?', 'never-let-me-go' ) );
		}
	} else {
		add_action( 'admin_notices', 'nlmg_version_notice' );
	}
}
add_action( 'plugins_loaded', 'nlmg_plugins_loaded' );



/**
 * Show version smessage
 *
 * @internal
 */
function nlmg_version_notice() {
	$message = sprintf(
		// translators: %1$s is your PHP version, %2$s is URL.
		__( '<strong>Plugin Error: </strong>Never Let Me Go requires PHP 5.6 and over, but your PHP is %1$s. Please consider updating your PHP or downgrading this plugin to <a href="%2$s">0.8.2</a>.', 'never-let-me-go' ),
		phpversion(),
		'https://downloads.wordpress.org/plugin/never-let-me-go.1.1.0.zip'
	);
	printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $message ) );
}
