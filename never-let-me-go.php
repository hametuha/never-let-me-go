<?php
/*
Plugin Name: Never Let Me Go
Plugin URI: https://wordpress.org/extend/plugins/never-let-me-go/
Author: Takahshi_Fumiki
Version: 1.0.3
PHP Version: 5.3.0
Author URI: https://takahashifumiki.com/
Description: If someone wants to leave your WordPress, let him go.
Text Domain: never-let-me-go
Domain Path: /language/
*/

defined( 'ABSPATH' ) or die( 'Do not load directly!' );

$info = get_file_data( __FILE__, [
	'version' => 'Version',
	'domain'  => 'Text Domain',
	'php_version' => 'PHP Version',
] );

// Register Domain
load_plugin_textdomain( $info['domain'], false, basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'language' );

// Base file name
define( 'NLMG_BASE_FILE', __FILE__ );

// Version
define( 'NLMG_VERSION', $info['version'] );

if ( version_compare( phpversion(), $info['php_version'], '>=' ) ) {
	$auto_loader = __DIR__.'/vendor/autoload.php';
	if ( file_exists( $auto_loader ) ) {
		require $auto_loader;
		call_user_func( array( 'NeverLetMeGo\\Admin', 'getInstance' ) );
		call_user_func( array( 'NeverLetMeGo\\Page', 'getInstance' ) );
	} else {
		trigger_error( __( 'Composer auto loader is missing. Did you run composer install?', 'never-let-me-go' ) );
	}
} else {
	add_action( 'admin_notices', '_nlmg_version_notice' );
}

/**
 * Show version smessage
 *
 * @internal
 */
function _nlmg_version_notice() {
	$message = sprintf(
		__( '<strong>Plugin Error: </strong>Never Let Me Go requires PHP 5.3 and over, but your PHP is %s. Please consider updating your PHP or downgrading this plugin to <a href="%s">0.8.2</a>.', 'never-let-me-go' ),
		phpversion(),
		'https://downloads.wordpress.org/plugin/never-let-me-go.0.8.2.zip'
	);
	printf( '<div class="error"><p>%s</p></div>', $message );
}


// Only for Poedit.
if ( false ) {
	__( 'If someone wants to leave your WordPress, let him go.', 'never-let-me-go' );
}
