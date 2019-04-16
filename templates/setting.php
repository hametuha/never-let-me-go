<?php /* @var $this NeverLetMeGo\Admin */ ?>
<div class="wrap nlmg">
    <h2>
        <img class="nlmg-logo" src="<?php echo $this->url ?>dist/img/icon-nlmg.png" align="Never Let Me go" height="32"
             width="32"/>
		<?php esc_html_e( 'Never Let Me Go setting', 'never-let-me-go' ); ?>
        <div style="clear:left;"></div>
    </h2>
    <form method="post">
		<?php wp_nonce_field( 'nlmg_option' ); ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th><label><?php esc_html_e( 'Allow user to self delete', 'never-let-me-go' ); ?></label>
                <td>
                    <label>
                        <input type="radio" name="nlmg_enable"
                               value="0"<?php checked( $this->option['enable'] == 0 ) ?> />
						<?php esc_html_e( 'Disabled', 'never-let-me-go' ); ?>
                    </label><br/>
                    <label>
                        <input type="radio" name="nlmg_enable"
                               value="1"<?php checked( $this->option['enable'] == 1 ) ?> />
						<?php esc_html_e( 'Enabled', 'never-let-me-go' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th><label for="nlmg_resign_page"><?php _e( 'Resign Page', 'never-let-me-go' ); ?></label>
                <td>
                    <select id="nlmg_resign_page" name="nlmg_resign_page">
                        <option value="0"<?php selected( $this->option['resign_page'] == 0 ) ?>><?php esc_html_e( 'No resign page', 'never-let-me-go' ); ?></option>
						<?php
						$query = new WP_Query( 'post_type=page&post_status=any&posts_per_page=0' );
						if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
							?>
                            <option value="<?php the_ID(); ?>"<?php selected( $this->option['resign_page'] == get_the_ID() ) ?>><?php the_title(); ?></option>
						<?php endwhile; endif;
						wp_reset_postdata(); ?>
                    </select>
                    <p class="description">
						<?php esc_html_e( 'Resign page means the static page which have form to resign.', 'never-let-me-go' ) ?>
                        <br/>
						<?php esc_html_e( 'If not specified, user can delete himself on profile page of admin panel.', 'never-let-me-go' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Resign Way', 'never-let-me-go' ); ?></label>
                <td>
                    <label>
                        <input type="radio" name="nlmg_keep_account"
                               value="0"<?php checked( $this->option['keep_account'] == 0 ) ?> />
                        <strong><?php esc_html_e( 'Normal', 'never-let-me-go' ); ?></strong>...<?php _e( 'Delete all data', 'never-let-me-go' ); ?>
                    </label><br>
                    <label>
                        <input type="radio" name="nlmg_keep_account"
                               value="1"<?php checked( $this->option['keep_account'] == 1 ) ?> />
                        <strong><?php esc_html_e( 'Advanced', 'never-let-me-go' ); ?></strong>...<?php _e( 'Make user account unavailable and keep data', 'never-let-me-go' ); ?>
                    </label>
                    <p class="description">
						<?php  echo wp_kses_post( sprintf(
							__( 'If you choose "%1$s", all data related to the user will be deleted from database.<br /> If not, the user account will be replaced to unavailabe account and whole data will be kept in your database.', 'never-let-me-go' ),
							__( 'Delete all data', 'never-let-me-go' )
						) ); ?>
                        <br/>
						<?php esc_html_e( 'To delete related information, see description below.<br />Please be careful with your country\'s low on other\'s privacy.', 'never-let-me-go' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Assign to', 'never-let-me-go' ); ?></label></th>
                <td>
                    <label>
						<?php esc_html_e( 'User ID', 'never-let-me-go' ); ?>
                        <input type="text" class="regular-text" id="nlmg_assign_to" name="nlmg_assign_to"
                               placeholder="<?php _e( 'Input user ID or type to search...', 'never-let-me-go' ); ?>"
                               value="<?php echo is_numeric( $this->option['assign_to'] ) && $this->option['assign_to'] ? esc_attr( $this->option['assign_to'] ) : ''; ?>"/>
                    </label>
                    <p class="description">
						<?php  echo wp_kses_post( sprintf(
							__( 'If you choose <strong>%s</strong>, You can assign resigning user\'s contents to particular user. i.e. in UGC site, assigning resigning\'s contents to the pseudo user with name with "deleted user".', 'never-let-me-go' ),
							__( 'Delete all data', 'never-let-me-go' )
						) ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="nlmg_destroy_level"><?php _e( 'Destroy Level', 'never-let-me-go' ); ?></label>
                <td>
                    <select id="nlmg_destroy_level" name="nlmg_destroy_level">
						<?php foreach (
							array(
								'1' => __( 'Make credential hashed', 'never-let-me-go' ),
								'0' => __( 'Keep all data', 'never-let-me-go' ),
							) as $level => $desc
						) : ?>
                            <option value="<?php echo $level; ?>"<?php selected( $this->option['destroy_level'] == $level ) ?>><?php echo esc_html( $desc ); ?></option>
						<?php endforeach; ?>
                    </select>
                    <p class="description">
						<?php echo wp_kses_post( sprintf(
							__( 'If you choose <strong>"%1$s"</strong>, user credentials will be changed irreversibly on removal process for his privacy.', 'never-let-me-go' ),
							__( 'Make user account unavailable and keep data', 'never-let-me-go' )
						) ) ?>
                        <br/>
						<?php  echo wp_kses_post( sprintf(
							__( 'If you don\'t want this, you can keep information by select <strong>"%s"</strong> but it\'s not recommended.', 'never-let-me-go' ),
							__( 'Keep all data', 'never-let-me-go' )
						) ); ?>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
		<?php submit_button(); ?>
    </form>
	<?php
	/**
	 * nlmg_after_form
	 *
	 * Do something on admin screen
	 *
	 * @since 1.0.0
	 * @action nlmg_after_form
	 */
	do_action( 'nlmg_after_form' );
	?>
    <hr/>
    <h3>
        <span class="dashicons dashicons-welcome-write-blog"></span> <?php esc_html_e( 'How to create Resign Page', 'never-let-me-go' ); ?>
    </h3>
    <p class="description">
		<?php esc_html_e( 'If you choose some resign page to publicly display, you can make show messag before resigning and after.', 'never-let-me-go' ); ?>
    </p>
    <ol class="nlmg-list">
        <li>
			<?php echo wp_kses_post( __( 'Split assigned page\'s content with <code>&lt;!--nextpage--&gt;</code> tag.', 'never-let-me-go' ) ) ?>
        </li>
        <li>
			<?php esc_html_e( '1st page will be shown before resigning. Write some content for changing mind or inform your user about what to loose.', 'never-let-me-go' ); ?>
        </li>
        <li>
			<?php esc_html_e( 'User will be redirected to 2nd page after removal of their account. Write thank you message or something.', 'never-let-me-go' ); ?>
        </li>
    </ol>
    <p>
		<?php esc_html_e( 'Below is an example:', 'never-let-me-go' ); ?>
    </p>
    <pre class="nlmg-pre">
<?php esc_html_e( 'You are about to resign from our web magazine.', 'never-let-me-go' ); ?>

<?php esc_html_e( 'Are you sure to delete your account?', 'never-let-me-go' ); ?>

<?php esc_html_e( 'All of your data on our service will be deleted and can\'t be restored.', 'never-let-me-go' ); ?>

<span class="nlmg-tag">&lt;!--nextpage--&gt;</span>
<?php esc_html_e( 'Your account has been deleted successfully.', 'never-let-me-go' ); ?>

<?php esc_html_e( 'We miss you and hope to see you again.', 'never-let-me-go' ); ?>
</pre>
    <hr/>
    <h3><span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e( 'How to customize' ) ?></h3>
    <ul class="nlmg-list">
        <li>
            <?php esc_html_e( 'Avoid user from leaving on specific condition.', 'never-let-me-go' ) ?>
        </li>
        <li>
			<?php esc_html_e( 'Change button labels.', 'never-let-me-go' ) ?>
        </li>
        <li>
			<?php esc_html_e( 'Do something if user leaves your site.', 'never-let-me-go' ) ?>
        </li>
    </ul>
    <p>
		<?php echo wp_kses_post( sprintf( __( 'There are some hooks available. For more details, please visit our support site <a href="%s" target="_blank">Gianism.info</a>', 'never-let-me-go' ), add_query_arg( array(
			'utm_source'   => 'dashboard',
			'utm_campaign' => 'nlmg',
			'utm_medium'   => 'link',
		), 'https://gianism.info/add-on/never-let-me-go/' ) ) ) ?>
    </p>
</div>
