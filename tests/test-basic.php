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

	/**
	 * A single example test.
	 */
	function test_auto_loader() {
		$this->assertTrue( class_exists( 'NeverLetMeGo\\Admin' ) );
		$this->assertTrue( class_exists( 'NeverLetMeGo\\Page' ) );
	}

	/**
	 * Delete user
	 *
	 * @runInSeparateProcess
	 */
	function test_user_delete() {
		// Create user
		$user = nlmg_pseudo_user();
		$result = \NeverLetMeGo\Admin::getInstance()->delete_user( $user->ID );
		$this->assertTrue( $result );
	}

}
