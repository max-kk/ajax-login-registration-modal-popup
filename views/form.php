<div class="lrm-user-modal" style="visibility: hidden;"> <!-- this is the entire modal form, including the background -->
    <div class="lrm-user-modal-container"> <!-- this is the container wrapper -->
        <ul class="lrm-switcher">
            <li><a href="#0" class="lrm-switch-to-link lrm-switch-to--login">
                    <?php echo LRM_Settings::get()->setting('messages/login/heading'); ?>
                </a></li>
            <li><a href="#0" class="lrm-switch-to-link lrm-switch-to--register">
                    <?php echo LRM_Settings::get()->setting('messages/registration/heading'); ?>
                </a></li>
        </ul>

        <div id="lrm-login"> <!-- log in form -->
            <form class="lrm-form" action="#0">
                <p class="lrm-form-message lrm-form-message--init"></p>

                <p class="fieldset">
                    <label class="image-replace lrm-email" for="signin-email"><?php echo esc_attr( LRM_Settings::get()->setting('messages/login/username') ); ?></label>
                    <input  name="username" class="full-width has-padding has-border" id="signin-email" type="text" placeholder="<?php echo esc_attr( LRM_Settings::get()->setting('messages/login/username') ); ?>" required value="">
                    <span class="lrm-error-message"></span>
                </p>

                <p class="fieldset">
                    <label class="image-replace lrm-password" for="signin-password"><?php echo esc_attr( LRM_Settings::get()->setting('messages/login/password') ); ?></label>
                    <input name="password" class="full-width has-padding has-border" id="signin-password" type="text"  placeholder="<?php echo esc_attr( LRM_Settings::get()->setting('messages/login/password') ); ?>" required value="">
                    <span class="lrm-error-message"></span>
                    <a href="#0" class="hide-password">Hide</a>
                </p>

                <div class="lrm-integrations lrm-integrations--login">
                    <?php do_action( 'lrm_login_form' ); ?>
                </div>

                <p class="fieldset">
                    <input type="checkbox" id="remember-me" name="remember-me" checked>
                    <label for="remember-me"><?php echo LRM_Settings::get()->setting('messages/login/remember-me'); ?></label>
                </p>

                <p class="fieldset">
                    <button class="full-width has-padding" type="submit">
                        <?php echo LRM_Settings::get()->setting('messages/login/button'); ?>
                    </button>
                </p>
                <input type="hidden" name="lrm_action" value="login">
                <input type="hidden" name="wp-submit" value="1">

                <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
            </form>

            <p class="lrm-form-bottom-message"><a href="#0"><?php echo LRM_Settings::get()->setting('messages/login/forgot-password'); ?></a></p>
            <!-- <a href="#0" class="lrm-close-form">Close</a> -->
        </div> <!-- lrm-login -->

        <div id="lrm-signup"> <!-- sign up form -->
            <form class="lrm-form" action="#0">
                <p class="lrm-form-message lrm-form-message--init"></p>
                
                <p class="fieldset">
                    <label class="image-replace lrm-username" for="signup-first-name"><?php echo esc_attr( LRM_Settings::get()->setting('messages/registration/first-name') ); ?></label>
                    <input name="first-name" class="full-width has-padding has-border" id="signup-first-name" type="text" placeholder="<?php echo esc_attr( LRM_Settings::get()->setting('messages/registration/first-name') ); ?>" required>
                    <span class="lrm-error-message"></span>
                </p>
                <p class="fieldset">
                    <label class="image-replace lrm-username" for="signup-last-name"><?php echo esc_attr( LRM_Settings::get()->setting('messages/registration/last-name') ); ?></label>
                    <input name="last-name" class="full-width has-padding has-border" id="signup-last-name" type="text" placeholder="<?php echo esc_attr( LRM_Settings::get()->setting('messages/registration/last-name') ); ?>" required>
                    <span class="lrm-error-message"></span>
                </p>

                <p class="fieldset">
                    <label class="image-replace lrm-email" for="signup-email"><?php echo esc_attr( LRM_Settings::get()->setting('messages/registration/email') ); ?></label>
                    <input name="email" class="full-width has-padding has-border" id="signup-email" type="email" placeholder="<?php echo esc_attr( LRM_Settings::get()->setting('messages/registration/email') ); ?>" required>
                    <span class="lrm-error-message"></span>
                </p>

                <div class="lrm-integrations lrm-integrations--register">
                    <?php
                    /**
                     * Fires following the 'Email' field in the user registration form.
                     *
                     * @since 2.1.0
                     */
                    do_action( 'lrm_register_form' );
                    ?>
                </div>

                <?php if( LRM_Settings::get()->setting('general/terms/off') ): ?>
                 <p class="fieldset">
                        <input type="checkbox" id="accept-terms">
                        <label for="accept-terms"><?php echo LRM_Settings::get()->setting('messages/registration/terms'); ?></label>
                    </p>
                <?php endif; ?>

                <p class="fieldset">
                    <button class="full-width has-padding" type="submit">
                        <?php echo LRM_Settings::get()->setting('messages/registration/button'); ?>
                    </button>
                </p>

                <input type="hidden" name="lrm_action" value="signup">
                <input type="hidden" name="wp-submit" value="1">
                <?php wp_nonce_field( 'ajax-signup-nonce', 'security' ); ?>
            </form>

            <!-- <a href="#0" class="lrm-close-form">Close</a> -->
        </div> <!-- lrm-signup -->

        <div id="lrm-reset-password"> <!-- reset password form -->
            <form class="lrm-form" action="#0">

                <p class="lrm-form-message"><?php echo LRM_Settings::get()->setting('messages/lost_password/message'); ?></p>

                <p class="fieldset">
                    <label class="image-replace lrm-email" for="reset-email"><?php echo LRM_Settings::get()->setting('messages/lost_password/email'); ?></label>
                    <input class="full-width has-padding has-border" name="user_login" id="reset-email" type="text" required placeholder="<?php echo esc_attr( LRM_Settings::get()->setting('messages/lost_password/email') ); ?>">
                    <span class="lrm-error-message"></span>
                </p>

                <div class="lrm-integrations lrm-integrations--reset-pass">
                    <?php
                    /**
                     * Fires inside the lostpassword form tags, before the hidden fields.
                     *
                     * @since 2.1.0
                     */
                    do_action( 'lrm_lostpassword_form' ); ?>
                </div>

                <input type="hidden" name="lrm_action" value="lostpassword">
                <input type="hidden" name="wp-submit" value="1">
                <?php wp_nonce_field( 'ajax-forgot-nonce', 'security' ); ?>

                <p class="fieldset">
                    <button class="full-width has-padding" type="submit">
                        <?php echo LRM_Settings::get()->setting('messages/lost_password/button'); ?>
                    </button>
                </p>
            </form>

            <p class="lrm-form-bottom-message"><a href="#0"><?php echo LRM_Settings::get()->setting('messages/lost_password/to_login'); ?></a></p>
        </div> <!-- lrm-reset-password -->
        <a href="#0" class="lrm-close-form"><?php echo LRM_Settings::get()->setting('messages/other/close_modal'); ?></a>
    </div> <!-- lrm-user-modal-container -->
</div> <!-- lrm-user-modal -->


<script type="text/html" id="tpl-lrm-button-loader">
<span class="lrm-button-loader">
    <svg version="1.1" id="L4" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 40" enable-background="new 0 0 0 0" xml:space="preserve">
      <circle fill="#ffffff" stroke="none" cx="30" cy="20" r="6">
          <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.1"/>
      </circle>
        <circle fill="#ffffff" stroke="none" cx="50" cy="20" r="6">
            <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.2"/>
        </circle>
        <circle fill="#ffffff" stroke="none" cx="70" cy="20" r="6">
            <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.3"/>
        </circle>
    </svg>
</span>
</script>