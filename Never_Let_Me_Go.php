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
	}
	
	/**
	 * Hook for admin_menu
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
	
}