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
		add_filter( 'nlmg_validate_user', [ $this, 'delete_filter' ], 10, 2 );
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
	
	/**
	 * Check user can delete account.
	 *
	 * @param \WP_Error $errors
	 * @param int      $user_id
	 * @return \WP_Error
	 */
	public function delete_filter( $errors, $user_id ) {
		// User has pending orders?
		$orders = wc_get_orders( [
			'customer_id' => $user_id,
			'status'      => 'wc-processing',
		] );
		if ( $orders ) {
			$errors->add( 'processing_orders', __( 'You have processing orders. Please wait until they will finish.', 'never-let-me-go' ) );
		}
		// User has active subscriptions?
		if ( function_exists( 'wcs_user_has_subscription' ) && wcs_user_has_subscription( $user_id, '', 'active' ) ) {
			$errors->add( 'has_subscriptions', __( 'You have active subscriptions. Please deactivate them before leaving.', 'never-let-me-go' ) );
		}
		return $errors;
	}
}