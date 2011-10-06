<?php
/**
 * Utility Class for Never let me go.
 * @package never_let_me_go
 */
class Never_Let_Me_Go extends Hametuha_Library{
	
	/**
	 * @see Hametuha_Plugin
	 * @var string
	 */
	protected $name = "never_let_me_go";
	
	/**
	 * オプション初期値
	 * @var array
	 */
	protected $default_option = array(
		'enable' => 0,
		'resign_page' => 0,
		'keep_account' => 0,
		'destroy_level' => 1
	);
	
	/**
	 * Public Hook
	 */
	public function template_redirect(){
		//Register Hook on Resign page
		if($this->option['enable'] && $this->option['resign_page'] && is_page($this->option['resign_page'])){
			if(is_user_logged_in() ){
				if((FORCE_SSL_ADMIN || FORCE_SSL_LOGIN) && !is_ssl){
					header("Location: ".  str_replace('http:', 'https:', get_permalink($this->option['resign_page'])));
				}else{
					if($this->verify_nonce('resign_public')){
						//Completed resigning and show message.
						$this->delete_current_user();
						//If paged, show 2nd page. If not, redirect to login page.
						global $numpages;
						if($numpages > 1){
							add_filter('the_content', array($this, 'show_thankyou'), 1);
						}else{
							header('Location: '.wp_login_url());
							die();
						}
					}else{
						//On resign and display resign form.
						add_filter('the_content', array($this, 'show_resign_form'));
					}
				}
			}else{
				//User is not logged in so redirected to login page.
				header('Location: '.wp_login_url());
			}
		}
	}
	
	/**
	 * Filter hook for functions page.
	 * @global array $pages
	 * @param string $content
	 * @return string
	 */
	public function show_thankyou($content){
		global $pages, $numpages;
		$numpages = 1;
		if(isset($pages[1])){
			return $pages[1];
		}else{
			return $content;
		}
	}
	
	/**
	 * Filter hook for resign page
	 * @param string $content 
	 * @return string
	 */
	public function show_resign_form($content){
		global $numpages, $multipage, $more, $pagenow;
		$perma_link = (FORCE_SSL_LOGIN || FORCE_SSL_ADMIN) ? str_replace('http:', 'https:', get_permalink()) : get_permalink();
		$url = (false !== strpos('?', $perma_link)) ? $perma_link."&amp;resign=complete": $perma_link."?resign=complete";
		$nonce = wp_nonce_field($this->nonce_action('resign_public'), "_".$this->name."_nonce", false, false);
		$label = $this->_("Delete Account");
		$form = <<<EOS
			<form id="nlmg-resign-form" method="post" action="{$url}">
				{$nonce}
				<p class="submit">
					<input type="submit" value="{$label}" />
				</p>
			</form>
EOS;
		if($numpages > 1){
			$numpages = 1;
			$multipage = false;
		}
		return $content.$form;
	}
	
	/**
	 * Action hook for admin panel.
	 */
	public function admin_init(){
		//Update options
		if($this->verify_nonce('nlmg_option')){
			$this->option['enable'] = (int) $this->post('nlmg_enable');
			$this->option['resign_page'] = (int) $this->post('nlmg_resign_page');
			$this->option['keep_account'] = (int) $this->post('nlmg_keep_account');
			$this->option['destroy_level'] = (int) $this->post('nlmg_destroy_level');
			if(update_option($this->name.'_option', $this->option)){
				$this->add_message($this->_('Option updated.'));
			}else{
				$this->add_message($this->_('Option failed to updated.', true));
			}
		}
		//Delete account on admin panel
		if(defined('IS_PROFILE_PAGE') && is_user_logged_in() && wp_verify_nonce($this->get('_wpnonce'), 'nlmg_delete_on_admin')){
			$this->delete_current_user();
			$url = wp_login_url();
			header('Location: '.$url);
		}
		//Add resign button on admin panel
		if($this->option['enable'] && $this->option['resign_page'] == 0){
			add_action('profile_personal_options', array($this, 'resign_button'));
		}
		//Add Assets
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
	}
	
	/**
	 * Hook for admin_menu
	 * @return void
	 */
	public function admin_menu(){
		add_options_page($this->_('Never Let Me Go setting'), $this->_("Resign Setting"), 'delete_users', 'nlmg', array($this, 'render'));
	}
	
	/**
	 * Render options page
	 * @return void
	 */
	public function render(){
		require_once $this->dir.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."setting.php";
	}
	
	/**
	 * Enqueue Javascripts on admin panel
	 * @param string $hook
	 */
	public function enqueue_scripts($hook){
		wp_enqueue_script('syntax-init', $this->url."/assets/onload.js", array('syntax-php'), $this->version);
		wp_enqueue_style('syntax-theme-default');
	}
	
	/**
	 * Create resign button on admin panel
	 * @return void
	 */
	public function resign_button($user){
		?>
			<h3><?php $this->e('Delete Account'); ?></h3>
			<p>
				<?php $this->e('You can delete your account by putting the button below.'); ?>
			</p>
			<p class="right">
				<a class="button" href="<?php echo wp_nonce_url(admin_url('profile.php'), 'nlmg_delete_on_admin');?>" onclick="if(!confirm('<?php $this->e('Are you sure to delete your account?'); ?>')) return false;"><?php $this->e("Delete"); ?></a>
			</p>
		<?php
	}
	
	/**
	 * Delete current user account
	 * @global wpdb $wpdb
	 * @global int $user_ID
	 * @return void
	 */
	public function delete_current_user(){
		global $user_ID, $wpdb;
		wp_logout();
		if($this->option['keep_account']){
			delete_user_meta($user_ID, $wpdb->prefix."capabilities");
			delete_user_meta($user_ID, $wpdb->prefix."user_level");
			switch ($this->option['destroy_level']) {
				case 0:
					break;
				default:
					$login = uniqid("nlmg.", true);
					$pass = wp_generate_password(20);
					//Update user table
					$user_id = wp_update_user(array(
						'ID' => $user_ID,
						'user_pass' => $pass,
						'user_email' => "",
						'display_name' => $this->_('Deleted User')
					));
					//Update user_login
					$wpdb->update(
						$wpdb->users,
						array(
							'user_login' => $login
						),
						array(
							'ID' => $user_id
						),
						array('%s'),
						array('%d')
					);
					//clear current user
					global $current_user;
					$current_user = null;
					break;
			}
			do_action('never_let_me_go', $user_ID);
		}else{
			require_once ABSPATH."/wp-admin/includes/user.php";
			wp_delete_user($user_ID);
		}
	}
}