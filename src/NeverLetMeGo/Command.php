<?php

namespace NeverLetMeGo;

/**
 * Utility command for Never Let Me Go
 *
 * @package nlmg
 * @since 1.2.0
 */
class Command extends \WP_CLI_Command {

	/**
	 * Create dummy users from CSV.
	 *
	 * This commands create dummy users via CSV file.
	 * Useful for test purpose.
	 *
	 * ## OPTIONS
	 *
	 * : --file=<file>
	 * Optional. CSV data for dummy user.
	 * Please check the layout of data/users.csv in this plugin
	 * and you can make original CSV
	 *
	 * : --dry-run
	 * Optional If set, only users data will be displayed.
	 * Dry run before actually importing.
	 *
	 * @synopsis [--file=<file>] [--dry-run]
	 * @subcommand import-dummy-users
	 * @param array $args
	 * @param array $assoc
	 */
	public function import_dummy_users( $args, $assoc ) {
		$file = dirname( NLMG_BASE_FILE ) . '/data/users.csv';
		if ( isset( $assoc['file'] ) && file_exists( $assoc['file'] ) ) {
			$file = $assoc['file'];
		}
		\WP_CLI::line( sprintf( 'Import users from %s', $file ) );
		$users   = [];
		$headers = [];
		$spl = new \SplFileObject( $file );
		$spl->setFlags( \SplFileObject::READ_CSV );
		$counter = 0;
		$table = new \cli\Table();
		foreach ( $spl as $row ) {
			$user = [];
			if ( ! $counter ) {
				// This is file header.
				$headers = $row;
				$table->setHeaders( $headers );
			} elseif ( count( $row ) === count( $headers ) ) {
				// Add row
				$table->addRow( $row );
				// Convert each line.
				foreach ( $row as $index => $value ) {
					$key = $headers[ $index ];
					$user[ $key ] = $value;
				}
				// Add users.
				$users[] = $user;
			}
			$counter++;
		}
		$table->display();
		$imported = 0;
		if ( empty( $assoc['dry-run'] ) ) {
			// Import
			foreach ( $users as $user ) {
				$meta      = [];
				$user_data = [];
				foreach ( $user as $key => $value ) {
					if ( $this->is_default_user_prop( $key ) ) {
						$user_data[ $key ] = $value;
					} else {
						$meta[ $key ] = $value;
					}
				}
				// Validate $user_data
				if ( empty( $user_data['user_email'] ) ) {
					$user_data['user_email'] = sprintf( '%s@example.com', uniqid( 'dummy-', true ) );
				}
				if ( empty( $user_data['user_login'] ) ) {
					$user_data['user_login'] = $user_data['user_email'];
				}
				if ( empty( $user_data['user_pass'] ) ) {
					$user_data['user_pass'] = wp_generate_password();
				}
				$result = wp_insert_user( $user_data );
				if ( is_wp_error( $result ) ) {
					\WP_CLI::warning( $result->get_error_message() );
				} else {
					foreach ( $meta as $key => $value ) {
						update_user_meta( $result, $key, $value );
					}
					$imported++;
				}
			}
			\WP_CLI::success( sprintf( '%d uesrs imported.', $imported ) );
		}
	}

	/**
	 * Get available meta keys.
	 *
	 * Run this command and you can ge possible user meta from real database.
	 * Useful for filter development.
	 *
	 */
	public function meta_key() {
		$meta_keys = Page::getInstance()->available_meta_keys();
		$table = new \cli\Table();
		$table->setHeaders( [ 'Key', 'Total' ] );
		foreach ( $meta_keys as $key => $count ) {
			$table->addRow( [ $key, $count ] );
		}
		$table->display();
	}

	/**
	 * Detect if property is user's default.
	 *
	 * @param string $key
	 * @return bool
	 */
	protected function is_default_user_prop( $key ) {
		switch ( $key ) {
			case 'role':
			case 'display_name':
			case 'nickname':
			case 'first_name':
			case 'last_name':
			case 'description':
			case 'locale':
				return true;
			default:
				return 0 === strpos( $key, 'user_' );
		}
	}

	/**
	 * Display users in trash bin.
	 *
	 * @return void
	 */
	public function leavings() {
		$users = new \WP_User_Query( [
			'role' => TranshBin::getInstance()->role(),
		] );
		if ( empty( $users->get_results() ) ) {
			\WP_CLI::error( 'No users in trash bin.' );
		}
		$table = new \cli\Table();
		$table->setHeaders( [ 'ID', 'user_login', 'email', 'Display Name', 'Registered', 'Left At', 'Removed At' ] );
		foreach ( $users->get_results() as $user ) {
			$left_at = get_user_meta( $user->ID, 'nlmg_leave_date', true );
			$date = new \DateTime( $left_at, wp_timezone() );
			$date->add( new \DateInterval( 'P' . TranshBin::getInstance()->option['trash_bin'] . 'D' ) );
			$table->addRow( [
				$user->ID,
				$user->user_login,
				$user->user_email,
				$user->display_name,
				$user->user_registered,
				$left_at,
				$date->format( 'Y-m-d H:i:s' ),
			] );
		}
		$table->display();
	}
}
