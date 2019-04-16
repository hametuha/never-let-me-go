<?php

namespace NeverLetMeGo;


use NeverLetMeGo\Pattern\Application;

/**
 * WooCommerce handler
 *
 * @package nlmg
 */
class WooCommerce extends Application {
	
	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	public function __construct( array $settings = [] ) {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}
		// Change redirect URL.
		add_filter( 'nlmg_not_logged_in_user_redirect', [ $this, 'login_redirect' ], 10, 2 );
	}
	
	/**
	 * Redirect user to WooCommerce account page.
	 *
	 * @param string $redirect_url
	 * @param string $page_url
	 * @return string
	 */
	public function login_redirect( $redirect_url, $page_url ) {
		$account = get_permalink( wc_get_page_id( 'myaccount' ) );
		return add_query_arg( [
			'redirect_to' => $page_url,
		], $account );
	}
}