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
	 */
	function test_auto_loader() {
		// Check class exists
		$this->assertTrue( class_exists( 'NeverLetMeGo\\Admin' ) );
		$this->assertTrue( class_exists( 'NeverLetMeGo\\Page' ) );
	}

	/**
	 * Delete user
	 */
	protected function delete_single_user() {
		$user = nlmg_pseudo_user();
		$this->assertInstanceOf( 'WP_User', $user );
		$result = \NeverLetMeGo\Admin::getInstance()->delete_user( $user->ID );
		$this->assertTrue( $result );
	}

	/**
	 * Delete user up to 4.7
	 * @runInSeparateProcess
	 */
	function test_delete_user() {
		$this->delete_single_user();
	}
}
