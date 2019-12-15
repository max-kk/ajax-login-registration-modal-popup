<div class="lrm-signin-section <?php echo !$users_can_register || $is_inline && $default_tab == 'login' ? 'is-selected' : ''; ?>"> <!-- log in form -->
	<form class="lrm-form" action="#0" data-action="login">
        <div class="lrm-fieldset-wrap">

            <div class="lrm-integrations lrm-integrations--login">
                <?php do_action( 'lrm/login_form/before' ); ?>
            </div>

            <p class="lrm-form-message lrm-form-message--init"></p>

            <div class="fieldset">
                <?php $username_label = esc_attr( lrm_setting('messages/login/username', true) ); ?>
                <label class="image-replace lrm-email lrm-ficon-mail" title="<?= $username_label; ?>"></label>
                <input name="username" class="full-width has-padding has-border" type="text" aria-label="<?= $username_label; ?>" placeholder="<?= $username_label; ?>" <?= $fields_required; ?> value="" autocomplete="username" data-autofocus="1">
                <span class="lrm-error-message"></span>
            </div>

            <div class="fieldset">
                <?php $pass_label = esc_attr( lrm_setting('messages/login/password', true) ); ?>
                <label class="image-replace lrm-password lrm-ficon-key" title="<?= $pass_label; ?>"></label>
                <input name="password" class="full-width has-padding has-border" type="password" aria-label="<?= $pass_label; ?>" placeholder="<?= $pass_label; ?>" <?= $fields_required; ?> value="">
                <span class="lrm-error-message"></span>
                <?php if ( apply_filters('lrm/login_form/allow_show_pass', true) ): ?>
                    <span class="hide-password lrm-ficon-eye" data-show="<?php echo lrm_setting('messages/other/show_pass'); ?>" data-hide="<?php echo lrm_setting('messages/other/hide_pass'); ?>" aria-label="<?php echo lrm_setting('messages/other/show_pass'); ?>"></span>
                <?php endif; ?>
            </div>

            <div class="fieldset">
                <?php if ( apply_filters('lrm/form/use_nice_checkbox', true) ): ?>
                    <label class="lrm-nice-checkbox__label lrm-remember-me-checkbox"><?php echo lrm_setting('messages/login/remember-me', true); ?>
                        <input type="checkbox" class="lrm-nice-checkbox lrm-remember-me" name="remember-me" checked>
                        <div class="lrm-nice-checkbox__indicator"></div>
                    </label>
                <?php else: ?>
                    <label class="lrm-remember-me-checkbox">
                        <input type="checkbox" class="lrm-remember-me" name="remember-me" checked>
                        <?php echo lrm_setting('messages/login/remember-me', true); ?>
                    </label>
                <?php endif; ?>
            </div>

            <div class="lrm-integrations lrm-integrations--login lrm-integrations-before-btn">
                <?php do_action( 'lrm_login_form' ); // Deprecated ?>
                <?php do_action( 'lrm/login_form' ); ?>
            </div>

            <div class="lrm-integrations-otp"></div>

        </div>

		<div class="fieldset fieldset--submit <?= esc_attr($fieldset_submit_class); ?>">
			<button class="full-width has-padding" type="submit">
				<?php echo lrm_setting('messages/login/button', true); ?>
			</button>
		</div>

        <div class="lrm-fieldset-wrap">
            <div class="lrm-integrations lrm-integrations--login">
                <?php do_action( 'lrm/login_form/after' ); ?>
            </div>
        </div>

		<input type="hidden" name="redirect_to" value="<?= $redirect_to; ?>">
		<input type="hidden" name="lrm_action" value="login">
		<input type="hidden" name="wp-submit" value="1">
		<!-- Fix for Eduma WP theme-->
		<input type="hidden" name="lp-ajax" value="login">

		<?php wp_nonce_field( 'ajax-login-nonce', 'security-login' ); ?>

		<!-- For Invisible Recaptcha plugin -->
		<span class="wpcf7-submit" style="display: none;"></span>
	</form>

	<p class="lrm-form-bottom-message"><a href="#0" class="lrm-switch-to--reset-password"><?php echo lrm_setting('messages/login/forgot-password', true); ?></a></p>
	<!-- <a href="#0" class="lrm-close-form">Close</a> -->
</div> <!-- lrm-login -->