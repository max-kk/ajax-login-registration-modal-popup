<?php
/**
 * Lost password template
 * @since 1.51
 * @version 1.00
 */

$fields_required = ('both' === LRM_Settings::get()->setting('advanced/validation/type')) ? 'required' : '';

$rp_key = isset($_GET['key']) ? wp_unslash( $_GET['key'] ) : '';
$rp_login = isset($_GET['login']) ? wp_unslash( $_GET['login'] ) : '';

$errors = new WP_Error();

$rp_data = LRM_AJAX::_validate_password_reset($errors);

if ( $errors->get_error_code() ) :
    echo '<p class="lrm-form-message">';
    echo implode('<br/>', $errors->get_error_messages());
    echo '</p>';
    LRM_Core::get()->render_form( true, 'lost-password' );
    return;
endif;


list($rp_key, $rp_login) = $rp_data;

?>
<div class="lrm-restore-password">
    <div class="lrm-user-modal-container">

        <form class="lrm-form" action="#0" data-action="password-reset">

            <p class="lrm-form-message lrm-form-message--init"></p>

            <div class="fieldset fieldset--password1">
                <div class="lrm-position-relative">
                    <label class="image-replace lrm-password"><?php echo esc_attr( LRM_Settings::get()->setting('messages_pro/registration/password', true) ); ?></label>
                    <input name="password1" class="full-width has-padding has-border" id="lrm-password1" data-relation="lrm-password2" type="text"  placeholder="<?php echo esc_attr( LRM_Settings::get()->setting('messages_pro/registration/password', true) ); ?>" <?= $fields_required; ?> value="<?php echo esc_attr( wp_generate_password( 16 ) ); ?>" autocomplete="new-password">
                    <span class="lrm-error-message"></span>
                    <span class="hide-password hide-password--on" data-show="<?php echo LRM_Settings::get()->setting('messages/other/show_pass'); ?>" data-hide="<?php echo LRM_Settings::get()->setting('messages/other/hide_pass'); ?>"><?php echo LRM_Settings::get()->setting('messages/other/hide_pass'); ?></span>
                </div>
                <span class="lrm-pass-strength-result"></span>
            </div>

            <div class="fieldset fieldset--pw-weak">
                <div class="pw-weak" style="display: none;">
                    <label>
                        <input type="checkbox" name="pw_weak" class="pw-checkbox" />
                        <?php echo lrm_setting('messages/password/use_weak_password'); ?>
                    </label>
                </div>
            </div>

            <div class="fieldset">
                <p class="description indicator-hint lrm-password-hint"><?php echo wp_get_password_hint(); ?></p>
            </div>

            <?php
            /**
             * Fires following the 'Strength indicator' meter in the user password reset form.
             *
             * @since 3.9.0
             *
             * @param WP_User $user User object of the user whose password is being reset.
             */
            $user = wp_get_current_user();
            do_action( 'resetpass_form', $user );
            ?>

            <div class="fieldset">
                <button class="full-width has-padding" type="submit">
                    <?php echo LRM_Settings::get()->setting('messages/lost_password/button', true); ?>
                </button>
            </div>

            <?php wp_nonce_field( 'ajax-password-reset-nonce', 'security-password-reset' ); ?>
            <input type="hidden" name="lrm_action" value="password_reset">
            <input type="hidden" name="key" value="<?php echo esc_attr( $rp_key ); ?>" autocomplete="off" />
            <input type="hidden" name="login" value="<?php echo esc_attr( $rp_login ); ?>" autocomplete="off" />


        </form>
    </div>
</div>
