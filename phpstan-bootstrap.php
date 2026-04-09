<?php
/**
 * PHPStan bootstrap file.
 *
 * @package NeverLetMeGo
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/wp/' );
}
if ( ! defined( 'WPINC' ) ) {
	define( 'WPINC', 'wp-includes' );
}
if ( ! defined( 'NLMG_BASE_FILE' ) ) {
	define( 'NLMG_BASE_FILE', __DIR__ . '/never-let-me-go.php' );
}
if ( ! defined( 'NLMG_VERSION' ) ) {
	define( 'NLMG_VERSION', 'phpstan' );
}
