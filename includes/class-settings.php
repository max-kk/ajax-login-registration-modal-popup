<?php

use underDEV\Utils\Settings\CoreFields;

/**
 * Class LRM_Settings
 *
 * File is modified a bit:
 * login-registration-modal\vendor\underdev\settings\views\settings-page.php
 */
class LRM_Settings {
    protected static $instance;
    /**
     * @var \underDEV\Utils\Settings
     */
    protected $settings;
    protected $page_id = 'login-and-register-popup';

    public function __construct() {

        // init library with your handle
        $this->settings = new underDEV\Utils\Settings( 'lrm' );

        require_once LRM_PATH .  "/includes/vendor/class-settings-field--text.php";

        // register menu as always
        add_action( 'admin_menu', array( $this, 'register_menu' ) );

        // register some settings
        add_action( 'init', array( $this, 'register_settings' ) );

        add_action( 'admin_notices', array( $this, 'beg_for_review' ) );
        
        if ( isset($_GET['action']) && $_GET['action'] === 'dismiss_rem_beg_message' ) {
            $this->dismiss_beg_message();
        }

        if ( isset($_GET['action']) && $_GET['action'] === 'lrm_reset_translations' && current_user_can('manage_options') ) {
            $this->_reset_translations();
        }

    }

    public function register_menu() {

        // pass the page hook to library to load scripts only on settings pages
        $this->settings->page_hook = add_options_page(
            'Login/Register modal',
            'Login/Register modal',
            'manage_options',
            $this->page_id,
            array( $this->settings, 'settings_page' )
        );

    }


    /**
     * Display notice with review beg
     * @return void
     */
    public function beg_for_review() {

        if ( !defined("LRM_PRO_VERSION") ) {

            echo '<div class="notice notice-info notification-notice"><p>';

            printf(
                'Looks like newer version of "AJAX Login and Registration modal popup PR0" plugin available! Please login yo your cabinet and <a href="%s" target="_blank">download it</a>!',
                'https://maxim-kaminsky.com/shop/my-account/orders/'
            );

            echo '</p></div>';
        }

        $screen = get_current_screen();

        if ( $screen->id != 'settings_page_' . $this->page_id ) {
            return;
        }

        if ( get_option( 'rem_beg_message' ) ) {
            return;
        }

        echo '<div class="notice notice-info notification-notice"><p>';

        printf( __( 'Do you like "Login and Register Modal" plugin? Please consider giving it a %1$sreview%2$s', 'ajax-login-and-registration-modal-popup' ), '<a href="https://wordpress.org/support/plugin/ajax-login-and-registration-modal-popup/reviews/#new-post" class="button button-secondary" target="_blank">⭐⭐⭐⭐⭐ ', '</a>' );

        echo '<a href="' . add_query_arg( array('action'=>'dismiss_rem_beg_message', '_wpnonce' => wp_create_nonce('lrm-beg-dismiss')) ) . '" class="dismiss-beg-message button" type="submit" style="float: right;">';
        _e( 'I already reviewed it', 'ajax-login-and-registration-modal-popup' );
        echo '</a>';

        echo '</p></div>';

    }

    /**
     * Dismiss beg message
     * @return object       json encoded response
     */
    public function dismiss_beg_message() {

        check_admin_referer( 'lrm-beg-dismiss' );

        update_option( 'lrm_beg_message', 'dismissed' );

    }
    


