<?php if ( $users_can_register ): ?>

	<div class="lrm-signup-section <?php echo $users_can_register && $default_tab == 'register' ? 'is-selected' : ''; ?>"> <!-- sign up form -->
		<?php if ( lrm_is_pro('1.28') && LRM_Pro_UltimateMember::is_ultimatemember_active() && lrm_setting( 'integrations/um/replace_with' ) ): ?>
			<?php LRM_Pro_UltimateMember::render_registration_form(); ?>
		<?php elseif ( lrm_is_pro('1.20') && LRM_Pro_BuddyPress::is_buddypress_active() && lrm_setting('integrations/bp/on') ): ?>
			<?php LRM_Pro_BuddyPress::render_registration_form(); ?>
		<?php elseif ( function_exists('rcp_registration_form') && lrm_setting('integrations/rcp/on') ): ?>
        <div class="lrm-form">
            <div class="lrm-integrations lrm-fieldset-wrap">
                <?php echo do_shortcode( lrm_setting('integrations/rcp/shortcode') ); ?>
            </div>
        </div>
		<?php else: ?>

			<form class="lrm-form" action="#0" data-action="registration" data-lpignore="true">

                <div class="lrm-fieldset-wrap lrm-form-message-wrap">
                    <p class="lrm-form-message lrm-form-message--init"></p>
                </div>

                <div class="lrm-fieldset-wrap">

                    <div class="lrm-integrations lrm-integrations--register">
                        <?php do_action( 'lrm/register_form/before' ); ?>
                    </div>

                    <?php if( has_action('lrm/register_form/render_fields') ): ?>
                        <?php do_action('lrm/register_form/render_fields'); ?>
                    <?php else: ?>
                        <?php if( ! lrm_setting('general_pro/all/hide_username') ): ?>
                            <div class="fieldset fieldset--username">
                                <?php $username_label = esc_attr( lrm_setting('messages/registration/username', true) ); ?>
                                <label class="image-replace lrm-username lrm-ficon-user" for="signup-username" title="<?= $username_label; ?>"></label>
                                <input name="username" class="full-width has-padding has-border" id="signup-username" type="text" placeholder="<?= $username_label; ?>" <?= $fields_required; ?> aria-label="<?= $username_label; ?>" autocomplete="off" data-lpignore="true">
                                <span class="lrm-error-message"></span>
                            </div>
                        <?php endif; ?>

                        <?php if( lrm_setting('general/registration/display_first_and_last_name') ): ?>
                            <div class="clearfix lrm-row">
                                <?php $fname_label = esc_attr( lrm_setting('messages/registration/first-name', true) );; ?>
                                <?php $lname_label = esc_attr( lrm_setting('messages/registration/last-name', true) ); ?>
                                <div class="lrm-col-half-width lrm-col-first fieldset--first-name lrm-col">
                                    <label class="image-replace lrm-username lrm-ficon-user" for="signup-first-name" title="<?= $fname_label; ?>"></label>
                                    <input name="first-name" class="full-width has-padding has-border" id="signup-first-name" type="text" placeholder="<?= $fname_label; ?>" <?= $fields_required; ?> aria-label="<?= $fname_label; ?>" autocomplete="off" data-lpignore="true">
                                    <span class="lrm-error-message"></span>
                                </div>
                                <div class="lrm-col-half-width lrm-col-last fieldset--last-name lrm-col">
                                    <label class="image-replace lrm-username lrm-ficon-user" for="signup-last-name" title="<?= $lname_label; ?>"></label>
                                    <input name="last-name" class="full-width has-padding has-border" id="signup-last-name" type="text" placeholder="<?= $lname_label; ?>" aria-label="<?= $lname_label; ?>" autocomplete="off" data-lpignore="true">
                                    <span class="lrm-error-message"></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="fieldset fieldset--email">
                            <?php $email_label = esc_attr( lrm_setting('messages/registration/email', true) ); ?>
                            <label class="image-replace lrm-email lrm-ficon-mail" for="signup-email" title="<?= $email_label; ?>"></label>
                            <input name="email" class="full-width has-padding has-border" id="signup-email" type="email" placeholder="<?= $email_label; ?>" <?= $fields_required; ?> autocomplete="off" aria-label="<?= $email_label; ?>">
                            <span class="lrm-error-message"></span>
                        </div>

                    <?php endif; ?>

                    <?php $pass_label = esc_attr( lrm_setting('messages/password/password', true) ); ?>
                    <?php if( lrm_setting('general_pro/all/allow_user_set_password') ): ?>
                        <div class="fieldset">
                            <div class="lrm-position-relative">
                                <label class="image-replace lrm-password lrm-ficon-key" for="signup-password" title="<?= $pass_label; ?>"></label>
                                <input name="password" class="full-width has-padding has-border" id="signup-password" type="password"  placeholder="<?= $pass_label; ?>" <?= $fields_required; ?> value="" autocomplete="new-password" aria-label="<?= $pass_label; ?>">
                                <span class="lrm-error-message"></span>
                                <span class="hide-password lrm-ficon-eye" data-show="<?php echo lrm_setting('messages/other/show_pass'); ?>" data-hide="<?php echo lrm_setting('messages/other/hide_pass'); ?>"></span>
                            </div>
                            <span class="lrm-pass-strength-result"></span>
                        </div>
                    <?php endif; ?>
                    <?php if( lrm_setting('general_pro/all/allow_user_set_password') && lrm_setting('general_pro/all/use_password_confirmation') ): ?>
                        <div class="fieldset">
                            <div class="lrm-position-relative">
                                <label class="image-replace lrm-password lrm-ficon-key" for="signup-password-confirmation"></label>
                                <input name="password-confirmation" class="full-width has-padding has-border" id="signup-password-confirmation" type="password"  placeholder="<?= $pass_label; ?>" <?= $fields_required; ?> value="" autocomplete="new-password">
                                <span class="lrm-error-message"></span>
                                <span class="hide-password lrm-ficon-eye" data-show="<?php echo lrm_setting('messages/other/show_pass'); ?>" data-hide="<?php echo lrm_setting('messages/other/hide_pass'); ?>"></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if( lrm_is_pro() && lrm_setting('user_role/general/on') ):
                        $active_roles_list = LRM_PRO_Roles_Manager::get_active_roles_flat();
                        $role_silent = in_array($role, $active_roles_list) && $role_silent ? true : false;
                        ?>
                        <div class="fieldset fieldset--user_role" style="<?= $role_silent ? 'display: none;' : '' ?>">
                            <label class="image-replace lrm-user_role lrm-ficon-lock" for="signup-username" title="<?= esc_attr(lrm_setting('messages/registration/user_role', true)); ?>"></label>
                            <select name="user_role" class="full-width has-padding has-border" id="user_role" required>
                                <option value=""><?php echo lrm_setting('messages/registration/user_role', true); ?></option>
                                <?php foreach ( $active_roles_list as $active_role_key => $active_role_label ) : ?>
                                    <option value="<?= $active_role_key; ?>" data-label="<?= esc_attr($active_role_label); ?>" <?php selected($active_role_label, $role) ?>><?= $active_role_label; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="lrm-error-message"></span>
                        </div>
                    <?php endif; ?>

                    <div class="lrm-integrations lrm-integrations--register">
                        <?php do_action( 'lrm_register_form' ); ?>
                        <?php do_action( 'lrm/register_form' ); ?>
                    </div>

                    <?php if( ! lrm_setting('general/terms/off') ): ?>
                        <div class="fieldset fieldset--terms">

                            <?php if ( apply_filters('lrm/form/use_nice_checkbox', true) ): ?>
                                <label class="lrm-nice-checkbox__label lrm-accept-terms-checkbox"><?php echo lrm_setting('messages/registration/terms', true); ?>
                                    <input type="checkbox" class="lrm-nice-checkbox lrm-accept-terms" name="registration_terms" value="yes">
                                    <span class="lrm-error-message"></span>
                                    <div class="lrm-nice-checkbox__indicator"></div>
                                </label>
                            <?php else: ?>
                                <label class="lrm-accept-terms-checkbox">
                                    <input type="checkbox" class="lrm-accept-terms" name="registration_terms" value="yes">
                                    <?php echo lrm_setting('messages/login/remember-me', true); ?>
                                    <span class="lrm-error-message"></span>
                                </label>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>

                    <div class="lrm-integrations lrm-integrations--register lrm-info lrm-info--register">
                        <?php do_action( 'lrm/register_form/before_button' ); ?>
                    </div>

				</div>

				<div class="fieldset fieldset--submit <?= esc_attr($fieldset_submit_class); ?>">
					<button class="full-width has-padding" type="submit">
						<?php echo lrm_setting('messages/registration/button', true); ?>
					</button>
				</div>

                <div class="lrm-fieldset-wrap">

                    <div class="lrm-integrations lrm-integrations--register">
                        <?php do_action( 'lrm/register_form/after' ); ?>
                    </div>

                </div>

				<input type="hidden" name="redirect_to" value="<?= $redirect_to; ?>">
				<input type="hidden" name="lrm_action" value="signup">
				<input type="hidden" name="wp-submit" value="1">

				<!-- Fix for Eduma WP theme-->
				<input type="hidden" name="is_popup_register" value="1">
				<?php wp_nonce_field( 'ajax-signup-nonce', 'security-signup' ); ?>
				<!-- For Invisible Recaptcha plugin -->
				<span class="wpcf7-submit" style="display: none;"></span>

			</form>

		<?php endif; ?>

		<!-- <a href="#0" class="lrm-close-form">Close</a> -->
	</div> <!-- lrm-signup -->

<?php endif; ?>