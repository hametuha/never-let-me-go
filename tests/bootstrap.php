<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Never_Let_Me_Go
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Create user for test
 *
 * @param array $args
 * @return WP_User
 * @throws RuntimeException
 */
function nlmg_pseudo_user( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'user_login' => 'nlmg_guest',
		'user_email' => 'pseudo@takahashifumiki.com',
		'user_pass'  => 'neverletmego',
		'role'       => 'subscriber',
	) );
	$user = wp_insert_user( $args );
	if ( is_wp_error( $user ) ) {
		throw new RuntimeException( $user->get_error_message(), $user->get_error_code() );
	}
	return new WP_User( $user );
}

/**
 * Set default option
 *
 * @param array $args
 */
function nlmg_test_option( $args = [] ) {
	$args = wp_parse_args( $args, array(
		'enable'        => true,
		'resign_page'   => 0,
		'keep_account'  => 0,
		'destroy_level' => 1,
		'assign_to'     => 0,
	) );
	update_option( 'never_let_me_go_option', $args );
}

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/never-let-me-go.php';
	// Set default option
	nlmg_test_option();
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