    public function register_settings() {

        $general = $this->settings->add_section( __( 'General', 'ajax-login-and-registration-modal-popup' ), 'general' );

        $general->add_group( __( 'Installation steps', 'ajax-login-and-registration-modal-popup' ), 'installation_steps' )
            ->add_field( array(
                'slug'        => 'installation_steps',
                'name'        => __('How to integrate modal on your site:', 'ajax-login-and-registration-modal-popup' ),
                'default'     => true,
                'render'      => array( $this, '_render__text_section' ),
                'sanitize'    => '__return_false',
                'addons' => array('section_file'=>'how-to-integrate'),
            ) );

        $general->add_group( __( 'Supported plugins', 'ajax-login-and-registration-modal-popup' ), 'supported_plugins' )
            ->add_field( array(
                'slug'        => 'free_version',
                'name'        => __('Free version are compatible with:', 'ajax-login-and-registration-modal-popup' ),
                'default'     => true,
                'render'      => array( $this, '_render__text_section' ),
                'sanitize'    => '__return_false',
                'addons' => array('section_file'=>'free-version-compatible'),
            ) );

        $general->add_group( __( 'Terms', 'ajax-login-and-registration-modal-popup' ), 'terms' )
            ->add_field( array(
                'slug'        => 'off',
                'name'        => __('Hide Terms box in Registration Form?', 'ajax-login-and-registration-modal-popup' ),
                'default'     => false,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );
        
        $general->add_group( __( 'General', 'ajax-login-and-registration-modal-popup' ), 'registration' )
            ->add_field( array(
                'slug'        => 'user_must_confirm_email',
                'name'        => __('User must confirm email after registration?', 'ajax-login-and-registration-modal-popup' ),
                'description' => __('If this option is enabled - the user won\'t automatically be logged into his account. He will need to open the email with his login information and enter them on the Log In tab.', 'ajax-login-and-registration-modal-popup' ),
                'default'     => false,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'reload_after_login',
                'name'        => __('Reload page after login/registration?', 'ajax-login-and-registration-modal-popup' ),
                'default'     => true,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );

        $ADVANCED_SECTION = $this->settings->add_section( __( 'Advanced', 'ajax-login-and-registration-modal-popup' ), 'advanced' );

        $ADVANCED_SECTION->add_group( __( 'Extra selectors (id, classes) to handle modal', 'ajax-login-and-registration-modal-popup' ), 'selectors_mapping' )
            ->add_field( array(
                'slug'        => 'login',
                'name'        => __('Extra selectors to handle log in modal?', 'ajax-login-and-registration-modal-popup' ),
                'description' => 'Comma separated values for jQuery, example: <code>.popup_login, #popup_login</code> or <code>.popup_login_show</code>.',
                'default'     => '.popup_login',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'register',
                'name'        => __('Extra selectors to handle register modal?', 'ajax-login-and-registration-modal-popup' ),
                'description' => 'Comma separated values for jQuery, example: <code>.popup_register, #popup_register</code> or <code>.popup_register_show</code>',
                'default'     => '.popup_register',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
        ->description( __('Use your custom selector to find button/link for attach modal.', 'ajax-login-and-registration-modal-popup' ) );


        $EMAILS_SECTION = $this->settings->add_section( __( 'Emails', 'ajax-login-and-registration-modal-popup' ), 'mails' );

        $EMAILS_SECTION->add_group( __( 'Registration', 'ajax-login-and-registration-modal-popup' ), 'registration' )
            ->add_field( array(
                'slug'        => 'subject',
                'name'        => __('Subject', 'ajax-login-and-registration-modal-popup'),
                'default'     => __( '[YOUR BLOG NAME] Your username and password info', 'ajax-login-and-registration-modal-popup' ),
                'description' => __( 'The email Subject to user about successful registration.', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'body',
                'name'        => __('Body', 'ajax-login-and-registration-modal-popup' ),
                'default'     =>
                    'You just registered on' . ' [YOUR BLOG NAME].' . "\r\n\r\n" .
                    'Url to login:' . ' {{LOGIN_URL}}' . "\r\n\r\n" .
                    'Username:' . ' {{USERNAME}}' . "\r\n\r\n" .
                    'Your password is:' . ' {{PASSWORD}}',
                'addons'      => array(
                    'pretty'   => true,
                ),
                'description' => 'The email Body to user about successful registration. Allowed tags: {{USERNAME}}, {{PASSWORD}}, {{LOGIN_URL}}',
                'render'      => array( new CoreFields\Editor(), 'input' ),
                'sanitize'    => array( new CoreFields\Editor(), 'sanitize' ),
            ) );

        $EMAILS_SECTION->add_group( __( 'Lost password', 'ajax-login-and-registration-modal-popup' ), 'lost_password' )
            ->add_field( array(
                'name'        => __('Subject', 'ajax-login-and-registration-modal-popup' ),
                'default'     => __( '[YOUR BLOG NAME] Your new password', 'ajax-login-and-registration-modal-popup' ),
                'slug'        => 'subject',
                'description' => __( 'The email Subject to user with new password.', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'name'        => __('Body', 'ajax-login-and-registration-modal-popup' ),
                'default'        =>
                    'Someone has requested a password reset for the following account:' . "\r\n\r\n" .
                    '{{LOGIN_URL}}' . "\r\n\r\n" .
                    __('Username:', 'ajax-login-and-registration-modal-popup') . ' {{USERNAME}}' . "\r\n\r\n" .
                    __('Your new password is:', 'ajax-login-and-registration-modal-popup') . ' {{PASSWORD}}' . "\r\n\r\n" .
                    __('Url to login:', 'ajax-login-and-registration-modal-popup') . ' {{LOGIN_URL}}' . "\r\n\r\n",
                'slug'        => 'body',
                'addons'      => array(
                    'pretty'   => true,
                ),
                'description' => __( 'The email Body to user with new password. Allowed tags: {{USERNAME}}, {{PASSWORD}}, {{LOGIN_URL}}', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new CoreFields\Editor(), 'input' ),
                'sanitize'    => array( new CoreFields\Editor(), 'sanitize' ),
            ) );

        $MESSAGES_SECTION = $this->settings->add_section( __( 'Expressions', 'ajax-login-and-registration-modal-popup' ), 'messages' );
        
        $MESSAGES_SECTION->add_group( __( 'Login', 'ajax-login-and-registration-modal-popup' ), 'login' )
             ->add_field( array(
                 'slug'        => 'heading',
                 'name'        => __('Form heading', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Sign in', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'username',
                 'name'        => __('Form: Email or Username', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Email or Username', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'password',
                 'name'        => __('Form: Password', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Password', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'remember-me',
                 'name'        => __('Form: Remember me', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Remember me', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'button',
                 'name'        => __('Form button: Login', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Log in', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'forgot-password',
                 'name'        => __('Link: Forgot your password?', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Forgot your password?', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
            // == MESSAGES ==
             ->add_field( array(
                'slug'        => 'no_login',
                'name'        => __('Message: No Username/Email', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Please enter your Username/email!', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'no_pass',
                 'name'        => __('Message: No Password', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Please enter your password!', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'success',
                 'name'        => __('Message: Login successful', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Login successful, reloading page...', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
            ->description( 'Please help <a class="button button-primary" target="_blank" href="https://translate.wordpress.org/projects/wp-plugins/ajax-login-and-registration-modal-popup">translate plugin</a> to your language!' );


        $MESSAGES_SECTION->add_group( __( 'Registration', 'ajax-login-and-registration-modal-popup' ), 'registration' )
             ->add_field( array(
                 'slug'        => 'heading',
                 'name'        => __('Form heading', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('New account', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'first-name',
                 'name'        => __('Form: First name', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('First name', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'last-name',
                 'name'        => __('Form: Last name', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Last name', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'email',
                 'name'        => __('Form: Email', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Email', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )

//            ->add_field( array(
//                'slug'        => 'password_repeat',
//                'name'        => __('Form: Repeat Password', 'ajax-login-and-registration-modal-popup' ),
//                'default'        => __('Repeat Password', 'ajax-login-and-registration-modal-popup'),
//                'render'      => array( new LRM_Field_Text(), 'input' ),
//                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
//            ) )
             ->add_field( array(
                 'slug'        => 'terms',
                 'name'        => __('Form: Terms', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('I agree with the <a href=\'#0\'>Terms</a>', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'button',
                 'name'        => __('Form button: Create account', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Create account', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
            // == MESSAGES ==
                         ->add_field( array(
                'slug'        => 'disabled',
                'name'        => __('Message: Registration is disabled', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Registration is disabled!', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
             ->add_field( array(
                 'slug'        => 'no_name',
                 'name'        => __('Message: No First or Last Name', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Please enter your First and Last Name!', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'wrong_email',
                 'name'        => __('Message: Wrong email', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Please enter a correct email!', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'success',
                 'name'        => __('Message: Registration successful', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Registration was successful, reloading page.', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'success_please_login',
                 'name'        => __('Message: Registration successful', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Registration was successful. We have sent you an email with your login information. Please use them to log into your account.', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) );

        $MESSAGES_SECTION->add_group( __( 'Lost password', 'ajax-login-and-registration-modal-popup' ), 'lost_password' )
             ->add_field( array(
                 'slug'        => 'message',
                 'name'        => __('Message', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Lost your password? Please enter your email address. You will receive mail with new password.', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'email',
                 'name'        => __('Form: Email or Username', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Email or Username', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'button',
                 'name'        => __('Form button: Reset password', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Reset password', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'to_login',
                 'name'        => __('Form button: Back to login', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Back to login', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )
                // Errors!
                 ->add_field( array(
                    'slug'        => 'invalid_email',
                    'name'        => __('Message: Missing login', 'ajax-login-and-registration-modal-popup' ),
                    'default'        => __('Enter an username or email address.', 'ajax-login-and-registration-modal-popup'),
                    'render'      => array( new LRM_Field_Text(), 'input' ),
                    'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
                ) )
                 ->add_field( array(
                     'slug'        => 'email_not_exists',
                     'name'        => __('Message: No user registered with that email address', 'ajax-login-and-registration-modal-popup' ),
                     'default'        => __('There is no user registered with that email address.', 'ajax-login-and-registration-modal-popup'),
                     'render'      => array( new LRM_Field_Text(), 'input' ),
                     'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'user_not_exists',
                     'name'        => __('Message: No user registered with that username', 'ajax-login-and-registration-modal-popup' ),
                     'default'        => __('There is no user registered with that username.', 'ajax-login-and-registration-modal-popup'),
                     'render'      => array( new LRM_Field_Text(), 'input' ),
                     'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'invalid_email_or_username',
                     'name'        => __('Message: Invalid username or e-mail address', 'ajax-login-and-registration-modal-popup' ),
                     'default'        => __('Invalid username or e-mail address.', 'ajax-login-and-registration-modal-popup'),
                     'render'      => array( new LRM_Field_Text(), 'input' ),
                     'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'unable_send',
                     'name'        => __('Message: Unable to send email', 'ajax-login-and-registration-modal-popup' ),
                     'default'        => __('System is unable to send you the mail containing your new password.', 'ajax-login-and-registration-modal-popup'),
                     'render'      => array( new LRM_Field_Text(), 'input' ),
                     'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'something_wrong',
                     'name'        => __('Message: Something went wrong', 'ajax-login-and-registration-modal-popup' ),
                     'default'        => __('Oops! Something went wrong while updating your account.', 'ajax-login-and-registration-modal-popup'),
                     'render'      => array( new LRM_Field_Text(), 'input' ),
                     'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'success',
                     'name'        => __('Message: Reset successful', 'ajax-login-and-registration-modal-popup' ),
                     'default'        => __('Check your mailbox to access your new password.', 'ajax-login-and-registration-modal-popup'),
                     'render'      => array( new LRM_Field_Text(), 'input' ),
                     'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
                 ) );

        $MESSAGES_SECTION->add_group( __( 'Other', 'ajax-login-and-registration-modal-popup' ), 'other' )
             ->add_field( array(
                 'slug'        => 'close_modal',
                 'name'        => __('Close modal text', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('close', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )

             ->add_field( array(
                 'slug'        => 'show_pass',
                 'name'        => __('Show password', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Show', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )

             ->add_field( array(
                 'slug'        => 'hide_pass',
                 'name'        => __('Hide password', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Hide', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) )

             ->add_field( array(
                 'slug'        => 'invalid_nonce',
                 'name'        => __('Message: Security token is expired', 'ajax-login-and-registration-modal-popup' ),
                 'default'        => __('Security token is expired! Please refresh the page!', 'ajax-login-and-registration-modal-popup'),
                 'render'      => array( new LRM_Field_Text(), 'input' ),
                 'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
             ) );
        

        if ( !class_exists('LRM_Pro') ) {

            $MESSAGES_SECTION = $this->settings->add_section( 'GET A PRO >>',  'get_a_pro' );

            $MESSAGES_SECTION->add_group( 'Why get PRO version?', 'main' )
                             ->add_field( array(
                                 'slug'     => 'heading',
                                 'name'     => __('PRO features', 'ajax-login-and-registration-modal-popup' ),
                                 'render'   => array( $this, '_render__text_section' ),
                                 'sanitize' => '__return_false',
                                 'addons' => array('section_file'=>'go-to-pro'),
                             ) );

        }

        do_action('lrm/register_settings', $this->settings);
    }


    /**
     * @param underDEV\Utils\Settings\Field     $field
     *
     * @since 1.11
     */
    public function _render__text_section( $field ) {
        if ( $section_file = $field->addon('section_file') ) {
            include LRM_PATH . "/views/admin/settings-section/{$section_file}.php";
            wp_enqueue_style('lrm-modal-settings', LRM_URL . '/assets/lrm-core-settings.css', false, LRM_ASSETS_VER);
        }
    }

    /**
     * Get all settings
     * @uses   underDEV\SettingsAPI Settings API class
     * @return array settings
     */
    public function settings() {

        return $this->settings->get_settings();

    }

    /**
     * Get single setting value
     * @uses   SettingsAPI Settings API class
     * @param  string $setting setting section/group/field separated with /
     * @param  bool do_stripslashes
     * @return mixed           field value or null if name not found
     */
    public function setting( $setting, $do_stripslashes = false) {

        $value = $this->settings->get_setting( $setting );
        return $do_stripslashes ? stripslashes( $value ) : $value;

    }

    private function _reset_translations() {
        delete_option( "lrm_messages" );
        echo "Reset done!";
        die();
    }

    /**
     * @return LRM_Settings
     */
    public static function get(){
        if ( !self::$instance ) {
            self::$instance = new LRM_Settings();
        }

        return self::$instance;
    }

}