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
            __( 'Login/Register modal' ),
            __( 'Login/Register modal' ),
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

        printf( __( 'Do you like "Login and Register Modal" plugin? Please consider giving it a %1$sreview%2$s', 'lrm' ), '<a href="#0" class="button button-secondary" target="_blank">⭐⭐⭐⭐⭐ ', '</a>' );

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

        $general = $this->settings->add_section( __( 'General' ), 'general' );

        $general->add_group( __( 'Supported plugins' ), 'supported_plugins' )
            ->add_field( array(
                'slug'        => 'free_version',
                'name'        => __( 'Free version are compatible with:' ),
                'default'     => true,
                'render'      => array( $this, 'free_version_compatible' ),
                'sanitize'    => '__return_false',
            ) );

        $general->add_group( __( 'Terms' ), 'terms' )
            ->add_field( array(
                'slug'        => 'off',
                'name'        => __( 'Hide Terms box in Registration Form?' ),
                'default'     => true,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );
        
        $general->add_group( __( 'General' ), 'registration' )
            ->add_field( array(
                'slug'        => 'auto_login_after_registration',
                'name'        => __( 'Auto-login user after registration?' ),
                'default'     => true,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'reload_after_login',
                'name'        => __( 'Reload page after login/registration?' ),
                'default'     => true,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );

        $ADVANCED_SECTION = $this->settings->add_section( __( 'Advanced' ), 'advanced' );

        $ADVANCED_SECTION->add_group( __( 'Extra selectors (id, classes) to handle modal' ), 'selectors_mapping' )
            ->add_field( array(
                'slug'        => 'login',
                'name'        => __( 'Extra selectors for handle login modal?' ),
                'description' => __( 'Comma separated values for jQuery, example: <code>.popup_login, #popup_login</code> or <code>.popup_login_show</code>.' ),
                'default'     => '.popup_login',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'register',
                'name'        => __( 'Extra selectors for handle register modal?' ),
                'description' => __( 'Comma separated values for jQuery, example: <code>.popup_register, #popup_register</code> or <code>.popup_register_show</code>' ),
                'default'     => '.popup_register',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) );


        $EMAILS_SECTION = $this->settings->add_section( __( 'Emails' ), 'mails' );

        $EMAILS_SECTION->add_group( __( 'Registration' ), 'registration' )
            ->add_field( array(
                'slug'        => 'subject',
                'name'        => __( 'Subject' ),
                'default'     => __( '[YOUR BLOG NAME] Your username and password info' ),
                'description' => __( 'The email Subject to user about successful registration.' ),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'body',
                'name'        => __( 'Body' ),
                'default'     =>
                    __('You just registered on') . ' [YOUR BLOG NAME].' . "\r\n\r\n" .
                    __('Url to login:') . ' {{LOGIN_URL}}' . "\r\n\r\n" .
                    __('Username:') . ' {{USERNAME}}' . "\r\n\r\n" .
                    __('Your password is:') . ' {{PASSWORD}}',
                'addons'      => array(
                    'pretty'   => true,
                ),
                'description' => __( 'The email Body to user about successful registration. Allowed tags: {{USERNAME}}, {{PASSWORD}}, {{LOGIN_URL}}' ),
                'render'      => array( new CoreFields\Editor(), 'input' ),
                'sanitize'    => array( new CoreFields\Editor(), 'sanitize' ),
            ) );

        $EMAILS_SECTION->add_group( __( 'Lost password' ), 'lost_password' )
            ->add_field( array(
                'name'        => __( 'Subject' ),
                'default'     => __( '[YOUR BLOG NAME] Your new password' ),
                'slug'        => 'subject',
                'description' => __( 'The email Subject to user with new password.' ),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'name'        => __( 'Body' ),
                'default'        =>
                    __('Someone has requested a password reset for the following account:') . "\r\n\r\n" .
                    '{{LOGIN_URL}}' . "\r\n\r\n" .
                    __('Username:') . ' {{USERNAME}}' . "\r\n\r\n" .
                    __('Your new password is:') . ' {{PASSWORD}}' . "\r\n\r\n" .
                    __('Url to login:') . ' {{LOGIN_URL}}' . "\r\n\r\n",
                'slug'        => 'body',
                'addons'      => array(
                    'pretty'   => true,
                ),
                'description' => __( 'The email Body to user with new password. Allowed tags: {{USERNAME}}, {{PASSWORD}}, {{LOGIN_URL}}' ),
                'render'      => array( new CoreFields\Editor(), 'input' ),
                'sanitize'    => array( new CoreFields\Editor(), 'sanitize' ),
            ) );

        $MESSAGES_SECTION = $this->settings->add_section( __( 'Expressions' ), 'messages' );

        $MESSAGES_SECTION->add_group( __( 'Login' ), 'login' )
            ->add_field( array(
                'slug'        => 'heading',
                'name'        => __( 'Form heading' ),
                'default'        => 'Sign in',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'username',
                'name'        => __( 'Form: E-mail or Username' ),
                'default'        => 'E-mail or Username',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password',
                'name'        => __( 'Form: Password' ),
                'default'        => 'Password',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'remember-me',
                'name'        => __( 'Form: Remember me' ),
                'default'        => 'Remember me',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'button',
                'name'        => __( 'Form button: Login' ),
                'default'        => 'Login',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'forgot-password',
                'name'        => __( 'Link: Forgot your password?' ),
                'default'        => 'Forgot your password?',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            // == MESSAGES ==
            ->add_field( array(
                'slug'        => 'no_login',
                'name'        => __( 'Message: No Login/Email' ),
                'default'        => 'Please enter login/email!',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'no_pass',
                'name'        => __( 'Message: No Password' ),
                'default'        => 'Please enter password!',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'success',
                'name'        => __( 'Message: Login successful' ),
                'default'        => 'Login successful, reloading page...',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) );


        $MESSAGES_SECTION->add_group( __( 'Registration' ), 'registration' )
            ->add_field( array(
                'slug'        => 'heading',
                'name'        => __( 'Form heading' ),
                'default'        => 'New account',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'first-name',
                'name'        => __( 'Form: First name' ),
                'default'        => 'First name',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'last-name',
                'name'        => __( 'Form: Last name' ),
                'default'        => 'Last name',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'email',
                'name'        => __( 'Form: Email' ),
                'default'        => 'Email',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'terms',
                'name'        => __( 'Form: Terms' ),
                'default'        => 'I agree to the <a href=\'#0\'>Terms</a>',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'button',
                'name'        => __( 'Form button: Create account' ),
                'default'        => 'Create account',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            // == MESSAGES ==
            ->add_field( array(
                'slug'        => 'disabled',
                'name'        => __( 'Message: Registration is disabled' ),
                'default'        => 'Registration is disabled!',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'no_name',
                'name'        => __( 'Message: No First or Last name' ),
                'default'        => 'Please enter First and Last name!',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'wrong_email',
                'name'        => __( 'Message: Wrong email' ),
                'default'        => 'Please enter correct email!',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'success',
                'name'        => __( 'Message: Registration successful' ),
                'default'        => 'Registration successful, reloading page.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) );

        $MESSAGES_SECTION->add_group( __( 'Lost password' ), 'lost_password' )
            ->add_field( array(
                'slug'        => 'message',
                'name'        => __( 'Message' ),
                'default'        => 'Lost your password? Please enter your email address. You will receive mail with new password.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'email',
                'name'        => __( 'Form: E-mail or Username' ),
                'default'        => 'E-mail or Username',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'button',
                'name'        => __( 'Form button: Reset password' ),
                'default'        => 'Reset password',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'to_login',
                'name'        => __( 'Form button: Back to login' ),
                'default'        => 'Back to login',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            // Errors!
            ->add_field( array(
                'slug'        => 'invalid_email',
                'name'        => __( 'Message: Empty login' ),
                'default'        => 'Enter an username or e-mail address.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'email_not_exists',
                'name'        => __( 'Message: No user with that email address' ),
                'default'        => 'There is no user registered with that email address.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'user_not_exists',
                'name'        => __( 'Message: No user registered with that username' ),
                'default'        => 'There is no user registered with that username.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'invalid_email_or_username',
                'name'        => __( 'Message: Invalid username or e-mail address' ),
                'default'        => 'Invalid username or e-mail address.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'unable_send',
                'name'        => __( 'Message: Unable send email' ),
                'default'        => 'System is unable to send you mail contain your new password.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'something_wrong',
                'name'        => __( 'Message: Something went wrong' ),
                'default'        => 'Oops! Something went wrong while updating your account.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'success',
                'name'        => __( 'Message: Reset successful' ),
                'default'        => 'Check your email address for you new password.',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) );

        $MESSAGES_SECTION->add_group( __( 'Other' ), 'other' )
            ->add_field( array(
                'slug'        => 'close_modal',
                'name'        => __( 'Close modal text' ),
                'default'        => 'close',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )

            ->add_field( array(
                'slug'        => 'invalid_nonce',
                'name'        => __( 'Message: Invalid nonce' ),
                'default'        => 'Invalid nonce!',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) );


        if ( !class_exists('LRM_Pro') ) {

            $MESSAGES_SECTION = $this->settings->add_section( __( 'GET A PRO >>' ), 'get_a_pro' );

            $MESSAGES_SECTION->add_group( __( 'Why you should get PRO version?' ), 'main' )
                             ->add_field( array(
                                 'slug'     => 'heading',
                                 'name'     => __( 'PRO features' ),
                                 'render'   => array( $this, 'go_to_pro_section' ),
                                 'sanitize' => '__return_false',
                             ) );

        }

        do_action('lrm/register_settings', $this->settings);
    }


    /**
     * Get all settings
     * @uses   SettingsAPI Settings API class
     * @return array settings
     */
    public function go_to_pro_section() {

        include LRM_PATH . "/views/admin-go-to-pro.php";

        wp_enqueue_style('lrm-modal-settings', LRM_URL . '/assets/lrm-core-settings.css', false, LRM_ASSETS_VER);

    }

    /**
     * Get all settings
     * @uses   SettingsAPI Settings API class
     * @return array settings
     */
    public function free_version_compatible() {

        include LRM_PATH . "/views/admin-free_version_compatible.php";

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
     * @return mixed           field value or null if name not found
     */
    public function setting( $setting ) {

        return $this->settings->get_setting( $setting );

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