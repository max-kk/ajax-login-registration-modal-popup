<?php

defined( 'ABSPATH' ) || exit;

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

//        require_once LRM_PATH .  "/includes/settings/class-settings-field--text.php";
//        require_once LRM_PATH .  "/includes/settings/class-settings-field--textarea.php";
//        require_once LRM_PATH .  "/includes/settings/class-settings-field--textarea-html.php";
//        require_once LRM_PATH .  "/includes/settings/class-settings-field--textarea-html-extended.php";
//        require_once LRM_PATH .  "/includes/settings/class-settings-field--editor.php";

        // register menu as always
        add_action( 'admin_menu', array( $this, 'register_menu' ) );

        // register some settings
        add_action( 'init', array( $this, 'register_settings' ) );

        add_action( 'admin_notices', array( $this, 'beg_for_review' ) );

        lrm_dismissible_notice( 'v2',
            sprintf(
                '<strong>AJAX Login & registration modal notice:</strong> you have installed version 2.0 that contains a lot of updates and tweaks. Please review your settings and reconfigure <a href="%s">after-login/registration actions</a>!',
                admin_url('options-general.php?page=login-and-register-popup&section=redirects')
            )
        );

        if ( LRM_WPML_Integration::is_wpml_active() ) {
            lrm_dismissible_notice('wpml',
                sprintf(
                    '<strong>AJAX Login & registration modal notice:</strong> since version 2.0 WPML translation process is has been slightly changed: now you should do the translation in a plugin Settings instead of using WPML Strings Translations module. More in <a href="https://docs.maxim-kaminsky.com/lrm/kb/multi-language-support-via-wpml/">docs >></a>',
                    admin_url('options-general.php?page=login-and-register-popup&section=redirects')
                )
            );
        }

	    lrm_dismissible_notice( 'font-icons',
		    sprintf(
			    '<strong>AJAX Login & registration modal notice:</strong> since the Free version 2.04 you are able to use the Font Icons instead of the default SVG. Find the settings at a <a href="%s">Skins tab</a>!',
			    admin_url('options-general.php?page=login-and-register-popup&section=skins')
		    )
	    );

        $latest_pro_version = '1.50';

        if ( lrm_is_pro() && ! lrm_is_pro( $latest_pro_version ) && !defined("LRM_HIDE_PRO_UPDATE_NOTICE") ) {
            lrm_dismissible_notice('lrm_pro_update_1.50',
                sprintf(
                    'Looks like newer version %s of "AJAX Login and Registration modal popup PRO" plugin is available! Please go to Plugins menu and run the update or open your cabinet and <a href="%s" target="_blank">download it</a>!',
                    $latest_pro_version,
                    'https://maxim-kaminsky.com/shop/my-account/orders/'
                )
            );
        }

        add_action( 'underdev/settings/enqueue_scripts', array( $this, 'settings_enqueue_scripts' ) );

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

        if ( lrm_is_pro() ) {

            // Update notice for 1.18 > 1.20
            if (
                lrm_is_pro( 1.17 ) &&
                LRM_Pro_User_Verification::link_verification_is_on()
                && false ===  strpos( LRM_Settings::get()->setting('mails/registration/body'), '{{VERIFY_ACCOUNT_URL}}' )
            ) {
                echo '<div class="notice notice-error notification-notice"><p>';

                printf(
                    '"AJAX Login and Register Modal" warning: please add tag <code>{{VERIFY_ACCOUNT_URL}}</code> to "Registration" mail body in <a href="%s">Emails Section</a>, else user can\'t verify account and login to your site.',
                    admin_url('options-general.php?page=login-and-register-popup&section=mails')
                );

                echo '</p></div>';

            }
        }

        // Update notice for 1.18 > 1.20
        if ( false === strpos( LRM_Settings::get()->setting('mails/lost_password/body'), '{{CHANGE_PASSWORD_URL}}' ) ) {

            echo '<div class="notice notice-error notification-notice"><p>';

            printf(
                '"AJAX Login and Register Modal" warning: please update "Lost password" mail body in <a href="%s">Emails Section</a> (replace <code>{{PASSWORD}}</code> with <code>{{CHANGE_PASSWORD_URL}}</code>)',
                admin_url('options-general.php?page=login-and-register-popup&section=mails')
            );

            echo '</p></div>';

        }

        $screen = get_current_screen();

        if ( $screen->id != 'settings_page_' . $this->page_id ) {
            return;
        }

        // Update notice for 1.18 > 1.20
        if ( ! get_option("users_can_register") ) {

            echo '<div class="notice notice-error notification-notice"><p>';

            printf(
                '"AJAX Login and Register Modal" warning: registration is disabled in your Wordpress settings. Please go to <a href="%s">Settings => General</a> and enable option "Anyone can register".',
                admin_url('options-general.php')
            );

            echo '</p></div>';

        }

        if ( ! get_option( 'lrm_beg_message' ) ) {
            echo '<div class="notice notice-info notification-notice"><p>';

            printf( __( 'Do you like "Login and Register Modal" plugin? Please consider giving it a %1$sreview%2$s', 'ajax-login-and-registration-modal-popup' ), '<a href="https://wordpress.org/support/plugin/ajax-login-and-registration-modal-popup/reviews/#new-post" class="button button-secondary" target="_blank">⭐⭐⭐⭐⭐ ', '</a>' );

            echo '<a href="' . add_query_arg( array('action'=>'dismiss_rem_beg_message', '_wpnonce' => wp_create_nonce('lrm-beg-dismiss')) ) . '" class="dismiss-beg-message button" type="submit" style="float: right;">';
            _e( 'I already reviewed it', 'ajax-login-and-registration-modal-popup' );
            echo '</a>';

            echo '</p></div>';

        }
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
                'addons' => array('section_file'=>'how-to-integrate-modal'),
            ) )
            ->add_field( array(
                'slug'        => 'installation_inline',
                'name'        => __('How to integrate inline form on your site:', 'ajax-login-and-registration-modal-popup' ),
                'default'     => true,
                'render'      => array( $this, '_render__text_section' ),
                'sanitize'    => '__return_false',
                'addons' => array('section_file'=>'how-to-integrate-inline'),
            ) )
            ->add_field( array(
                'slug'        => 'installation_support',
                'name'        => __('If you need support:', 'ajax-login-and-registration-modal-popup' ),
                'default'     => true,
                'render'      => array( $this, '_render__text_section' ),
                'sanitize'    => '__return_false',
                'addons' => array('section_file'=>'support'),
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


        $user_must_confirm_email_description = __('If this option is enabled - the user won\'t automatically be logged into his account.', 'ajax-login-and-registration-modal-popup' ) . ' <br/>';

        if ( ! lrm_is_pro() ) {
            $user_must_confirm_email_description .= __('He will need to open the email with his login information and enter them on the Log In tab.', 'ajax-login-and-registration-modal-popup' );
        } else {
//            if ( LRM_Settings::get()->setting('general_pro/all/allow_user_set_password') ) {
//                $user_must_confirm_email_description .= '<strong>' . __('[ACTIVE]') . '</strong>';
//            }
//
            $user_must_confirm_email_description .= '<strong>' . __('[If user can\'t set password] ') . '</strong>' . __('He will need to open the email with his login information and enter them on the Log In tab.', 'ajax-login-and-registration-modal-popup' );

            $user_must_confirm_email_description .= '<br/>';
//
//            if ( ! LRM_Settings::get()->setting('general_pro/all/allow_user_set_password') ) {
//                $user_must_confirm_email_description .= '<strong>' . __('[ACTIVE]') . '</strong>';
//            }

            $user_must_confirm_email_description .= '<strong>' . __('[If user can set password] ') . '</strong>' . __('He will need to open the email and click verification link. Please add {{VERIFY_ACCOUNT_URL}} tag to Registration Email.', 'ajax-login-and-registration-modal-popup' );
        }

        $general->add_group( __( 'General', 'ajax-login-and-registration-modal-popup' ), 'registration' )
//            ->add_field( array(
//                'slug'        => 'user_must_confirm_email',
//                'name'        => __('User must confirm email after registration?', 'ajax-login-and-registration-modal-popup' ),
//                'description' => $user_must_confirm_email_description,
//                'default'     => false,
//                'render'      => array( new CoreFields\Checkbox(), 'input' ),
//                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
//            ) )
//            ->add_field( array(
//                'slug'        => 'reload_after_login',
//                'name'        => __('Reload page after login/registration?', 'ajax-login-and-registration-modal-popup' ),
//                'default'     => 'true',
//                'description' => 'During registration that option only work if «' . __('User must confirm email after registration?', 'ajax-login-and-registration-modal-popup' ) . '» option is disabled.',
//                'render'      => array( new CoreFields\Checkbox(), 'input' ),
//                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
//            ) )
            ->add_field( array(
                'slug'        => 'display_first_and_last_name',
                'name'        => __('Display First and Last name fields in Registration Form?', 'ajax-login-and-registration-modal-popup' ),
                'default'     => 'true',
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );

        /**
         * ============================================================================================
         * ============================================================================================
         */

        LRM_Pages_Manager::register_settings($this->settings);
        LRM_Redirects_Manager::register_settings($this->settings);

        /**
         * ============================================================================================
         * ============================================================================================
         */

        $SKINS_SECTION = $this->settings->add_section( __( 'Skins', 'ajax-login-and-registration-modal-popup' ), 'skins' );

        $skins_arr = LRM_Skins::i()->get_list();

        $SKINS_SECTION->add_group( __( 'Skins (modal design)', 'ajax-login-and-registration-modal-popup' ), 'skin' )
            ->add_field( array(
                'slug'        => 'current',
                'name'        => __('Select skin', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => $skins_arr,
                ),
                'default'     => 'default',
                'description' => sprintf( __('In a PRO version you will have <a href="%s" target="_blank">more skins</a>', 'ajax-login-and-registration-modal-popup' ), 'https://maxim-kaminsky.com/shop/product/ajax-login-and-registration-modal-popup-pro/' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'icons',
                'name'        => __('Select icons type', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => [
                    	'svg'       => 'Default SVG Icons',
                    	'icomoon'   => 'Icomoon font icons',
                    	'material'  => 'Material font icons',
                    	'fa4'       => 'Font awesome 4 font icons',
                    	'fa5-free'  => 'Font awesome 5 font icons',
                    ],
                ),
                'default'     => 'svg',
                'description' => '"Font awesome 5" is loading a full icons pack (bigger size), all other very minimal icons packs',
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) )
            ->description(
                sprintf(
                    'Skins colors you can customize in <a href="%s" class="button button-secondary">WP Customizer</a> with installed PRO version.',
                    admin_url('customize.php?autofocus[panel]=lrm_panel')
                )
            );

        /**
         * ============================================================================================
         * ============================================================================================
         */

        $ADVANCED_SECTION = $this->settings->add_section( __( 'Advanced', 'ajax-login-and-registration-modal-popup' ), 'advanced' );

        $ADVANCED_SECTION->add_group( __( 'Extra selectors (id, classes) to handle modal', 'ajax-login-and-registration-modal-popup' ), 'selectors_mapping' )
            ->add_field( array(
                'slug'        => 'login',
                'name'        => __('Extra selectors to handle log in modal?', 'ajax-login-and-registration-modal-popup' ),
                'description' => 'Comma separated values for jQuery, example: <code>.popup_login, #popup_login</code> or <code>.popup_login_show</code>.',
                'default'     => '',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'register',
                'name'        => __('Extra selectors to handle register modal?', 'ajax-login-and-registration-modal-popup' ),
                'description' => 'Comma separated values for jQuery, example: <code>.popup_register, #popup_register</code> or <code>.popup_register_show</code>',
                'default'     => '',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
        ->description( __('Use your custom selector to find button/link for attach modal.', 'ajax-login-and-registration-modal-popup' ) );

        $ADVANCED_SECTION->add_group( __( 'Data validation', 'ajax-login-and-registration-modal-popup' ), 'validation' )
            ->add_field( array(
                'slug'        => 'type',
                'name'        => __('Data validation method', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        'both'      => 'Both (browser and server)',
                        'server'    => 'Server only - more requests, no browser default messages',
                    ),
                ),
                'default'     => 'both',
                'description' => __('With using "server" method you can avoid displaying default browser "field invalid" messages and gives more customization options.', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) );

        $ADVANCED_SECTION->add_group( __( 'Debug', 'ajax-login-and-registration-modal-popup' ), 'debug' )
            ->add_field( array(
                'slug'        => 'ajax',
                'name'        => __('Enable debug mode for public AJAX requests. Required to simply find an error messages.', 'ajax-login-and-registration-modal-popup' ),
                'description' => __('Please disable it once problem was solved to improve security!', 'ajax-login-and-registration-modal-popup' ),
                'default'     => false,
                'addons'      => array('label' => __( 'Yes' )),
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );

        $ADVANCED_SECTION->add_group( __( 'Uninstall', 'ajax-login-and-registration-modal-popup' ), 'uninstall' )
            ->add_field( array(
                'slug'        => 'remove_all_data',
                'name'        => __('Remove all plugin settings on plugin deactivation?', 'ajax-login-and-registration-modal-popup' ),
                'default'     => false,
                'addons'      => array('label' => __( 'Yes for Free and Pro' )),
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) );


//        $ADVANCED_SECTION->add_group( __( 'Troubleshooting', 'ajax-login-and-registration-modal-popup' ), 'troubleshooting' )
//            ->add_field( array(
//                'slug'        => 'hook',
//                'name'        => __('When to call login/registration actions.', 'ajax-login-and-registration-modal-popup'),
//                'addons'      => array(
//                    'options'     => array(
//                        'wp_loaded'           => '"wp_loaded" - hook by Default [late]',
//                        'init'              => '"init" hook',
//                        'plugins_loaded'    => '"plugins_loaded" hook',
//                    ),
//                ),
//                'default'     => 'wp_loaded',
//                'description' => __('By default calls made very late to allow other plugins apply hooks/filters, like reCaptcha, WooCommerce, etc.
//                But for fix issues (like redirect that broken login process) with some plugins like s2member, etc you could play with this option.', 'ajax-login-and-registration-modal-popup' ),
//                'render'      => array( new CoreFields\Select(), 'input' ),
//                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
//            ) );

        $EMAILS_SECTION = $this->settings->add_section( __( 'Emails', 'ajax-login-and-registration-modal-popup' ), 'mails' );

        $EMAILS_SECTION->add_group( __( 'Mails', 'ajax-login-and-registration-modal-popup' ), 'mail' )
            ->add_field( array(
                'slug'        => 'format',
                'name'        => __('Email format', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        'plain'      => 'plain',
                        'text/html'  => 'text/html',
                        'wc-text/html'  => 'text/html using WooCommerce template (require WooCommerce plugin)',
                    ),
                ),
                'default'     => 'plain',
                'description' => sprintf(
                    __('To enable support of html tags - use text/html email format. More - <a href="%1$s" target="_blank">%1$s</a><br/> Option "use WooCommerce template" is experimental feature and could have issues with some WC plugins.', 'ajax-login-and-registration-modal-popup' ),
                    'http://blog.cakemail.com/html-vs-plain-text/'
                ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) );


        $EMAILS_SECTION->add_group( __( 'Registration', 'ajax-login-and-registration-modal-popup' ), 'registration', true )
            ->add_field( array(
                'slug'        => 'subject',
                'name'        => __('Subject', 'ajax-login-and-registration-modal-popup'),
                'default'     => __( '{{SITE_NAME}} Your username and password info', 'ajax-login-and-registration-modal-popup' ),
                'description' => __( 'The email Subject to user about successful registration.', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'body',
                'name'        => __('Body', 'ajax-login-and-registration-modal-popup' ),
                'default'     =>
                    __('You just registered on', 'ajax-login-and-registration-modal-popup') . ' {{SITE_NAME}}.' . "\r\n\r\n" .
                    __('Url to login:', 'ajax-login-and-registration-modal-popup') . ' {{LOGIN_URL}}' . "\r\n\r\n" .
                        __('Username:', 'ajax-login-and-registration-modal-popup') . ' {{USERNAME}}' . "\r\n\r\n" .
                            __('Your password is:', 'ajax-login-and-registration-modal-popup') . ' {{PASSWORD}}',
                'addons'      => array(
                    'pretty'   => true,
                ),
                'description' => 'The email Body to user about successful registration. Allowed tags: <code>{{SITE_NAME}}</code>, <code>{{SITE_URL}}</code>, <code>{{HOME_URL}}</code>, <code>{{EMAIL}}</code>, <code>{{FIRST_NAME}}</code>, <code>{{LAST_NAME}}</code>, <code>{{USERNAME}}</code>, <code>{{PASSWORD}}</code>, <code>{{LOGIN_URL}}</code>, <code>{{VERIFY_ACCOUNT_URL}}</code> (only for PRO)',
                'render'      => array( new LRM_Field_Editor(), 'input' ),
                'sanitize'    => array( new LRM_Field_Editor(), 'sanitize' ),
            ) );

        $EMAILS_SECTION->add_group( __( 'Lost password', 'ajax-login-and-registration-modal-popup' ), 'lost_password', true )
            ->add_field( array(
                'name'        => __('Subject', 'ajax-login-and-registration-modal-popup' ),
                'default'     => __( '{{SITE_NAME}} Your new password', 'ajax-login-and-registration-modal-popup' ),
                'slug'        => 'subject',
                'description' => __( 'The email Subject to user with new password.', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'name'        => __('Body', 'ajax-login-and-registration-modal-popup' ),
                'default'        =>
                    'Someone has requested a password reset for the following username: {{USERNAME}}' . "\r\n\r\n" .
                    __( 'If this was a mistake, just ignore this email and nothing will happen.', 'ajax-login-and-registration-modal-popup' ) . "\r\n\r\n" .
                    __('To reset your password, visit the following address:', 'ajax-login-and-registration-modal-popup') . ' {{CHANGE_PASSWORD_URL}}' . "\r\n\r\n",
                'slug'        => 'body',
                'addons'      => array(
                    'pretty'   => true,
                ),
                'description' => __( 'The email Body to user with new password. Allowed tags: <code>{{USERNAME}}</code>, <code>{{CHANGE_PASSWORD_URL}}</code>, <code>{{SITE_URL}}</code>, <code>{{HOME_URL}}</code>, <code>{{LOGIN_URL}}</code>, <code>{{EMAIL}}</code>, <code>{{FIRST_NAME}}</code>, <code>{{LAST_NAME}}</code>', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Editor(), 'input' ),
                'sanitize'    => array( new LRM_Field_Editor(), 'sanitize' ),
            ) );

        $EMAILS_SECTION->add_group( __( 'Admin emails', 'ajax-login-and-registration-modal-popup' ), 'admin_new_user', true )
            ->add_field( array(
                'slug'        => 'on',
                'name'        => __('Send email to admin about new user?', 'ajax-login-and-registration-modal-popup' ),
                'default'     => false,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'subject',
                'name'        => __('Subject', 'ajax-login-and-registration-modal-popup'),
                'default'     => '{{SITE_NAME}} ' . __( 'New user registration on your site:', 'ajax-login-and-registration-modal-popup' ),
                'description' => __( 'The email Subject to user about successful registration.', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'body',
                'name'        => __('Body', 'ajax-login-and-registration-modal-popup' ),
                'default'     =>
                    __('New user just registered on', 'ajax-login-and-registration-modal-popup' ) .' {{SITE_NAME}}.' . "\r\n\r\n" .
                    __('Username:', 'ajax-login-and-registration-modal-popup' ) . ' {{USERNAME}}' . "\r\n\r\n" .
                    __('Email:', 'ajax-login-and-registration-modal-popup' ) . ' {{EMAIL}}' . "\r\n\r\n" .
                    __('View:', 'ajax-login-and-registration-modal-popup' ) . ' {{USER_ADMIN_URL}}',
                'addons'      => array(
                    'pretty'   => true,
                ),
                'description' => 'The email Body to admin about new user. Allowed tags: <code>{{USERNAME}}</code>, <code>{{EMAIL}}</code>, <code>{{USER_ADMIN_URL}}</code>. <br/><b>Please note</b> - in case of using Social login this email will be not triggered, so please check Social Login plugin settings for that.',
                'render'      => array( new LRM_Field_Editor(), 'input' ),
                'sanitize'    => array( new LRM_Field_Editor(), 'sanitize' ),
            ) );

        $EMAILS_SECTION->add_group( __( 'Template for HTML emails (text/html format only, not WooCommerce!)', 'ajax-login-and-registration-modal-popup' ), 'template', true )
            ->add_field( array(
                'slug'        => 'code',
                'name'        => __('HTML email template', 'ajax-login-and-registration-modal-popup' ),
                'default'     => '{{CONTENT}}',
                'description' => __('Put here your custom mail html template + css + tag {{CONTENT}} (required).', 'ajax-login-and-registration-modal-popup')
                    . sprintf('<a href="%s" target="_blank">Tutorial >></a>', 'https://docs.maxim-kaminsky.com/lrm/kb/how-to-style-email-templates/'),
                'render'      => array( new LRM_Field_Textarea_With_Html_Extended(), 'input' ),
                'sanitize'    => array( new LRM_Field_Textarea_With_Html_Extended(), 'sanitize' ),
                'addons'      => array(
                    'rows'   => 4,
                ),
            ) );

        $MESSAGES_SECTION = $this->settings->add_section( __( 'Expressions', 'ajax-login-and-registration-modal-popup' ), 'messages' );

        $MESSAGES_SECTION->add_group( __( 'Login', 'ajax-login-and-registration-modal-popup' ), 'login', true )
            ->add_field( array(
                'slug'        => 'heading',
                'name'        => __('Form heading', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Sign in',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'username',
                'name'        => __('Form: Email or Username', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Email or Username',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password',
                'name'        => __('Form: Password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Password',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'remember-me',
                'name'        => __('Form: Remember me', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Remember me',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'button',
                'name'        => __('Form button: Login', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Log in',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'forgot-password',
                'name'        => __('Link: Forgot your password?', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Forgot your password?',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            // == MESSAGES ==
            ->add_field( array(
                'slug'        => 'no_login',
                'name'        => __('Message: No Username/Email', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Please enter your Username/email!',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'invalid_login',
                'name'        => __('Message: Invalid username (not exists)', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Invalid username!',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'no_pass',
                'name'        => __('Message: No Password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Please enter your password!',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'success',
                'name'        => __('Message: Login successful (with "Reload page after login/registration?" enabled)', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Login successful, reloading page...',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'success_no_reload',
                'name'        => __('Message: Login successful (with "Reload page after login/registration?" disabled)', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Login successful, you can close this window.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->description( 'Please help <a class="button button-primary" target="_blank" href="https://translate.wordpress.org/projects/wp-plugins/ajax-login-and-registration-modal-popup">translate plugin</a> to your language!' );


        $MESSAGES_SECTION->add_group( __( 'Registration', 'ajax-login-and-registration-modal-popup' ), 'registration', true )
            ->add_field( array(
                'slug'        => 'heading',
                'name'        => __('Form heading', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'New account',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'username',
                'name'        => __('Form: Username', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Username*',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'first-name',
                'name'        => __('Form: First name', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'First name*',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'last-name',
                'name'        => __('Form: Last name', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Last name',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'email',
                'name'        => __('Form: Email', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Email*',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'user_role',
                'name'        => __('Form: User Role', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Select a Role',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'no_user_role',
                'name'        => __('Form: User Role is missing', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Please select a Role',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )

//            ->add_field( array(
//                'slug'        => 'password_repeat',
//                'name'        => __('Form: Repeat Password', 'ajax-login-and-registration-modal-popup' ),
//                'default'        => 'Repeat Password',
//                'render'      => array( new LRM_Field_Text(), 'input' ),
//                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
//            ) )
            ->add_field( array(
                'slug'        => 'terms',
                'name'        => __('Form: Terms', 'ajax-login-and-registration-modal-popup' ),
                'default'     => 'I agree with the <a href=\'/terms\'>Terms</a>. <i>Edit this in Settings => Ajax Login Modal => Expressions tab => Registration section</i>',
                'description' => 'sanitized by wp_kses_post',
                'render'      => array( new LRM_Field_Textarea_With_Html(), 'input' ),
                'sanitize'    => array( new LRM_Field_Textarea_With_Html(), 'sanitize' ),
            ) )
	        ->add_field( array(
		        'slug'        => 'must_agree_with_terms',
		        'name'        => __('Message: Must agree with the terms', 'ajax-login-and-registration-modal-popup' ),
		        'default'        => 'Please agree with the terms to continue!',
		        'render'      => array( new LRM_Field_Text(), 'input' ),
		        'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
	        ) )
            ->add_field( array(
                'slug'        => 'button',
                'name'        => __('Form button: Create account', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Create account',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            // == MESSAGES ==
            ->add_field( array(
                'slug'        => 'disabled',
                'name'        => __('Message: Registration is disabled', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Registration is disabled!',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'no_username',
                'name'        => __('Message: No UserName', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Please enter your UserName!',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'no_name',
                'name'        => __('Message: No First Name', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Please enter your First Name!',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'wrong_email',
                'name'        => __('Message: Wrong email', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Please enter a correct email!',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'success',
                'name'        => __('Message: Registration successful', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Registration was successful, reloading page.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'success_please_login',
                'name'        => __('Message: Registration successful', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Registration was successful. We have sent you an email with your login information. Please use them to log into your account.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) );

        $MESSAGES_SECTION->add_group( __( 'Lost password', 'ajax-login-and-registration-modal-popup' ), 'lost_password', true )
            ->add_field( array(
                'slug'        => 'message',
                'name'        => __('Message', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Lost your password? Please enter your email address. You will receive mail with link to set new password.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'email',
                'name'        => __('Form: Email or Username', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Email or Username',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'button',
                'name'        => __('Form button: Reset password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Reset password',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'to_login',
                'name'        => __('Form button: Back to login', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Back to login',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            // Errors!
            ->add_field( array(
                'slug'        => 'invalid_email',
                'name'        => __('Message: Missing login', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Enter an username or email address.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'email_not_exists',
                'name'        => __('Message: No user registered with that email address', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'There is no user registered with that email address.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'user_not_exists',
                'name'        => __('Message: No user registered with that username', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'There is no user registered with that username.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'invalid_email_or_username',
                'name'        => __('Message: Invalid username or e-mail address', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Invalid username or e-mail address.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'unable_send',
                'name'        => __('Message: Unable to send email', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'System is unable to send you the mail containing your new password.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'something_wrong',
                'name'        => __('Message: Something went wrong', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Oops! Something went wrong while updating your account.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'success',
                'name'        => __('Message: Reset successful', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Check your mailbox to access your new password.',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) );

        $MESSAGES_SECTION->add_group( __( 'Password (registration/reset password)', 'ajax-login-and-registration-modal-popup' ), 'password', true )
            ->add_field( array(
                'slug'        => 'password',
                'name'        => __('Password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Password', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'use_weak_password',
                'name'        => __('Confirm use of weak password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Confirm use of weak password', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_missing',
                'name'        => __('Message: Password is missing', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Password is missing!', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_good',
                'name'        => __('Message: Good Password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Good Password', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_bad',
                'name'        => __('Message: Bad Password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Bad Password', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'passwords_is_mismatch',
                'name'        => __('Message: Mismatch', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Passwords is mismatch!', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_strong',
                'name'        => __('Message: Strong Password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Strong Password', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'password_is_short',
                'name'        => __('Message: Too Short Password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => __('Too Short Password', 'ajax-login-and-registration-modal-popup'),
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) );

        $MESSAGES_SECTION->add_group( __( 'Other', 'ajax-login-and-registration-modal-popup' ), 'other', true )
            ->add_field( array(
                'slug'        => 'close_modal',
                'name'        => __('Close modal text', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'close',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )

            ->add_field( array(
                'slug'        => 'show_pass',
                'name'        => __('Show password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Show',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )

            ->add_field( array(
                'slug'        => 'hide_pass',
                'name'        => __('Hide password', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Hide',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) )

            ->add_field( array(
                'slug'        => 'invalid_nonce',
                'name'        => __('Message: Security token is expired', 'ajax-login-and-registration-modal-popup' ),
                'default'        => 'Security token is expired! Please refresh the page!',
                'render'      => array( new LRM_Field_Text(), 'input' ),
                'sanitize'    => array( new LRM_Field_Text(), 'sanitize' ),
            ) );

        $pro_label = lrm_is_pro() ? ' > PRO' : ' [PRO Only]' ;
        $pro_note = lrm_is_pro() ? '' : 'This settings will work only in a PRO version!' ;

        $SECURITY_SECTION = $this->settings->add_section( __( 'Security (captcha)' . $pro_label, 'ajax-login-and-registration-modal-popup' ), 'security' );

        $SECURITY_SECTION->add_group( __( 'General', 'ajax-login-and-registration-modal-popup' ), 'general' )
            ->add_field( array(
                'slug'        => 'type',
                'name'        => __('How to secure forms?', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        ''               => '= Please select =',
                        'reCaptcha'      => 'reCaptcha (api keys are required) [PRO]',
                        'MatchCaptcha'    => 'MatchCaptcha (less secure but faster) [PRO]',
                    ),
                ),
                'default'     => '',
                'description' => __('Ony if you do not have external Captcha plugins.', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Select_W_PRO(), 'input' ),
                'sanitize'    => array( new LRM_Field_Select_W_PRO(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'secure_login',
                'name'        => __('Secure login form?', 'ajax-login-and-registration-modal-popup' ),
                'default'     => 'true',
                'addons'      => array('label' => __( 'Yes' )),
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'secure_register',
                'name'        => __('Secure register form?', 'ajax-login-and-registration-modal-popup' ),
                'default'     => 'true',
                'addons'      => array('label' => __( 'Yes' )),

                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'secure_lostpass',
                'name'        => __('Secure lost password form?', 'ajax-login-and-registration-modal-popup' ),
                'default'     => 'true',
                'addons'      => array('label' => __( 'Yes' )),

                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            ) )
            ->description( 'You have to restrict access to the wp-login.php area, else it will be not secure.' . $pro_note );


        $SECURITY_SECTION->add_group( __( 'reCaptcha Api Keys', 'ajax-login-and-registration-modal-popup' ), 'recaptcha' )
            ->add_field( array(
                'slug'        => 'type',
                'name'        => __('reCaptcha type:', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        'inline'        => 'Inline',
                        'invisible'     => 'Invisible',
                    ),
                ),
                'default'     => '',
                'description' => __('Please make sure that keys is created for the selected reCaptcha type!', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'site_key',
                'name'        => __('Site key', 'ajax-login-and-registration-modal-popup' ),
                'default'     => '',
                'render'      => array( new LRM_Field_Password(), 'input' ),
                'sanitize'    => array( new LRM_Field_Password(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'secret_key',
                'name'        => __('Secret key', 'ajax-login-and-registration-modal-popup' ),
                'default'     => '',
                'render'      => array( new LRM_Field_Password(), 'input' ),
                'sanitize'    => array( new LRM_Field_Password(), 'sanitize' ),
            ) )
            ->description( __('Find them here: https://www.google.com/recaptcha/admin', 'ajax-login-and-registration-modal-popup' ) );

	    $SECURITY_SECTION->add_group( __( 'Match Captcha', 'ajax-login-and-registration-modal-popup' ), 'match_captcha' )
		    ->description('Label/error messages can be changed on the Expressions > PRO tab');


        if ( !lrm_is_pro() ) {

            $MESSAGES_SECTION = $this->settings->add_section( 'GET PRO >>',  'get_a_pro', false );

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

        //$this->register_wpml_strings();
        LRM_WPML_Integration::register_strings();

	    LRM_Roles_Manager::register_settings( $this->settings );
        LRM_Import_Export_Manager::register_settings( $this->settings );
    }


    /**
     * @param underDEV\Utils\Settings\Field     $field
     *
     * @since 1.11
     */
    public function _render__text_section( $field ) {
        if ( $section_file = $field->addon('section_file') ) {
            include LRM_PATH . "/views/admin/settings-section/{$section_file}.php";
        }
    }

    public function settings_enqueue_scripts() {
        wp_enqueue_script( 'lrm-admin', LRM_URL . 'assets/lrm-admin.js', array( 'jquery', 'jquery-ui-sortable' ), LRM_VERSION, true );
	    wp_localize_script('lrm-admin', 'LRM_ADMIN', array(
	    	'ajax_url' => admin_url('admin-ajax.php'),
	    ));

        wp_enqueue_style('lrm-admin-css', LRM_URL . '/assets/lrm-core-settings.css', false, LRM_ASSETS_VER);
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
     * @param  string $setting_slug setting section/group/field separated with /
     * @param  bool do_stripslashes
     * @return mixed           field value or null if name not found
     */
    public function setting($setting_slug, $do_stripslashes = false) {

        $setting_path = explode('/', $setting_slug);

//        $value = $this->_get_maybe_wpml_translated_string($setting_slug, $setting_path[0]);
//
//        if ( null !== $value ) {
//            return stripslashes($value);
//        }

        $value = $this->settings->get_setting( $setting_slug );
        // IF Value is empty and it's message string - try to get translated


        if ( $setting_path[0] == 'messages' || $setting_path[0] == 'mails' ) {

            $value = stripslashes($value);
        }

//
//        if ( is_admin() && !$value && $setting_path[0] == 'messages' ){
//
//            // SKIP if we on Default language
//            global $sitepress;
//
//            $current_language           = $sitepress->get_current_language();
//            //var_dump( $current_language );
//            $current_language_code = $sitepress->get_locale_from_language_code( $current_language );
//
//            switch_to_locale( $current_language_code );
//
//            var_dump($current_language_code);
//
//            $fields = $this->get_section_settings_fields('messages');
//
//            $default_value = $fields[$setting_slug]->default_value();
//            if ($default_value) {
//                $__value = __($default_value, 'ajax-login-and-registration-modal-popup');
//                if ($default_value !== $__value) {
//                    $value = $__value;
//                }
//            }
//
//        }


        if (!$value && $setting_path[0] == 'messages' && defined("LRM/SETTINGS/TRY_GET_TRANSLATED")) {
            $fields = $this->get_section_settings_fields('messages');

            $default_value = $fields[$setting_slug]->default_value();
            if ($default_value) {
                $__value = __($default_value, 'ajax-login-and-registration-modal-popup');
                if ($default_value !== $__value) {
                    $value = $__value;
                }
            }

        }

        //restore_previous_locale();

        return $do_stripslashes ? stripslashes( $value ) : $value;

    }

    /**
     * Update single setting value
     * @uses   SettingsAPI Settings API class
     *
     * @param  string $setting_slug setting section/group/field separated with /
     * @param  mixed $new_value
     *
     * @return bool
     * @throws Exception
     * @since 1.51
     */
    public function update_setting($setting_slug, $new_value)
    {

        $setting_path = explode('/', $setting_slug);

        if ( count($setting_path) !== 3 ) {
            throw new Exception('Invalid $setting_slug: ' . $setting_slug);
        }

        $res = update_option( 'lrm_' . $setting_path[0] . '[' . $setting_path[1] . ']', $new_value );

//        var_dump( 'lrm_' . $setting_path[0] );
//        var_dump( $new_value );
//        var_dump( get_option('lrm_' . $setting_path[0]  ) );

        return  $res;
    }

    /**
     * Get translated option value (string)
     * If enabled WPML - then try return translated
     *
     * @param string $setting_slug
     * @param $section_slug
     *
     * @return string
     * @since 1.33
     */
    protected function _get_maybe_wpml_translated_string($setting_slug, $section_slug) {

        // && isset($this->wpml_labels[$key])
        if ( class_exists('SitePress') ) {

            // SKIP if we on Default language
            global $sitepress;

            $current_language = $sitepress->get_current_language();
            $default_language = $sitepress->get_default_language();

            /**
             * Switch Language for AJAX
             * @since 1.33
             */
            if ( defined("LRM_IS_AJAX") ) {
                /**
                 * @var WPML_Language_Resolution $wpml_language_resolution
                 */

                global $wpml_language_resolution;

                if ($current_language != $wpml_language_resolution->get_referrer_language_code()) {
                    $sitepress->switch_lang($wpml_language_resolution->get_referrer_language_code());
                    $current_language = $sitepress->get_current_language();
                }
            }

            if ( $default_language == $current_language ) {
                return null;
            }

            $fields = $this->get_section_settings_fields($section_slug);
            /**
             * @see https://wpml.org/wpml-hook/wpml_translate_single_string/
             * @since 1.29
             */
            return apply_filters( 'wpml_translate_single_string', $fields[$setting_slug]->default_value(), 'AJAX Login & Registration modal', $fields[$setting_slug]->name(). ' [' . $fields[$setting_slug]->group() . '/' .$fields[$setting_slug]->slug() . ']' );
            //return wpml_register_single_string('AJAX Login & Registration modal', $fields[$setting_slug]->name(). ' [' . $fields[$setting_slug]->group() . '/' .$fields[$setting_slug]->slug() . ']', $fields[$setting_slug]->default_value());
        }
        return null;
    }

    /**
     * Add strings to WPML strings translator
     *
     * @since 1.33
     */
    protected function register_wpml_strings() {

        do_action( 'wpml_multilingual_options', 'lrm_messages' );
        do_action( 'wpml_multilingual_options', 'lrm_mails' );
        do_action( 'wpml_multilingual_options', 'lrm_messages_pro' );


        // && function_exists('icl_register_string')
        if ( class_exists('SitePress')  ) {

            //switch_to_locale( 'en_US' );


//            $messages = $this->get_section_settings_fields('messages');
//            $mails = $this->get_section_settings_fields('mails');
//
//            $all = $messages + $mails;
//
//            if ( lrm_is_pro() ) {
//                $messages_pro = $this->get_section_settings_fields('messages_pro');
//                $all = $all + $messages_pro;
//            }
//
//            foreach ($all as $key => $field) {
//                /**
//                 * @see https://wpml.org/wpml-hook/wpml_register_single_string
//                 * @since 1.29
//                 */
//                    do_action( 'wpml_register_single_string', 'AJAX Login & Registration modal', $field->name(). ' [' . $field->group() . '/' .$field->slug() . ']', $field->default_value() );
//                // icl_register_string is deprecated
//                //icl_register_string( 'AJAX Login & Registration modal', $field->name(). ' [' . $field->group() . '/' .$field->slug() . ']', $field->default_value() );
//            }

            //restore_previous_locale();
        }
    }


    /**
     * Get all fields from section
     *
     * @param string $section_slug
     *
     * @since 1.24
     *
     * @return \underDEV\Utils\Settings\Field[]
     */
    public function get_section_settings_fields( $section_slug ) {

        $fields = array();

        $section = $this->settings->get_section( $section_slug );

        foreach ( $section->get_groups() as $group_slug => $group ) {

            foreach ( $group->get_fields() as $field_slug => $field ) {
                $fields[ $section_slug . '/' . $group_slug . '/' . $field_slug ] = $field;
            }
        }

        return $fields;
    }

    /**
     * Get all fields from section
     *
     * @param string $section_slug
     *
     * @since 1.24
     *
     * @return \underDEV\Utils\Settings\Field[]
     */
    public function get_sections(  ) {
        return $this->settings->get_sections(  );
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