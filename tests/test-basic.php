<?php
/**
 *
 *
 * @package never_let_me_go
 */

/**
 * Sample test case.
 */
class NLMG_Basic_Test extends WP_UnitTestCase {

	protected $backupGlobals = true;

	/**
	 * A single example test
	 *
	 * @runInSeparateProcess
	 */
	function test_delete_user() {
		// Create user
		$user = nlmg_pseudo_user();
		$this->assertInstanceOf( 'WP_User', $user );
		$result = \NeverLetMeGo\Admin::getInstance()->delete_user( $user->ID );
		$this->assertTrue( $result );
	}

	/**
	 * Check auto loader
	 */
	function test_auto_loader() {
		// Check class exists
		$this->assertTrue( class_exists( 'NeverLetMeGo\\Admin' ) );
		$this->assertTrue( class_exists( 'NeverLetMeGo\\Page' ) );
	}
}
