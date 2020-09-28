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
		if ( $this->option[ 'enable' ] && $this->option[ 'resign_page' ] ) {
			// Process resign
			add_action( 'template_redirect', array( $this, 'templateRedirect' ) );
			// Register script
			add_action( 'init', [ $this, 'registerAssets' ] );
		}
	}
	
	/**
	 * Register assets.
	 */
	public function registerAssets() {
		wp_register_script( 'nlmg-form', $this->url . 'dist/js/unregister-form.js', [ 'jquery' ], $this->version, true );
		wp_register_style( 'nlmg-form', $this->url . 'dist/css/public.css', [], $this->version );
	}
	
	
	
	/**
	 * Public Hook for template redirect
	 */
	public function templateRedirect() {
		global $pages, $numpages, $multipage, $more, $pagenow;
		// Register Hook on Resign page
		if ( ! is_page( $this->option[ 'resign_page' ] ) ) {
			return;
		}
		// Avoid caching.
		nocache_headers();
		// Enqueue assets.
		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_script( 'nlmg-form' );
			wp_enqueue_style( 'nlmg-form' );
		} );
		// Is user logged in?
		if ( ! is_user_logged_in() ) {
			//User is not logged in so redirected to login page.
			$current_url = get_permalink( get_queried_object() );
			/**
			 * nlmg_not_logged_in_user_redirect
			 *
			 * Redirect user to this URL.
			 *
			 * @param string $redirect_url
			 * @param string $current_url
			 *
			 * @return string
			 */
			$redirect_url = apply_filters( 'nlmg_not_logged_in_user_redirect', wp_login_url( $current_url ), $current_url );
			wp_redirect( $redirect_url );
			exit;
		}
		add_filter( 'wp_link_pages', [ $this, 'kill_pagination' ] );
		// Avoid pagination.
		if ( $numpages > 1 ) {
			$numpages  = 1;
			$multipage = false;
		}
		if ( $this->input->verify_nonce( $this->nonce_action( 'resign_public' ) ) ) {
			// Validation.
			$user_id = get_current_user_id();
			$result  = $this->delete_current_user();
			if ( is_wp_error( $result ) ) {
				$this->errors = $result;
				// Add form to resign page.
				wp_enqueue_style( 'nlmg-form' );
			} else {
				// Successfully deleted.
				/**
				 * Executed after user has been deleted.
				 *
				 * @param int $user_id
				 *
				 * @since 0.9.0
				 *
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
	}
	
	/**
	 * Filter hook for resign page
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function showResignForm( $content ) {
		if ( get_the_ID() == $this->option[ 'resign_page' ] ) {
			// Check if error exists.
			if ( $this->errors ) {
				$message = sprintf(
					'<div class="error nlmg-error">%s<ul>%s</ul></div>',
					sprintf( '<h3 class="nlmg-error-title">%s</h3>', esc_html( _x( 'Error', 'error_message', 'never-let-me-go' ) ) ),
					implode( ' ', array_map( function ( $error ) {
						return sprintf( '<li class="nlmg-error-item">%s</li>', wp_kses_post( $error ) );
					}, $this->errors->get_error_messages() ) )
				);
				/**
				 * nlmg_error_messages
				 *
				 * @filter
				 *
				 * @param string $message Error message HTML markup.
				 * @param \WP_Error $errors
				 *
				 * @return string
				 * @since 1.0.0
				 *
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
			
			// UI Class.
			$classes = apply_filters( 'nlmg_resign_button_class', [ 'button-primary', 'button-nlmg' ] );
			
			// Confirmation UI
			if ( $this->option['display_acceptance'] ) {
				$onclick = ' disabled';
				$acceptance = sprintf(
					'<p class="nlmg-acceptance-block"><label class="nlmg-acceptance-label"><input type="checkbox" name="nlmg_accept_resign" id="nlmg-acceptance" class="nlmg-acceptance-checkbox" value="1" /> %s</label></p>',
					esc_html( apply_filters( 'nlmg_acceptance_text', __( 'I have consented to deleting my account.', 'never-let-me-go' ) ) )
				);
			} else {
				$acceptance = '';
				$classes = [];
				$confirm = $this->confirm_label();
				$onclick = $confirm ? sprintf( ' onclick="return confirm(\'%s\')"', esc_js( $confirm ) ) : '';
			}
			
			$classes = esc_attr( implode( ' ', $classes ) );
			$form    = <<<HTML
				<form id="nlmg-resign-form" method="post" action="{$url}">
					{$nonce}
					{$acceptance}
					<p class="submit">
						<input id="nlmg-resign-button" class="{$classes}" type="submit" value="{$label}"{$onclick} />
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
	 * @param string $content
	 *
	 * @return string
	 * @global array $pages
	 *
	 */
	public function showThankYou( $content ) {
		if ( $this->option[ 'resign_page' ] == get_the_ID() ) {
			// Cut content.
			$contents = explode( '<!--nextpage-->', get_post()->post_content );
			if ( count( $contents ) > 1 ) {
				$content = $contents[ 1 ];
			}
			/**
			 * nlmg_the_content
			 *
			 * Applied for content on resign page in the loop.
			 *
			 * @filter nlmg_the_content
			 *
			 * @param string $content Content to display.
			 * @param string $context 'resign' or 'thank_you'.
			 *
			 * @return string
			 * @since 1.0.0
			 *
			 */
			$content = apply_filters( 'nlmg_the_content', $content, 'thank_you' );
		}
		
		return $content;
	}
	
	/**
	 * Empty pagination.
	 *
	 * @param string $pagination
	 *
	 * @return string
	 */
	public function kill_pagination( $pagination ) {
		if ( is_preview() ) {
			return $pagination;
		}
		
		return '';
	}
}
