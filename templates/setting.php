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
					</label>
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
						<?php $this->e('Resign page means the static page which have form to resign. If not specified, user can delete himself on profile page of admin panel.');?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label><?php $this->e('Resign Way');?></label>
				<td>
					<label>
						<input type="radio" name="nlmg_keep_account" value="0"<?php if($this->option['keep_account'] == 0) echo ' checked="checked"'?> />
						<?php $this->e('Delete from database'); ?>
					</label>
					<label>
						<input type="radio" name="nlmg_keep_account" value="1"<?php if($this->option['keep_account'] == 1) echo ' checked="checked"'?> />
						<?php $this->e('Make user account unavailable and keep data'); ?>
					</label>
					<p class="description">
						<?php printf($this->_('If you choose "%1$s", all data related to the user will be deleted. If not, the user account will be replaced to unavailabe account and whole data will be kept in your database.'), $this->_('Delete from database'));?>
						<?php $this->e('To delete related information, see description below.');?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php submit_button(); ?>
</form>
<h3><?php $this->e('How to treat user data'); ?></h3>
<p>
	<?php $this->e('In case you choose "%s", Your user\'s data will remain on your database. But in most cases, you might want personal data like email or address to be deleted.'); ?>
	<?php $this->e('For this purpose, action hook is available. Write the code below in your theme\'s <em>functions.php</em>.'); ?>
</p>
<pre>

</pre>