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
		'keep_account' => 0
	);
	
	/**
	 * init hook
	 */
	public function init(){
		
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
			if(update_option($this->name.'_option', $this->option)){
				$this->add_message($this->_('Option updated.'));
			}else{
				$this->add_message($this->_('Option failed to updated.', true));
			}
		}
		//Delete account on admin panel
		if(defined('IS_PROFILE_PAGE') && is_user_logged_in() && wp_verify_nonce($this->get('_wpnonce'), 'nlmg_delete_on_admin')){
			$this->delete_current_user();
		}
		//Add resign button on admin panel
		if($this->option['enable']){
			add_action('profile_personal_options', array($this, 'resign_button'));
		}
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
		$url = wp_login_url();
		wp_logout();
		if($this->option['keep_account']){
			delete_user_meta($user_ID, $wpdb->prefix."capabilities");
			delete_user_meta($user_ID, $wpdb->prefix."user_level");
			do_action('never_let_me_go', $user_ID);
		}else{
			require_once ABSPATH."/wp-admin/includes/user.php";
			wp_delete_user($user_ID);
		}
		header('Location: '.$url);
	}
}