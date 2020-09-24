<?php

/**
 * Bootstrap
 *
 * @package nlmg
 */
class NeverLetMeGo extends \NeverLetMeGo\Pattern\Application {
	
	/**
	 * constructor
	 *
	 * @param array $settings
	 */
	public function __construct( array $settings = [] ) {
		// Initialize instances.
		\NeverLetMeGo\Admin::getInstance();
		\NeverLetMeGo\Page::getInstance();
		\NeverLetMeGo\WooCommerce::getInstance();
		// Register command if avilable.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::add_command( 'nlmg', \NeverLetMeGo\Command::class );
		}
	}
	
	
}
