<?php /* @var $this NeverLetMeGo\Admin */ ?>
<div class="wrap">
<h2>
    <?php $this->i18n->e('Never Let Me Go setting'); ?>
</h2>
<form method="post">
	<?php wp_nonce_field('nlmg_option'); ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th><label><?php $this->i18n->e('Allow user to self delete');?></label>
				<td>
					<label>
						<input type="radio" name="nlmg_enable" value="0"<?php checked($this->option['enable'] == 0) ?> />
						<?php $this->i18n->e('Disabled'); ?>
					</label><br />
					<label>
						<input type="radio" name="nlmg_enable" value="1"<?php checked($this->option['enable'] == 1) ?> />
						<?php $this->i18n->e('Enabled'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th><label for="nlmg_resign_page"><?php $this->i18n->e('Resign Page');?></label>
				<td>
					<select id="nlmg_resign_page" name="nlmg_resign_page">
						<option value="0"<?php selected($this->option['resign_page'] == 0) ?>><?php $this->i18n->e('No resign page'); ?></option>
						<?php
							$query = new WP_Query('post_type=page&post_status=any&posts_per_page=0');
							if( $query->have_posts() ): while( $query->have_posts() ): $query->the_post();
						?>
						<option value="<?php the_ID(); ?>"<?php selected($this->option['resign_page'] == get_the_ID()) ?>><?php the_title(); ?></option>
						<?php endwhile; endif; wp_reset_postdata(); ?>
					</select>
					<p class="description">
						<?php $this->i18n->e('Resign page means the static page which have form to resign. <br />If not specified, user can delete himself on profile page of admin panel.');?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label><?php $this->i18n->e('Resign Way');?></label>
				<td>
					<label>
						<input type="radio" name="nlmg_keep_account" value="0"<?php checked($this->option['keep_account'] == 0) ?> />
						<strong><?php $this->i18n->e('Normal'); ?></strong>...<?php $this->i18n->e('Delete from database'); ?>
					</label><br >
					<label>
						<input type="radio" name="nlmg_keep_account" value="1"<?php checked($this->option['keep_account'] == 1) ?> />
						<strong><?php $this->i18n->e('Advanced'); ?></strong>...<?php $this->i18n->e('Make user account unavailable and keep data'); ?>
					</label>
					<p class="description">
						<?php printf($this->i18n->_('If you choose "%1$s", all data related to the user will be deleted.<br /> If not, the user account will be replaced to unavailabe account and whole data will be kept in your database.'), $this->i18n->_('Delete from database'));?><br />
						<?php $this->i18n->e('To delete related information, see description below.<br />Please be carefull with your country\'s low on other\'s privacy.');?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label><?php $this->i18n->e('Assign to'); ?></label></th>
				<td>
					<label>
						<?php $this->i18n->e('User ID'); ?>
						<input type="text" class="small-text" id="nlmg_assign_to" name="nlmg_assign_to" value="<?php echo intval($this->option['assign_to']); ?>" />
					</label>
					<div class="inc-search-container">
						<input type="text" class="regular-text" id="user-inc-search" placeholder="<?php $this->i18n->e('Input user name or e-mail to find user ID'); ?>" />
						<img class="loader toggle" src="<?php echo $this->url; ?>assets/img/ajax-loader.gif" width="16" height="11" />
						<ul id="user-inc-search-result"></ul>
					</div>
					<p class="description">
						<?php printf($this->i18n->_('If you choose <strong>%s</strong>, You can assign resigning user\'s contents to particular user. i.e. in UGC site, assigning resigning\'s contents to the pseudo user(deleted user).'), $this->i18n->_('Delete from database')); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label for="nlmg_destroy_level"><?php $this->i18n->e('Destroy Level');?></label>
				<td>
					<select id="nlmg_destroy_level" name="nlmg_destroy_level">
						<?php foreach(array(
							'0' => $this->i18n->_('Nothing changed.'),
							'1' => $this->i18n->_('Make credential hashed')
						) as $level => $desc): ?>
						<option value="<?php echo $level;?>"<?php selected($this->option['destroy_level'] == $level) ?>><?php echo "{$level} - {$desc}"; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						<?php printf($this->i18n->_('User information will be changed when user delete his own account.<br />If you don\'t want this, you can keep infomration by select "%s".'), $this->i18n->_('Nothing changed.'));?>
					</p>
				</td>

			</tr>
		</tbody>
	</table>
	<?php submit_button(); ?>
</form>
	<hr />
<h3><span class="dashicons dashicons-welcome-write-blog"></span> <?php $this->i18n->e('How to create Resign Page'); ?></h3>
<p>
	<?php $this->i18n->e('If you choose some resign page to publicly display, you can make show messag before resigning and after.'); ?><br />
	<?php $this->i18n->e('Split assigned page\'s content with &lt;!--nextpage--&gt;.<br /> 1st page will be shown before resigning and 2nd after.'); ?><br />
	<?php $this->i18n->e('Here is an example:'); ?>
</p>
<pre class="brush: php">
<?php $this->i18n->e('You are about to resign from our web magazine.'); ?>　
<?php $this->i18n->e('Are you sure to delete your account?'); ?>　
<?php $this->i18n->e('All of your data on our service will be deleted and can\'t be restored.'); ?>　
&lt;!--nextpage--&gt;　
<?php $this->i18n->e('Your account has been deleted successfully.'); ?>　
<?php $this->i18n->e('We miss you and hope to see you again.'); ?>　
<?php ?>
</pre>
<h3><span class="dashicons dashicons-editor-help"></span> <?php $this->i18n->e('How to treat user data'); ?></h3>
<p>
	<?php printf($this->i18n->_('In case you choose <strong>"%s"</strong>, Your user\'s data will remain on your database.'), $this->i18n->_('Make user account unavailable and keep data')); ?><br />
	<?php $this->i18n->e('But in most cases, you might want personal data like email or address to be deleted.'); ?><br />
	<?php $this->i18n->e('For this purpose, action hook is available. Write the code below in your theme\'s <em>functions.php</em>.'); ?>
</p>
<pre class="brush: php">
/**
 * <?php $this->i18n->e('This function are executed when user delete himself with this plugin'); ?> 
 * @param int $user_id <?php $this->i18n->e('User id to delete'); ?> 
 * @return void
 */
function _my_delete_func($user_id){
	//<?php $this->i18n->e('Now you get user_id and manage data how you like.'); ?> 
	//<?php $this->i18n->e('For example, you can delete user_meta &quot;address&quot;'); ?> 
	delete_user_meta($user_id, 'address'); 
	//<?php $this->i18n->e('Furthermore, You can call action hook for other plugins.'); ?> 
	do_action('delete_user', $user_id); 
	do_action('deleted_user', $user_id); 
}
add_action('never_let_me_go', '_my_delete_func');
</pre>
</div>

<h3><span class="dashicons dashicons-admin-tools"></span> <?php $this->i18n->e('Filter Hooks') ?></h3>
<pre class="brush: php">
/**
 * nlmg_resign_button_label
 *
 * <?php $this->i18n->e('Button label for resign form.') ?>　
 *
 * @param string $label
 * @param int $user_id User ID
 * @return string
 */

/**
 * nlmg_resign_confirm_label
 *
 * <?php $this->i18n->e('Confirm dialog label when user click delete account button.') ?>　
 *
 * @param string $confirm
 * @param int $user_id
 * @return string
 */
</pre>
