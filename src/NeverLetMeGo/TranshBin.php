<?php

namespace NeverLetMeGo;

use NeverLetMeGo\Pattern\Application;

/**
 * Handle transh bin feature.
 */
class TranshBin extends Application {

	/**
	 * Cron hook name.
	 */
	const CRON_HOOK = 'nlmg_trash_bin';

	/**
	 * {@inheritDoc}
	 */
	public function __construct( $settings = array() ) {
		add_action( 'init', [ $this, 'register_role' ] );
		add_action( 'init', [ $this, 'register_cron' ] );
		add_action( self::CRON_HOOK, [ $this, 'remove_outdated_users' ] );
	}

	/**
	 * Register cron to remove user in trash bin.
	 *
	 * @return void
	 */
	public function register_cron() {
		if ( $this->option['trash_bin'] > 0 ) {
			// Should register cron to remove user.
			if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
				wp_schedule_event( time(), 'daily', self::CRON_HOOK );
			}
		} else {
			// Should remove cron.
			if ( wp_next_scheduled( self::CRON_HOOK ) ) {
				wp_clear_scheduled_hook( self::CRON_HOOK );
			}

		}
	}

	/**
	 * Register role.
	 *
	 * @return void
	 */
	public function register_role() {
		// Create user with no cap.
		if ( $this->option['trash_bin'] > 0 ) {
			add_role( $this->role(), $this->role_label(), [] );
		}
	}

	/**
	 * Role for leaving user.
	 *
	 * @return string
	 */
	public function role() {
		return (string) apply_filters( 'nlmg_leaving_user_role', 'leaving_user' );
	}

	/**
	 * Label of leaving user.
	 *
	 * @return string
	 */
	public function role_label() {
		return (string) apply_filters( 'nlmg_leaving_user_label', __( 'Leaving User', 'never-let-me-go' ) );
	}

	/**
	 * Move user to trash bin.
	 *
	 * @param int $user_id
	 * @return true|\WP_Error
	 */
	public function move_to( $user_id ) {
		$result = wp_update_user( [
			'ID'   => $user_id,
			'role' => $this->role(),
		] );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		update_user_meta( $user_id, 'nlmg_leave_date', current_time( 'mysql' ) );
		return true;
	}

	/**
	 * Remove outdated users.
	 *
	 * @return void
	 */
	public function remove_outdated_users() {
		if ( 0 < $this->option['trash_bin'] ) {
			// Remove all users.
			$date = new \DateTime( 'now', wp_timezone() );
			$date->sub( new \DateInterval( 'P' . $this->option['trash_bin'] . 'D' ) );
			$should_remove = $date->format( 'Y-m-d H:i:s' );
			$users = new \WP_User_Query( [
				'role'    => $this->role(),
				'orderby' => 'ID',
				'order'   => 'ASC',
				'meta_query' => [
					[
						'key'     => 'nlmg_leave_date',
						'value'   => $should_remove,
						'compare' => '<=',
						'type'    => 'DATETIME',
					],
				],
			] );
			foreach ( $users->get_results() as $user ) {
				$this->delete_user( $user->ID );
			}
		}
	}
}
