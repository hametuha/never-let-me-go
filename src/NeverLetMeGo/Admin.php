<?php

namespace NeverLetMeGo;


use NeverLetMeGo\Pattern\Application;

/**
 * Setting screen controller.
 */
class Admin extends Application {

	/**
	 * @var array
	 */
	private $admin_error = array();

	/**
	 * @var array
	 */
	private $admin_message = array();


	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	protected function __construct( array $settings = [] ) {
		add_action( 'admin_init', array( $this, 'adminInit' ) );
		add_action( 'admin_menu', array( $this, 'adminMenu' ) );
		add_action( 'admin_notices', array( $this, 'adminNotices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
		// Add Action links on plugin lists.
		add_filter( 'plugin_action_links', array( $this, 'pluginPageLink' ), 500, 2 );
	}

	/**
	 * Add menu
	 */
	public function adminMenu() {
		add_options_page(
			__( 'Never Let Me Go setting', 'never-let-me-go' ),
			__( 'Resign Setting', 'never-let-me-go' ),
			'delete_users',
			'nlmg',
			array( $this, 'render' )
		);
	}

	/**
	 * Executed on admin_init
	 */
	public function adminInit() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			//Ajax Action for incremental search
			add_action( 'wp_ajax_nlmg_user_search', [ $this, 'incSearch' ] );
		} else {
			// Update options
			if ( $this->input->verify_nonce( 'nlmg_option' ) ) {
				$option                       = [];
				$option['enable']             = (int) $this->input->post( 'nlmg_enable' );
				$option['resign_page']        = (int) $this->input->post( 'nlmg_resign_page' );
				$option['keep_account']       = (int) $this->input->post( 'nlmg_keep_account' );
				$option['destroy_level']      = (int) $this->input->post( 'nlmg_destroy_level' );
				$option['assign_to']          = (int) $this->input->post( 'nlmg_assign_to' );
				$option['display_acceptance'] = (int) $this->input->post( 'nlmg_display_acceptance' );
				$option['meta_to_keep']       = implode( ',', array_filter( (array) $this->input->post( 'nlmg_meta_to_keep' ) ) );
				if ( update_option( $this->name . '_option', $option ) ) {
					$this->add_message( __( 'Option updated.', 'never-let-me-go' ) );
				} else {
					$this->add_message( __( 'Option failed to updated.', 'never-let-me-go' ), true );
				}
			}
			// Delete account on admin panel
			if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE && is_user_logged_in() && $this->input->verify_nonce( 'nlmg_delete_on_admin' ) ) {
				$user_id = get_current_user_id();
				$result  = $this->delete_current_user();
				if ( is_wp_error( $result ) ) {
					$messages = $result->get_error_messages();
					add_action( 'admin_notices', function () use ( $messages ) {
						printf( '<div class="error"><p>%s</p></div>', implode( '<br />', $messages ) );
					} );
				} else {
					wp_redirect( $this->default_redirect_link( $user_id ) );
					exit;
				}
			}
			// Add resign button on admin panel
			if ( $this->option['enable'] && ( 0 == $this->option['resign_page'] ) ) {
				add_action( 'show_user_profile', array( $this, 'resignButton' ) );
			}
		}
	}


	/**
	 * Enqueue Javascript on admin panel
	 *
	 * @param string $page
	 */
	public function enqueueScripts( $page ) {
		if ( 'settings_page_nlmg' == $page ) {
			wp_enqueue_style( 'nlmg-ajax' );
			wp_localize_script( 'nlmg-admin', 'NLMG', array(
				'endpoint'  => admin_url( 'admin-ajax.php?action=nlmg_user_search' ),
				'noResults' => __( 'No results', 'never-let-me-go' ),
				'found'     => __( '%% found.', 'never-let-me-go' ),
			) );
			wp_enqueue_script( 'nlmg-admin' );
		}
	}

	/**
	 * Show notices on admin UI
	 *
	 * @return void
	 */
	public function adminNotices() {
		if ( ! empty( $this->admin_error ) ) {
			printf( '<div class="error"><p>%s</p></div>', implode( '<br />', $this->admin_error ) );
		}
		if ( ! empty( $this->admin_message ) ) {
			printf( '<div class="updated"><p>%s</p></div>', implode( '<br />', $this->admin_message ) );
		}
		if ( current_user_can( 'manage_options' ) && ! $this->option['enable'] ) {
			printf(
				'<div class="error"><p>%s</p></div>',
				sprintf(
					__( '<strong>[Never Let Me Go] Plugin is active but features are not enabled. Please go to <a href="%s">setting page</a>.</strong>', 'never-let-me-go' ),
					admin_url( 'options-general.php?page=nlmg' )
				)
			);
		}
	}

	/**
	 * Add message to admin screen
	 *
	 * @param string $string
	 * @param boolean $error (optional) If true, add error message.
	 *
	 * @return void
	 */
	public function add_message( $string, $error = false ) {
		if ( $error ) {
			$this->admin_error[] = (string) $string;
		} else {
			$this->admin_message[] = (string) $string;
		}
	}

	/**
	 * Add action link on plugin lists
	 *
	 * @param array $links
	 * @param string $file
	 *
	 * @return string
	 */
	public function pluginPageLink( $links, $file ) {
		if ( false !== strpos( $file, 'never-let-me-go' ) ) {
			foreach (
				array(
					sprintf( '<a target="_blank" href="%s">%s</a>', add_query_arg( array(
						'utm_source'   => 'dashboard',
						'utm_campaign' => 'nlmg',
						'utm_medium'   => 'plugin-list',
					), 'https://gianism.info/add-on/never-let-me-go/' ), __( 'Support', 'never-let-me-go' ) ),
					sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=nlmg' ), __( 'Settings', 'never-let-me-go' ) ),
				) as $link
			) {
				array_unshift( $links, $link );
			}
		}

		return $links;
	}


	/**
	 * Create resign button on admin panel
	 *
	 * @param \WP_User
	 *
	 * @return void
	 */
	public function resignButton( $user ) {
		?>
        <hr/>
        <h3><?php _e( 'Delete Account', 'never-let-me-go' ); ?></h3>
        <p>
			<?php _e( 'You can delete your account by putting the button below.', 'never-let-me-go' ); ?>
        </p>
        <p class="right">
            <a class="button" href="<?php echo wp_nonce_url( admin_url( 'profile.php' ), 'nlmg_delete_on_admin' ); ?>"
               onclick="if(!confirm('<?php echo esc_js( $this->confirm_label() ); ?>')) return false;"><?php esc_html_e( 'Delete', 'never-let-me-go' ); ?></a>
        </p>
        <hr/>
		<?php
	}

	/**
	 * Render options page
	 *
	 * @return void
	 */
	public function render() {
		include $this->dir . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'setting.php';
	}

	/**
	 * Returns user object by incremental search
	 */
	public function incSearch() {
		$result = array();
		if ( current_user_can( 'manage_options' ) ) {
			if ( $this->input->get( 'term' ) ) {
				/** @var \wpdb $wpdb */
				global $wpdb;
				$query             = '%' . $this->input->get( 'term' ) . '%';
				$sql               = <<<SQL
					SELECT SQL_CALC_FOUND_ROWS
						ID, user_login, display_name
					FROM {$wpdb->users}
					WHERE user_login LIKE %s
					   OR user_email LIKE %s
					   OR display_name LIKE %s
                    ORDER BY display_name ASC
					LIMIT 10
SQL;
				$result = array_map( function ( $user ) {
					$user->avatar = get_avatar( $user->ID, '48', '', $user->display_name );

					return $user;
				}, $wpdb->get_results( $wpdb->prepare( $sql, $query, $query, $query ) ) );
			}
		}
		wp_send_json( $result );
	}
}
