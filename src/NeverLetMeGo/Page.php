<?php

namespace NeverLetMeGo;


use NeverLetMeGo\Pattern\Application;

/**
 * Page controller
 *
 * @package NeverLetMeGo
 */
class Page extends Application
{

	protected function __construct( $settings = array() ) {
		if( $this->option['enable'] && $this->option['resign_page'] ){
			// Process resign
			add_action("template_redirect", array($this, "templateRedirect"));
		}
	}

	/**
	 * Public Hook for template redirect
	 */
	public function templateRedirect(){
		//Register Hook on Resign page
		if( is_page($this->option['resign_page']) ){
			if( is_user_logged_in() ){
				if( force_ssl_admin() && !is_ssl() ){
					wp_redirect(str_replace('http:', 'https:', get_permalink($this->option['resign_page'])));
					exit;
				}else{
					if( $this->input->verify_nonce($this->nonce_action('resign_public'))){
						//Get Resign Page
						$curpage = get_post($this->option['resign_page']);
						//Completed resigning and show message.
						$this->delete_current_user();
						// If paged, show 2nd page. If not, redirect to login page.
						if( $curpage && count(preg_split("/<!--*?nextpage*?-->/", $curpage->post_content)) >= 2 ){
							add_filter('the_content', array($this, 'showThankYou'), 1);
						}else{
							wp_redirect(wp_login_url());
							exit;
						}
					}else{
						// Add form to resign page
						add_filter('the_content', array($this, 'showResignForm'));
					}
				}
			}else{
				//User is not logged in so redirected to login page.
				auth_redirect();
			}
		}
	}

	/**
	 * Filter hook for resign page
	 *
	 * @param string $content
	 * @return string
	 */
	public function showResignForm($content){
		global $pages, $numpages, $multipage, $more, $pagenow;
		if( get_the_ID() == $this->option['resign_page'] ){
			$perma_link = force_ssl_admin() ? str_replace('http://', 'https://', get_permalink()) : get_permalink();
			$url = (false !== strpos('?', $perma_link)) ? $perma_link."&resign=complete": $perma_link."?resign=complete";
			$nonce = wp_nonce_field($this->nonce_action('resign_public'), '_wpnonce', false, false);
			/**
			 * nlmg_resign_button_label
			 *
			 * @param string $label
			 * @param int $user_id User ID
			 * @return string
			 */
			$label = apply_filters('nlmg_resign_button_label', $this->i18n->_("Delete Account"), get_current_user_id());

			/**
			 * nlmg_resign_confirm_label
			 *
			 * @param string $confirm
			 * @param int $user_id
			 * @return string
			 */
			$confirm = apply_filters('nlmg_resign_confirm_label', $this->i18n->_("Are you sure to delete account?"), get_current_user_id());
			$onclick = $confirm ? sprintf(' onclick="return confirm(\'%s\')"', esc_js($confirm)) : '';
			$form = <<<HTML
				<form id="nlmg-resign-form" method="post" action="{$url}">
					{$nonce}
					<p class="submit">
						<input class="button-primary" type="submit" value="{$label}"{$onclick} />
					</p>
				</form>
HTML;
			if( $numpages > 1 ){
				$numpages = 1;
				$multipage = false;
			}
			$content .= $form;
		}
		return $content;
	}


	/**
	 * Filter hook for functions page.
	 *
	 * @global array $pages
	 * @param string $content
	 * @return string
	 */
	public function showThankYou($content){
		global $pages, $numpages, $multipage;
		$numpages = 1;
		$multipage = false;
		if( isset($pages[1]) ){
			return $pages[1];
		}else{
			return $content;
		}
	}
}
