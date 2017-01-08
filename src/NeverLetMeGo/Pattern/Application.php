<?php

namespace NeverLetMeGo\Pattern;


use NeverLetMeGo\Utility\i18n;
use NeverLetMeGo\Utility\Input;

/**
 * Class Application
 *
 * @package NeverLetMeGo\Pattern
 * @property-read Input $input
 * @property-read i18n $i18n
 * @property-read string $nonce_key
 * @property-read array $option
 * @property-read string $dir
 * @property-read string $url
 * @property-read string $version
 */
class Application extends Singleton {

	/**
	 * @var string
	 */
	protected $name = 'never_let_me_go';


	/**
	 * nonce用に接頭辞をつけて返す
	 *
	 * @param string $action
	 *
	 * @return string
	 */
	public function nonce_action( $action ) {
		return $this->name . '_' . $action;
	}

	/**
	 * wp_nonce_fieldのエイリアス
	 *
	 * @param string $action
	 */
	public function nonce_field( $action ) {
		wp_nonce_field( $this->nonce_action( $action ), $this->nonce_key );
	}

	/**
	 * Delete current_user account
	 *
	 * @return true|\WP_Error
	 */
	public function delete_current_user() {
		$user_id = get_current_user_id();
		return $this->delete_user( $user_id );
	}

	/**
	 * Delete user account
	 *
	 * @return true|\WP_Error
	 */
	public function delete_user( $user_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		if ( ! $user_id ) {
			return new \WP_Error( 404, __( 'User doesn\'t exist.', 'never-let-me-go' ) );
		}
		/**
		 * nlmg_validate_user
		 *
		 * Validate before user remove his account.
		 * If any error is added with `$error->add($code, $message)`,
		 * User can't remove their account.
		 *
		 * @filter nlmg_validate_user
		 * @since 1.0.0
		 * @param \WP_Error $error Error object
		 * @param int $user_id User ID to leave
		 * @return \WP_Error
		 */
		$result = apply_filters( 'nlmg_validate_user', new \WP_Error(), $user_id );
		if ( $result->get_error_messages() ) {
			return $result;
		}
		// Validation O.K. Remove user.
		/**
		 * nlmg_before_leave
		 *
		 * Executed before leave site.
		 *
		 * @since 1.0.0
		 * @action nlmg_before_leave
		 * @param int $user_id
		 */
		do_action( 'nlmg_before_leave', $user_id );
		wp_logout();
		if ( $this->option['keep_account'] ) {
			delete_user_meta( $user_id, $wpdb->prefix . 'capabilities' );
			delete_user_meta( $user_id, $wpdb->prefix . 'user_level' );
			switch ( $this->option['destroy_level'] ) {
				case 0:
					// Do nothing
					break;
				default:
					$login = uniqid( 'nlmg.', true );
					$pass  = wp_generate_password( 20 );
					//Update user table
					$user_id = wp_update_user( array(
						'ID'           => $user_id,
						'user_pass'    => $pass,
						'user_email'   => '',
						'display_name' => __( 'Deleted User', 'never-let-me-go' ),
					) );
					// Update user_login
					$wpdb->update(
						$wpdb->users,
						array(
							'user_login' => $login,
						),
						array(
							'ID' => $user_id,
						),
						array( '%s' ),
						array( '%d' )
					);
					//clear current user
					global $current_user;
					$current_user = null;
					break;
			}
			return true;
		} else {
			require_once ABSPATH . '/wp-admin/includes/user.php';
			/**
			 * nlmg_assign_to
			 *
			 * Validate before user remove his account.
			 * If any error is added with `$error->add($code, $message)`,
			 * User can't remove their account.
			 *
			 * @filter nlmg_assign_to
			 * @since 1.0.0
			 * @param int|string User ID to be assigned. Might be 0 or empty string.
			 * @param int $user_id User ID to leave
			 * @return \WP_Error
			 */
			$assign_to = apply_filters( 'nlmg_assign_to', $this->option['assign_to'] ? $this->option['assign_to'] : null, $user_id );
			return wp_delete_user( $user_id, $assign_to );
		}
	}

	/**
	 * Get redirect URL after remove account
	 *
	 * @param int $user_id
	 * @return string
	 */
	protected function default_redirect_link( $user_id = 0 ) {
		/**
		 * nlmg_redirect_link
		 *
		 * @filter nlmg_redirect_link
		 * @since 1.0.0
		 * @param string $link Default is `wp_login_url()`
		 * @param int    $user_id User ID removed.
		 * @return string
		 */
		return apply_filters( 'nlmg_redirect_link', wp_login_url(), $user_id );
	}

	/**
	 * Get confirm label
	 *
	 * @return string
	 */
	protected function confirm_label() {
		/**
		 * nlmg_resign_confirm_label
		 *
		 * @param string $confirm
		 * @param int $user_id
		 *
		 * @return string
		 */
		return apply_filters( 'nlmg_resign_confirm_label', __( 'Are you sure to delete account?', 'never-let-me-go' ), get_current_user_id() );
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'input':
				return Input::getInstance();
				break;
			case 'nonce_key':
				return "_{$this->name}_nonce";
				break;
			case 'option':
				$option = get_option( $this->name . '_option', array() );
				foreach (
					array(
						'enable'        => 0,
						'resign_page'   => 0,
						'assign_to'     => 0,
						'keep_account'  => 0,
						'destroy_level' => 1,
					) as $key => $val
				) {
					if ( ! isset( $option[ $key ] ) ) {
						$option[ $key ] = $val;
					}
				}

				return $option;
				break;
			case 'version':
				return NLMG_VERSION;
				break;
			case 'dir':
				return dirname( NLMG_BASE_FILE );
				break;
			case 'url':
				return plugin_dir_url( NLMG_BASE_FILE );
				break;
			default:
				// Do nothing
				return null;
				break;
		}
	}
}
