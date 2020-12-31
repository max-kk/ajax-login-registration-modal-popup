<div class="lrm-reset-password-section <?php echo $users_can_register && $default_tab == 'lost-password' ? 'is-selected' : ''; ?>"> <!-- reset password form -->
	<form class="lrm-form js-lrm-form" action="#0" data-action="lost-password">

        <div class="lrm-fieldset-wrap">
            <p class="lrm-form-message"><?php echo lrm_setting('messages/lost_password/message', true); ?></p>

            <div class="fieldset">
                <?php $email_label = esc_attr( lrm_setting('messages/lost_password/email', true) ); ?>
                <label class="image-replace lrm-email lrm-ficon-mail" title="<?= $email_label; ?>"></label>
                <input class="full-width has-padding has-border" name="user_login" type="text" <?= $fields_required; ?> placeholder="<?= $email_label; ?>" data-autofocus="1" aria-label="<?= $email_label; ?>">
                <span class="lrm-error-message"></span>
            </div>

            <div class="lrm-integrations lrm-integrations--reset-pass">
                <?php
                /**
                 * Fires inside the lostpassword form tags, before the hidden fields.
                 *
                 * @since 2.1.0
                 * @deprecated
                 */
                do_action( 'lrm_lostpassword_form' );
                // New action since 1.42
                do_action( 'lrm/lostpassword_form' ); ?>
            </div>

            <input type="hidden" name="lrm_action" value="lostpassword">
            <input type="hidden" name="wp-submit" value="1">
            <?php wp_nonce_field( 'ajax-forgot-nonce', 'security-lostpassword' ); ?>

        </div>

		<div class="fieldset fieldset--submit <?= esc_attr($fieldset_submit_class); ?>">
			<button class="full-width has-padding" type="submit">
				<?php echo lrm_setting('messages/lost_password/button', true); ?>
			</button>
		</div>
		<!-- For Invisible Recaptcha plugin -->
		<span class="wpcf7-submit" style="display: none;"></span>

	</form>

	<p class="lrm-form-bottom-message"><a href="#0" class="lrm-switch-to--login"><?php echo lrm_setting('messages/lost_password/to_login', true); ?></a></p>
</div> <!-- lrm-reset-password -->