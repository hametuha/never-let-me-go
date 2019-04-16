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
		\NeverLetMeGo\Admin::getInstance();
		\NeverLetMeGo\Page::getInstance();
		\NeverLetMeGo\WooCommerce::getInstance();
	}
	
	
}
