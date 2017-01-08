<?php

namespace NeverLetMeGo;


use NeverLetMeGo\Pattern\Application;

/**
 * Page controller
 *
 * @package NeverLetMeGo
 */
class Page extends Application {

	/**
	 * @var \WP_Error
	 */
	protected $errors = null;

	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	protected function __construct( $settings = array() ) {
		if ( $this->option['enable'] && $this->option['resign_page'] ) {
			// Process resign
			add_action( 'template_redirect', array( $this, 'templateRedirect' ) );
		}
	}

	/**
	 * Public Hook for template redirect
	 */
	public function templateRedirect() {
		global $pages, $numpages, $multipage, $more, $pagenow;
		//Register Hook on Resign page
		if ( is_page( $this->option['resign_page'] ) ) {
			nocache_headers();
			// Avoid pagination.
			if ( $numpages > 1 ) {
				$numpages  = 1;
				$multipage = false;
			}
			if ( is_user_logged_in() ) {
				if ( $this->input->verify_nonce( $this->nonce_action( 'resign_public' ) ) ) {
					// Validation
					$user_id = get_current_user_id();
					$result  = $this->delete_current_user();
					if ( is_wp_error( $result ) ) {
						$this->errors = $result;
						// Add form to resign page
						add_filter( 'the_content', array( $this, 'showResignForm' ) );
					} else {
						// Successfully deleted.
						/**
						 * Executed after user has been deleted.
						 *
						 * @since 0.9.0
						 *
						 * @param int $user_id
						 */
						do_action( 'never_let_me_go', $user_id );
						// If paged, show 2nd page. If not, redirect to login page.
						if ( count( preg_split( '/<!--*?nextpage*?-->/', get_queried_object()->post_content ) ) >= 2 ) {
							add_filter( 'the_content', array( $this, 'showThankYou' ), 1 );
						} else {
							wp_redirect( $this->default_redirect_link( $user_id ) );
							exit;
						}
					}
				} else {
					// Add form to resign page
					add_filter( 'the_content', array( $this, 'showResignForm' ) );
				}
			} else {
				//User is not logged in so redirected to login page.
				auth_redirect();
				exit;
			}
		}
	}

	/**
	 * Filter hook for resign page
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function showResignForm( $content ) {
		if ( get_the_ID() == $this->option['resign_page'] ) {
			// Check if error exists.
			if ( $this->errors ) {
				$message = sprintf( '<div class="error nlmg-error"><ul>%s</ul></div>', implode( ' ', array_map( function ( $error ) {
					return sprintf( '<li>%s</li>', $error );
				}, $this->errors->get_error_messages() ) ) );
				/**
				 * nlmg_error_messages
				 *
				 * @filter
				 * @since 1.0.0
				 *
				 * @param string $message Error message HTML markup.
				 * @param \WP_Error $errors
				 *
				 * @return string
				 */
				$content = apply_filters( 'nlmg_error_messages', $message, $this->errors ) . $content;
			}
			$url   = add_query_arg( array(
				'resign' => 'complete',
			), get_permalink() );
			$nonce = wp_nonce_field( $this->nonce_action( 'resign_public' ), '_wpnonce', false, false );
			/**
			 * nlmg_resign_button_label
			 *
			 * @param string $label
			 * @param int $user_id User ID
			 *
			 * @return string
			 */
			$label = apply_filters( 'nlmg_resign_button_label', __( 'Delete Account', 'never-let-me-go' ), get_current_user_id() );

			$confirm = $this->confirm_label();
			$onclick = $confirm ? sprintf( ' onclick="return confirm(\'%s\')"', esc_js( $confirm ) ) : '';
			$form    = <<<HTML
				<form id="nlmg-resign-form" method="post" action="{$url}">
					{$nonce}
					<p class="submit">
						<input class="button-primary button-nlmg" type="submit" value="{$label}"{$onclick} />
					</p>
				</form>
HTML;
			$content .= $form;
			$content = apply_filters( 'nlmg_the_content', $content, 'resign' );
		}

		return $content;
	}


	/**
	 * Filter hook for functions page.
	 *
	 * @global array $pages
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function showThankYou( $content ) {
		if ( $this->option['resign_page'] == get_the_ID() ) {
			// Cut content.
			$contents = explode( '<!--nextpage-->', get_post()->post_content );
			if ( count( $contents ) > 1 ) {
				$content = $contents[1];
			}
			/**
			 * nlmg_the_content
			 *
			 * Applied for content on resign page in the loop.
			 *
			 * @filter nlmg_the_content
			 * @since 1.0.0
			 *
			 * @param string $content Content to display.
			 * @param string $context 'resign' or 'thank_you'.
			 *
			 * @return string
			 */
			$content = apply_filters( 'nlmg_the_content', $content, 'thank_you' );
		}

		return $content;
	}
}
