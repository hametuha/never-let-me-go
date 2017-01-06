<?php

namespace NeverLetMeGo;


use NeverLetMeGo\Pattern\Application;


class Admin extends Application
{


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
	protected function __construct( $settings = array() ){
		add_action('admin_init', array($this, 'adminInit'));
		add_action("admin_menu", array($this, "adminMenu"));
		add_action("admin_notices", array($this, "adminNotices"));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		// Add Action links on plugin lists.
		add_filter('plugin_action_links', array($this, 'pluginPageLink'), 500, 2);
	}

	/**
	 * Add menu
	 */
	public function adminMenu(){
		add_options_page($this->i18n->_('Never Let Me Go setting'), $this->i18n->_("Resign Setting"), 'delete_users', 'nlmg', array($this, 'render'));
	}

	/**
	 * Executed on admin_init
	 */
	public function adminInit(){
		if( defined('DOING_AJAX') && DOING_AJAX ){
			//Ajax Action for incremental search
			add_action('wp_ajax_nlmg_user_search', array($this, 'incSearch'));
		}else{
			// Update options
			if( $this->input->verify_nonce('nlmg_option') ){
				$option = array();
				$option['enable'] = (int) $this->input->post('nlmg_enable');
				$option['resign_page'] = (int) $this->input->post('nlmg_resign_page');
				$option['keep_account'] = (int) $this->input->post('nlmg_keep_account');
				$option['destroy_level'] = (int) $this->input->post('nlmg_destroy_level');
				$option['assign_to'] = (int) $this->input->post('nlmg_assign_to');
				if( update_option($this->name.'_option', $option) ){
					$this->add_message($this->i18n->_('Option updated.'));
				}else{
					$this->add_message($this->i18n->_('Option failed to updated.', true));
				}
			}
			// Delete account on admin panel
			if( defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE && is_user_logged_in() && $this->input->verify_nonce('nlmg_delete_on_admin')){
				$this->delete_current_user();
				wp_redirect(wp_login_url());
				exit;
			}
			// Add resign button on admin panel
			if( $this->option['enable'] && $this->option['resign_page'] == 0 ){
				add_action('show_user_profile', array($this, 'resignButton'));
			}
		}
	}


	/**
	 * Enqueue Javascript on admin panel
	 *
	 * @param string $page
	 */
	public function enqueueScripts($page){
		if( 'settings_page_nlmg' == $page ){
			$ext = WP_DEBUG ? '' : '.min';
			wp_enqueue_script('syntax-init', $this->url."dist/js/onload.js", array(), $this->version, true);
			wp_localize_script('syntax-init', 'NLMG', array(
				'endpoint' => admin_url('admin-ajax.php'),
				'action' => 'nlmg_user_search',
				'noResults' => $this->i18n->_('No results'),
				'found' => $this->i18n->_('%% found.')
			));
			wp_enqueue_style('nlmg-ajax', $this->url.'dist/css/admin.css', array(), $this->version);
		}
	}

	/**
	 * 管理画面にメッセージを表示する
	 *
	 * @return void
	 */
	public function adminNotices(){
		if( !empty($this->admin_error) ){
			printf('<div class="error"><p>%s</p></div>', implode('<br />', $this->admin_error));
		}
		if( !empty($this->admin_message) ){
			printf('<div class="updated"><p>%s</p></div>', implode('<br />', $this->admin_message));
		}
	}

	/**
	 * 管理画面に表示するメッセージを追加する
	 *
	 * @param string $string
	 * @param boolean $error (optional) trueにするとエラーメッセージ
	 * @return void
	 */
	public function add_message($string, $error = false){
		if( $error ){
			$this->admin_error[] = (string) $string;
		}else{
			$this->admin_message[] = (string) $string;
		}
	}

	/**
	 * Add action link on plugin lists
	 *
	 * @param array $links
	 * @param string $file
	 * @return string
	 */
	public function pluginPageLink($links, $file){
		if( false !== strpos($file, "never-let-me-go") ){
			$link = '<a href="'.admin_url('options-general.php?page=nlmg').'">'.__('Settings').'</a>';
			array_unshift( $links, $link);
		}
		return $links;
	}


	/**
	 * Create resign button on admin panel
	 *
	 * @param \WP_User
	 * @return void
	 */
	public function resignButton($user){
		?>
		<hr />
		<h3><?php $this->i18n->e('Delete Account'); ?></h3>
		<p>
			<?php $this->i18n->e('You can delete your account by putting the button below.'); ?>
		</p>
		<p class="right">
			<a class="button" href="<?php echo wp_nonce_url(admin_url('profile.php'), 'nlmg_delete_on_admin');?>" onclick="if(!confirm('<?php echo esc_js($this->i18n->_('Are you sure to delete your account? This action is not cancelable.')); ?>')) return false;"><?php $this->i18n->e("Delete"); ?></a>
		</p>
		<hr />
	<?php
	}

	/**
	 * Render options page
	 *
	 * @return void
	 */
	public function render(){
		include $this->dir.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."setting.php";
	}

	/**
	 * Returns user object by incremental search
	 */
	public function incSearch(){
		$result = array(
			'status' => false,
			'total' => 0,
			'results' => array()
		);
		if( current_user_can('manage_options') ){
			if( $this->input->post('query') ){
				/** @var \wpdb $wpdb */
				global $wpdb;
				$query = '%'.$this->input->post('query').'%';
				$sql = <<<SQL
					SELECT SQL_CALC_FOUND_ROWS
						ID, display_name
					FROM {$wpdb->users}
					WHERE user_login LIKE %s
					   OR user_email LIKE %s
					   OR display_name LIKE %s
					LIMIT 10
SQL;
				$result['results'] = $wpdb->get_results($wpdb->prepare($sql, $query, $query, $query), ARRAY_A);
				$result['total'] = (int) $wpdb->get_var("SELECT FOUND_ROWS()");
				$result['status'] = (boolean) $result['total'];
			}
		}
		wp_send_json($result);
	}
}
