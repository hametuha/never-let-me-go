<?php do_action('admin_notice'); ?>
<?php /* @var $this Never_Let_Me_Go */ ?>
<div id="icon-users" class="icon32"><br></div>
<h2><?php $this->e('Never Let Me Go setting'); ?></h2>
<form method="post">
	<?php $this->nonce_field('nlmg_option'); ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th><label><?php $this->e('Allow user to self delete');?></label>
				<td>
					<label>
						<input type="radio" name="nlmg_enable" value="0"<?php if($this->option['enable'] == 0) echo ' checked="checked"'?> />
						<?php $this->e('Disabled'); ?>
					</label><br />
					<label>
						<input type="radio" name="nlmg_enable" value="1"<?php if($this->option['enable'] == 1) echo ' checked="checked"'?> />
						<?php $this->e('Enabled'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th><label for="nlmg_resign_page"><?php $this->e('Resign Page');?></label>
				<td>
					<select id="nlmg_resign_page" name="nlmg_resign_page">
						<option value="0"<?php if($this->option['resign_page'] == 0) echo ' selected="selected"'?>><?php $this->e('No resign page'); ?></option>
						<?php
							$query = new WP_Query('post_type=page&post_status=any&posts_per_page=0');
							if($query->have_posts()): while($query->have_posts()): $query->the_post();
						?>
						<option value="<?php the_ID(); ?>"<?php if($this->option['resign_page'] == get_the_ID()) echo ' selected="selected"'?>><?php the_title(); ?></option>
						<?php endwhile; endif; wp_reset_query(); ?>
					</select>
					<p class="description">
						<?php $this->e('Resign page means the static page which have form to resign. <br />If not specified, user can delete himself on profile page of admin panel.');?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label><?php $this->e('Resign Way');?></label>
				<td>
					<label>
						<input type="radio" name="nlmg_keep_account" value="0"<?php if($this->option['keep_account'] == 0) echo ' checked="checked"'?> />
						<strong><?php $this->e('Normal'); ?></strong>...<?php $this->e('Delete from database'); ?>
					</label><br >
					<label>
						<input type="radio" name="nlmg_keep_account" value="1"<?php if($this->option['keep_account'] == 1) echo ' checked="checked"'?> />
						<strong><?php $this->e('Advanced'); ?></strong>...<?php $this->e('Make user account unavailable and keep data'); ?>
					</label>
					<p class="description">
						<?php printf($this->_('If you choose "%1$s", all data related to the user will be deleted.<br /> If not, the user account will be replaced to unavailabe account and whole data will be kept in your database.'), $this->_('Delete from database'));?><br />
						<?php $this->e('To delete related information, see description below.<br />Please be carefull with your country\'s low on other\'s privacy.');?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label><?php $this->e('Assign to'); ?></label></th>
				<td>
					<label>
						<?php $this->e('User ID'); ?>
						<input type="text" class="small-text" id="nlmg_assign_to" name="nlmg_assign_to" value="<?php echo intval($this->option['assign_to']); ?>" />
					</label>
					<div class="inc-search-container">
						<input type="text" class="regular-text" id="user-inc-search" placeholder="<?php $this->e('Input user name or e-mail to find user ID'); ?>" />
						<img class="loader toggle" src="<?php echo $this->url; ?>assets/ajax-loader.gif" width="16" height="11" /> 
						<ul id="user-inc-search-result"></ul>
					</p>
					<p class="description">
						<?php printf($this->_('If you choose <strong>%s</strong>, You can assign resigning user\'s contents to particular user. i.e. in CGM site, assigning resigning\'s contents to the pseudo user(deleted user).'), $this->_('Delete from database')); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label for="nlmg_destroy_level"><?php $this->e('Destroy Level');?></label>
				<td>
					<select id="nlmg_destroy_level" name="nlmg_destroy_level">
						<?php foreach(array(
							'0' => $this->_('Nothing changed.'),
							'1' => $this->_('Make credential hashed')
						) as $level => $desc): ?>
						<option value="<?php echo $level;?>"<?php if($this->option['destroy_level'] == $level) echo ' selected="selected"'?>><?php echo "{$level} - {$desc}"; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						<?php printf($this->_('User information will be changed when user delete his own account.<br />If you don\'t want this, you can keep infomration by select "%s".'), $this->_('Nothing changed.'));?>
					</p>
				</td>

			</tr>
		</tbody>
	</table>
	<?php submit_button(); ?>
</form>
<h3><?php $this->e('How to create Resign Page'); ?></h3>
<p>
	<?php $this->e('If you choose some resign page to publicly display, you can make show messag before resigning and after.'); ?><br />
	<?php $this->e('Split assigned page\'s content with &lt;!--nextpage--&gt;.<br /> 1st page will be shown before resigning and 2nd after.'); ?><br />
	<?php $this->e('Here is an example:'); ?>
<pre class="brush: php">
<?php $this->e('You are about to resign from our web magazine.'); ?> 
<?php $this->e('Are you sure to delete your account?'); ?> 
<?php $this->e('All of your data on our service will be deleted and can\'t be restored.'); ?> 
&lt;!--nextpage--&gt;
<?php $this->e('Your account has been deleted successfully.'); ?> 
<?php $this->e('We miss you and hope to see you again.'); ?> 
<?php ?>
</pre>
</p>
<h3><?php $this->e('How to treat user data'); ?></h3>
<p>
	<?php printf($this->_('In case you choose <strong>"%s"</strong>, Your user\'s data will remain on your database.'), $this->_('Make user account unavailable and keep data')); ?><br />
	<?php $this->e('But in most cases, you might want personal data like email or address to be deleted.'); ?><br />
	<?php $this->e('For this purpose, action hook is available. Write the code below in your theme\'s <em>functions.php</em>.'); ?>
</p>
<pre class="brush: php">
/**
 * <?php $this->e('This function are executed when user delete himself with this plugin'); ?> 
 * @param int $user_id <?php $this->e('User id to delete'); ?> 
 * @return void
 */
function _my_delete_func($user_id){
	//<?php $this->e('Now you get user_id and manage data how you like.'); ?> 
	//<?php $this->e('For example, you can delete user_meta &quot;address&quot;'); ?> 
	delete_user_meta($user_id, 'address'); 
	//<?php $this->e('Furthermore, You can call action hook for other plugins.'); ?> 
	do_action('delete_user', $user_id); 
	do_action('deleted_user', $user_id); 
}
add_action('never_let_me_go', '_my_delete_func');
</pre>