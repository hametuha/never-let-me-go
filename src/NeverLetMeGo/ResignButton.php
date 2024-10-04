<?php

namespace NeverLetMeGo;


use NeverLetMeGo\Pattern\Application;
use function WP_CLI\Utils\wp_version_compare;

/**
 * Register resign button block.
 */
class ResignButton extends Application {

	/**
	 * {@inheritDoc}
	 */
	public function __construct( $settings = array() ) {
		// If version is greater than 6.1, register blocks.
		if ( version_compare( get_bloginfo( 'version' ), '6.1', '>=' ) ) {
			add_action( 'init', [ $this, 'register_blocks' ] );
			add_action( 'rest_api_init', [ $this, 'register_endpoint' ] );
		}
	}

	/**
	 * Register resign button.
	 *
	 * @return void
	 */
	public function register_blocks() {
		wp_localize_script( 'nlmg-resign-block-helper', 'NlmgRedirect', [
			'url' => home_url(),
		] );
		register_block_type( 'nlmg/resign-block', [
			'editor_script_handles' => [ 'nlmg-resign-button' ],
			'view_script_handles'   => [ 'nlmg-resign-block-helper' ],
			'style_handles'  => [ 'nlmg-resign-block' ],
			'editor_style_handles'  => [ 'nlmg-resign-block-editor' ],
		] );
		register_block_type( 'nlmg/resign-button', [
			'render_callback' => function( $attributes = [], $content = '' ) {
				// If user is not logged in, return empty.
				return is_user_logged_in() ? $content : '';
			},
		] );
		register_block_type( 'nlmg/resign-login', [
			'render_callback' => function( $attributes = [], $content = '' ) {
				// If user is logged in, return empty.
				return is_user_logged_in() ? '' : $content;
			},
		] );
	}

	/**
	 * Register REST API endpoint.
	 *
	 * @return void
	 */
	public function register_endpoint() {
		register_rest_route( 'nlmg/v1', 'resign', [
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'resign_action' ],
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			]
		] );
	}

	/**
	 * Handle resign action.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function resign_action() {
		$result = $this->delete_current_user();
		if ( is_wp_error( $result ) ) {
			return $result;
		} elseif ( ! $result ) {
			return new \WP_Error( 'user_deletion_failure', __( 'Failed to delete account.', 'never-let-me-go' ), [
				'status' => 500,
			] );
		} else {
			return new \WP_REST_Response( [
				'success' => true,
			] );
		}
	}
}
