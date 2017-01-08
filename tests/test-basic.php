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

	protected $backupGlobals = false;

	/**
	 * A single example test
	 *
	 * @runInSeparateProcess
	 */
	function test_delete_user() {
		// Check class exists
		$this->assertTrue( class_exists( 'NeverLetMeGo\\Admin' ) );
		$this->assertTrue( class_exists( 'NeverLetMeGo\\Page' ) );
		// Create user
		$user = nlmg_pseudo_user();
		$this->assertInstanceOf( 'WP_User', $user );
		$result = \NeverLetMeGo\Admin::getInstance()->delete_user( $user->ID );
		$this->assertTrue( $result );
	}
}
