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

        // register menu as always
        add_action( 'admin_menu', array( $this, 'register_menu' ) );

        // register some settings
        add_action( 'init', array( $this, 'register_settings' ) );

        add_action( 'admin_notices', array( $this, 'beg_for_review' ) );
        
        if ( isset($_GET['action']) && $_GET['action'] === 'dismiss_rem_beg_message' ) {
            $this->dismiss_beg_message();
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

        $screen = get_current_screen();

        if ( $screen->id != 'settings_page_' . $this->page_id ) {
            return;
        }

        if ( get_option( 'rem_beg_message' ) ) {
            return;
        }

        echo '<div class="notice notice-info notification-notice"><p>';

        printf( __( 'Do you like "Login and Register Modal" plugin? Please consider giving it a %1$sreview%2$s', 'lrm' ), '<a href="https://wordpress.org/support/plugin/ajax-login-and-registration-modal-popup/reviews/#new-post" class="button button-secondary" target="_blank">⭐⭐⭐⭐⭐ ', '</a>' );

        echo '<a href="' . add_query_arg( array('action'=>'dismiss_rem_beg_message', '_wpnonce' => wp_create_nonce('lrm-beg-dismiss')) ) . '" class="dismiss-beg-message button" type="submit" style="float: right;">';
        _e( 'I already reviewed it', 'lrm' );
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

        $general = $this->settings->add_section( __( 'General', 'lrm' ), 'general' );

        $general->add_group( __( 'Installation steps', 'lrm' ), 'installation_steps' )
            ->add_field( array(
                'slug'        => 'installation_steps',
                'name'        => __('How to integrate modal on your site:', 'lrm' ),
                'default'     => true,
                'render'      => array( $this, 'render__how_to_integrate' ),
                'sanitize'    => '__return_false',
            ) );

        $general->add_group( __( 'Supported plugins', 'lrm' ), 'supported_plugins' )
            ->add_field( array(
                'slug'        => 'free_version',
                'name'        => __('Free version are compatible with:', 'lrm' ),
                'default'     => true,
                'render'      => array( $this, 'free_version_compatible' ),
                'sanitize'    => '__return_false',
            ) );

        $general->add_group( __( 'Terms', 'lrm' ), 'terms' )
            ->add_field( array(
                'slug'        => 'off',
                'name'        => __('Hide Terms box in Registration Form?', 'lrm' ),
                'default'     => true,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );
        
        $general->add_group( __( 'General', 'lrm' ), 'registration' )
            ->add_field( array(
                'slug'        => 'allow_user_set_password',
                'name'        => __('Allow user set password during registration?', 'lrm' ),
                'default'     => false,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'auto_login_after_registration',
                'name'        => __('Auto-login user after registration?', 'lrm' ),
                'default'     => true,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'reload_after_login',
                'name'        => __('Reload page after login/registration?', 'lrm' ),
                'default'     => true,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );

        $ADVANCED_SECTION = $this->settings->add_section( __( 'Advanced', 'lrm' ), 'advanced' );

        $ADVANCED_SECTION->add_group( __( 'Extra selectors (id, classes) to handle modal', 'lrm' ), 'selectors_mapping' )
            ->add_field( array(
                'slug'        => 'login',
                'name'        => __('Extra selectors for handle login modal?', 'lrm' ),
                'description' => 'Comma separated values for jQuery, example: <code>.popup_login, #popup_login</code> or <code>.popup_login_show</code>.',
                'default'     => '.popup_login',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'register',
                'name'        => __('Extra selectors for handle register modal?', 'lrm' ),
                'description' => 'Comma separated values for jQuery, example: <code>.popup_register, #popup_register</code> or <code>.popup_register_show</code>',
                'default'     => '.popup_register',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) );


        $EMAILS_SECTION = $this->settings->add_section( __( 'Emails', 'lrm' ), 'mails' );

        $EMAILS_SECTION->add_group( __( 'Registration', 'lrm' ), 'registration' )
            ->add_field( array(
                'slug'        => 'subject',
                'name'        => __('Subject', 'lrm'),
                'default'     => __( '[YOUR BLOG NAME] Your username and password info', 'lrm' ),
                'description' => __( 'The email Subject to user about successful registration.', 'lrm' ),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'body',
                'name'        => __('Body', 'lrm' ),
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

        $EMAILS_SECTION->add_group( __( 'Lost password', 'lrm' ), 'lost_password' )
            ->add_field( array(
                'name'        => __('Subject', 'lrm' ),
                'default'     => __( '[YOUR BLOG NAME] Your new password', 'lrm' ),
                'slug'        => 'subject',
                'description' => __( 'The email Subject to user with new password.', 'lrm' ),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'name'        => __('Body', 'lrm' ),
                'default'        =>
                    'Someone has requested a password reset for the following account:' . "\r\n\r\n" .
                    '{{LOGIN_URL}}' . "\r\n\r\n" .
                    __('Username:', 'lrm') . ' {{USERNAME}}' . "\r\n\r\n" .
                    __('Your new password is:', 'lrm') . ' {{PASSWORD}}' . "\r\n\r\n" .
                    __('Url to login:', 'lrm') . ' {{LOGIN_URL}}' . "\r\n\r\n",
                'slug'        => 'body',
                'addons'      => array(
                    'pretty'   => true,
                ),
                'description' => __( 'The email Body to user with new password. Allowed tags: {{USERNAME}}, {{PASSWORD}}, {{LOGIN_URL}}', 'lrm' ),
                'render'      => array( new CoreFields\Editor(), 'input' ),
                'sanitize'    => array( new CoreFields\Editor(), 'sanitize' ),
            ) );

        $MESSAGES_SECTION = $this->settings->add_section( __( 'Expressions', 'lrm' ), 'messages' );
        
        $MESSAGES_SECTION->add_group( __( 'Login', 'lrm' ), 'login' )
             ->add_field( array(
                 'slug'        => 'heading',
                 'name'        => __('Form heading', 'lrm' ),
                 'default'        => __('Sign in', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'username',
                 'name'        => __('Form: E-mail or Username', 'lrm' ),
                 'default'        => __('E-mail or Username', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'password',
                 'name'        => __('Form: Password', 'lrm' ),
                 'default'        => __('Password', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'remember-me',
                 'name'        => __('Form: Remember me', 'lrm' ),
                 'default'        => __('Remember me', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
//             ->add_field( array(
//                 'slug'        => 'before-button',
//                 'name'        => __('Form: Text before button, like "We use cookies, etc"', 'lrm' ),
//                 'default'        => __('', 'lrm'),
//                 'render'      => array( new CoreFields\Text(), 'input' ),
//                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
//             ) )
             ->add_field( array(
                 'slug'        => 'button',
                 'name'        => __('Form button: Login', 'lrm' ),
                 'default'        => __('Login', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'forgot-password',
                 'name'        => __('Link: Forgot your password?', 'lrm' ),
                 'default'        => __('Forgot your password?', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
            // == MESSAGES ==
             ->add_field( array(
                'slug'        => 'no_login',
                'name'        => __('Message: No Login/Email', 'lrm' ),
                'default'        => __('Please enter login/email!', 'lrm'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'no_pass',
                 'name'        => __('Message: No Password', 'lrm' ),
                 'default'        => __('Please enter password!', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'success',
                 'name'        => __('Message: Login successful', 'lrm' ),
                 'default'        => __('Login successful, reloading page...', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) );


        $MESSAGES_SECTION->add_group( __( 'Registration', 'lrm' ), 'registration' )
             ->add_field( array(
                 'slug'        => 'heading',
                 'name'        => __('Form heading', 'lrm' ),
                 'default'        => __('New account', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'first-name',
                 'name'        => __('Form: First name', 'lrm' ),
                 'default'        => __('First name', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'last-name',
                 'name'        => __('Form: Last name', 'lrm' ),
                 'default'        => __('Last name', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'email',
                 'name'        => __('Form: Email', 'lrm' ),
                 'default'        => __('Email', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
            ->add_field( array(
                'slug'        => 'password',
                'name'        => __('Form: Password', 'lrm' ),
                'default'        => __('Password', 'lrm'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_good',
                'name'        => __('Form: Good Password', 'lrm' ),
                'default'        => __('Good Password', 'lrm'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_bad',
                'name'        => __('Form: Bad Password', 'lrm' ),
                'default'        => __('Bad Password', 'lrm'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_strong',
                'name'        => __('Form: Strong Password', 'lrm' ),
                'default'        => __('Strong Password', 'lrm'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_short',
                'name'        => __('Form: Too Short Password', 'lrm' ),
                'default'        => __('Too Short Password', 'lrm'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
//            ->add_field( array(
//                'slug'        => 'password_repeat',
//                'name'        => __('Form: Repeat Password', 'lrm' ),
//                'default'        => __('Repeat Password', 'lrm'),
//                'render'      => array( new CoreFields\Text(), 'input' ),
//                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
//            ) )
             ->add_field( array(
                 'slug'        => 'terms',
                 'name'        => __('Form: Terms', 'lrm' ),
                 'default'        => __('I agree to the <a href=\'#0\'>Terms</a>', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'button',
                 'name'        => __('Form button: Create account', 'lrm' ),
                 'default'        => __('Create account', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
            // == MESSAGES ==
                         ->add_field( array(
                'slug'        => 'disabled',
                'name'        => __('Message: Registration is disabled', 'lrm' ),
                'default'        => __('Registration is disabled!', 'lrm'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
             ->add_field( array(
                 'slug'        => 'no_name',
                 'name'        => __('Message: No First or Last name', 'lrm' ),
                 'default'        => __('Please enter First and Last name!', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'wrong_email',
                 'name'        => __('Message: Wrong email', 'lrm' ),
                 'default'        => __('Please enter correct email!', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'success',
                 'name'        => __('Message: Registration successful', 'lrm' ),
                 'default'        => __('Registration successful, reloading page.', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) );

        $MESSAGES_SECTION->add_group( __( 'Lost password', 'lrm' ), 'lost_password' )
             ->add_field( array(
                 'slug'        => 'message',
                 'name'        => __('Message', 'lrm' ),
                 'default'        => __('Lost your password? Please enter your email address. You will receive mail with new password.', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'email',
                 'name'        => __('Form: E-mail or Username', 'lrm' ),
                 'default'        => __('E-mail or Username', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'button',
                 'name'        => __('Form button: Reset password', 'lrm' ),
                 'default'        => __('Reset password', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
             ->add_field( array(
                 'slug'        => 'to_login',
                 'name'        => __('Form button: Back to login', 'lrm' ),
                 'default'        => __('Back to login', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
                // Errors!
                 ->add_field( array(
                    'slug'        => 'invalid_email',
                    'name'        => __('Message: Empty login', 'lrm' ),
                    'default'        => __('Enter an username or e-mail address.', 'lrm'),
                    'render'      => array( new CoreFields\Text(), 'input' ),
                    'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
                ) )
                 ->add_field( array(
                     'slug'        => 'email_not_exists',
                     'name'        => __('Message: No user with that email address', 'lrm' ),
                     'default'        => __('There is no user registered with that email address.', 'lrm'),
                     'render'      => array( new CoreFields\Text(), 'input' ),
                     'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'user_not_exists',
                     'name'        => __('Message: No user registered with that username', 'lrm' ),
                     'default'        => __('There is no user registered with that username.', 'lrm'),
                     'render'      => array( new CoreFields\Text(), 'input' ),
                     'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'invalid_email_or_username',
                     'name'        => __('Message: Invalid username or e-mail address', 'lrm' ),
                     'default'        => __('Invalid username or e-mail address.', 'lrm'),
                     'render'      => array( new CoreFields\Text(), 'input' ),
                     'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'unable_send',
                     'name'        => __('Message: Unable send email', 'lrm' ),
                     'default'        => __('System is unable to send you mail contain your new password.', 'lrm'),
                     'render'      => array( new CoreFields\Text(), 'input' ),
                     'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'something_wrong',
                     'name'        => __('Message: Something went wrong', 'lrm' ),
                     'default'        => __('Oops! Something went wrong while updating your account.', 'lrm'),
                     'render'      => array( new CoreFields\Text(), 'input' ),
                     'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
                 ) )
                 ->add_field( array(
                     'slug'        => 'success',
                     'name'        => __('Message: Reset successful', 'lrm' ),
                     'default'        => __('Check your email address for you new password.', 'lrm'),
                     'render'      => array( new CoreFields\Text(), 'input' ),
                     'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
                 ) );

        $MESSAGES_SECTION->add_group( __( 'Other', 'lrm' ), 'other' )
             ->add_field( array(
                 'slug'        => 'close_modal',
                 'name'        => __('Close modal text', 'lrm' ),
                 'default'        => __('close', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) )
    
             ->add_field( array(
                 'slug'        => 'invalid_nonce',
                 'name'        => __('Message: Invalid nonce', 'lrm' ),
                 'default'        => __('Invalid nonce!', 'lrm'),
                 'render'      => array( new CoreFields\Text(), 'input' ),
                 'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
             ) );
        

        if ( !class_exists('LRM_Pro') ) {

            $MESSAGES_SECTION = $this->settings->add_section( 'GET A PRO >>',  'get_a_pro' );

            $MESSAGES_SECTION->add_group( 'Why you should get PRO version?', 'main' )
                             ->add_field( array(
                                 'slug'     => 'heading',
                                 'name'     => __('PRO features', 'lrm' ),
                                 'render'   => array( $this, 'go_to_pro_section' ),
                                 'sanitize' => '__return_false',
                             ) );

        }

        do_action('lrm/register_settings', $this->settings);
    }


    public function go_to_pro_section() {

        include LRM_PATH . "/views/admin-go-to-pro.php";

        wp_enqueue_style('lrm-modal-settings', LRM_URL . '/assets/lrm-core-settings.css', false, LRM_ASSETS_VER);

    }

    public function free_version_compatible() {

        include LRM_PATH . "/views/admin-free_version_compatible.php";

        wp_enqueue_style('lrm-modal-settings', LRM_URL . '/assets/lrm-core-settings.css', false, LRM_ASSETS_VER);
    }

    /**
     * @since 1.01
     */
    public function render__how_to_integrate() {

        include LRM_PATH . "/views/admin-how_to_integrate.php";

        wp_enqueue_style('lrm-modal-settings', LRM_URL . '/assets/lrm-core-settings.css', false, LRM_ASSETS_VER);
    }

    /**
     * Get all settings
     * @uses   SettingsAPI Settings API class
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