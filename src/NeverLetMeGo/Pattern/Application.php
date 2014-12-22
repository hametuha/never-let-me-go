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
class Application extends Singleton
{

	/**
	 * @var string
	 */
	protected $name = "never_let_me_go";


	/**
	 * nonce用に接頭辞をつけて返す
	 *
	 * @param string $action
	 * @return string
	 */
	public function nonce_action($action){
		return $this->name."_".$action;
	}

	/**
	 * wp_nonce_fieldのエイリアス
	 *
	 * @param string $action
	 */
	public function nonce_field($action){
		wp_nonce_field($this->nonce_action($action), $this->nonce_key);
	}

	/**
	 * Delete current user account
	 *
	 * @return void
	 */
	public function delete_current_user(){
		/** @var \wpdb $wpdb */
		global $wpdb;
		$user_id = get_current_user_id();
		wp_logout();
		if( $this->option['keep_account'] ){
			delete_user_meta($user_id, $wpdb->prefix."capabilities");
			delete_user_meta($user_id, $wpdb->prefix."user_level");
			switch ($this->option['destroy_level']) {
				case 0:
					break;
				default:
					$login = uniqid("nlmg.", true);
					$pass = wp_generate_password(20);
					//Update user table
					$user_id = wp_update_user(array(
						'ID' => $user_id,
						'user_pass' => $pass,
						'user_email' => "",
						'display_name' => $this->i18n->_('Deleted User')
					));
					// Update user_login
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
			do_action('never_let_me_go', $user_id);
		}else{
			require_once ABSPATH."/wp-admin/includes/user.php";
			$assign_to = $this->option['assign_to'] ? $this->option['assign_to'] : null;
			wp_delete_user($user_id, apply_filters('nlmg_assign_to', $assign_to));
		}
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return Singleton
	 */
	public function __get($name){
		switch($name){
			case 'input':
				return Input::getInstance();
				break;
			case 'i18n':
				return i18n::getInstance();
				break;
			case 'nonce_key':
				return "_{$this->name}_nonce";
				break;
			case 'option':
				$option = get_option($this->name."_option", array());
				foreach( array(
					'enable' => 0,
					'resign_page' => 0,
					'assign_to' => 0,
					'keep_account' => 0,
					'destroy_level' => 1
				) as $key => $val ){
					if( !isset($option[$key]) ){
						$option[$key] = $val;
					}
				}
				return $option;
				break;
			case 'version':
				return NLMG_VERSION;
				break;
			case 'dir':
				return dirname(NLMG_BASE_FILE);
				break;
			case 'url':
				return plugin_dir_url(NLMG_BASE_FILE);
				break;
			default:
				// Do nothing
				return null;
				break;
		}
	}
}
