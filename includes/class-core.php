<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class LRM_Core
 */
class LRM_Core {
    protected static $instance;

    public function __construct()
    {
        //if ( defined("LRM_LOAD_PLUGIN_TEXTDOMAIN") ) {
//            $this->load_plugin_textdomain();
        //}

        $this->load_plugin_textdomain();

        WP_Admin_Dismissible_Notice::get();
        LRM_Settings::get();

        // Fix for https://wordpress.org/plugins/eonet-manual-user-approve/, to stop reset user password
	    add_filter('eonet_mua_avoid_password_reset', '__return_false');

        add_shortcode('lrm_form', array($this, 'shortcode'));
        add_shortcode('lrm_lostpassword_form', array($this, 'lostpassword_shortcode'));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'), 5);
        add_action('wp_footer', array($this, 'wp_footer__action'), 1);

        add_action('init', array('LRM_Updater', 'init'));
        add_action( 'template_redirect', array($this, 'template_redirect'), 99 );

        if ( !class_exists('LRM_Pro') ) {
            add_action('lrm_login_form', array($this, 'form_fblogin__action'));
            add_action('lrm_register_form', array($this, 'form_fblogin__action'));
            add_action('lrm_lostpassword_form', array($this, 'form_fblogin__action'));
        }

        if ( !empty($_REQUEST['lrm_action']) ) {
            add_action( 'wp_loaded', array($this, 'process_ajax'), 9 );
        }

        // RUN PRO UPDATER
        if ( file_exists(LRM_PATH . 'vendor/plugin-update-checker/plugin-update-checker.php') && is_admin() && lrm_is_pro() && !lrm_is_pro('1.50') ) {

            require LRM_PATH . 'vendor/plugin-update-checker/plugin-update-checker.php';
            $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                'https://addons-updater.wp-vote.net/?action=get_metadata&slug=ajax-login-and-registration-modal-popup-pro',
                LRM_PRO_PATH . 'login-registration-modal-pro.php', //Full path to the main plugin file or functions.php.
                'ajax-login-and-registration-modal-popup-pro'
            );
        }

        //if ( class_exists('LRM_Pro') ) {
        //} else {
        //    $this->process_ajax();
        //}


        if ( !defined("LRM_IN_BUILD_FREE") ) {
            add_filter('plugin_action_links_' . LRM_BASENAME, array($this, 'add_settings_link'));
        }

        new LRM_Admin_Menus();

        WP_Skins_Customizer::init();
        LRM_Skins::instance()->load_defaults();

        LRM_Pages_Manager::init();

	    LRM_Import_Export_Manager::init();
    }

    public function shortcode($atts) {
        $atts = wp_parse_args($atts, array(
            'default_tab'   => 'login',
            'logged_in_message'  => 'You are currently logged in!',
            'role'          => '',
            'role_silent'   => false,
        ));

        $atts['role_silent'] = ($atts['role_silent'] || $atts['role_silent'] === 'yes') ? true : false;

        if ( !is_customize_preview() && is_user_logged_in() ) {
            return $atts['logged_in_message'];
        }

        ob_start();
            $this->render_form( true, $atts['default_tab'], $atts['role'], $atts['role_silent'] );
        return ob_get_clean(  );
    }


    public function lostpassword_shortcode($atts) {
        $atts = wp_parse_args($atts, array(
            'logged_in_message'  => 'You are currently logged in!',
        ));


        if ( !is_customize_preview() && is_user_logged_in() ) {
            return $atts['logged_in_message'];
        }

        ob_start();
            require LRM_PATH . 'views/restore-password.php';
        return ob_get_clean(  );
    }

    /**
     * Add settings link to plugin list table
     *
     * @param  array $links Existing links
     *
     * @return array        Modified links
     */
    public function add_settings_link($links)
    {
        $settings_link = sprintf('<a href="admin.php?page=login-and-register-popup">%s</a>', __('Settings', 'lrm'));
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Define the locale for this plugin for internationalization.
     * Do not loaded by default because used https://translate.wordpress.org/
     * https://translate.wordpress.org/projects/wp-plugins/ajax-login-and-registration-modal-popup
     *
     * @since    1.02
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            //'lrm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/'
            'ajax-login-and-registration-modal-popup', false, dirname( LRM_BASENAME ) . '/languages/'
        );
    }


    /**
     * Redirect the user to the 'redirect_to' param is he's located on the login/registration page
     *
     * @since 2.03
     */
    public function template_redirect() {
        if ( ! is_user_logged_in() || ! isset($_GET['redirect_to']) ) {
            return;
        }

        $pages = LRM_Pages_Manager::_get_pages_arr();

        if ( isset( $pages[get_the_ID()]) ) {
            wp_safe_redirect( $_GET['redirect_to'] );
        }
    }

    /**
     *
     * @since 1.0
     */
    public function process_ajax() {
        /**
         * Fix for ACF PRO
         * @since 1.18
         */

        if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'acf/validate_save_post') {
            return;
        }

        add_action('wp_ajax_nopriv_lrm_login', array('LRM_AJAX', 'login'));
        add_action('wp_ajax_nopriv_lrm_signup', array('LRM_AJAX', 'signup'));
        add_action('wp_ajax_nopriv_lrm_lostpassword', array('LRM_AJAX', 'lostpassword'));
        add_action('wp_ajax_nopriv_lrm_password_reset', array('LRM_AJAX', 'password_reset'));

        //var_dump( function_exists('cptch_login_check') );
        //add_filter('authenticate', 'cptch_login_check', 21, 1);
        if ( !empty($_REQUEST['lrm_action']) ) {
            $lrm_action = sanitize_key($_REQUEST['lrm_action']);

            define("LRM_IS_AJAX", true);

            // Load the SimpleHistory plugin, to log the events
            if ( class_exists('SimpleHistory') ) {
                SimpleHistory::get_instance()->load_loggers();
            }

            do_action( 'wp_ajax_nopriv_lrm_' . $lrm_action );
            die();
        }
    }

    /**
     * Load FB login link from plugin:
     * https://wordpress.org/plugins/wp-facebook-login/
     *
     * @param string $function
     * @since 1.0
     */
    public function form_fblogin__action($function) {
        do_action('facebook_login_button');
    }

    /**
     * Call PRO function
     *
     * @param string    $function
     * @return mixed
     */
    public function call_pro( $function, $param1 = false ) {
        if ( class_exists('LRM_Pro') ) {
            return LRM_Pro::get()->$function($param1);
        }
    }

    public function wp_footer__action() {
        $is_customize_preview = is_customize_preview();
        /**
         * @since 1.01
         */
        if ( !$is_customize_preview ) {
            require LRM_PATH . 'views/footer_styles.php';
        }

        if ( !$is_customize_preview && is_user_logged_in() ) {
            return;
        }

        $this->render_form();
    }

    public function enqueue_assets() {

        if ( ( !is_customize_preview() && is_user_logged_in() ) || is_admin() ) {
            return;
        }

        $required_scripts = array('jquery');

        // For the Password Reset page
//        if ( get_the_ID() == LRM_Pages_Manager::get_page_id('restore-password') ) {
//            $required_scripts[] = 'password-strength-meter';
//        }

        wp_enqueue_script('lrm-modal', LRM_URL . 'assets/lrm-core.js', $required_scripts, LRM_ASSETS_VER, true);

        wp_enqueue_style('lrm-modal', LRM_URL . 'assets/lrm-core-compiled.css', false, LRM_ASSETS_VER);
        wp_enqueue_style('lrm-fonts', LRM_URL . 'assets/fonts.css', false, LRM_ASSETS_VER);

        LRM_Skins::i()->load_current_skin_assets();
        //wp_enqueue_style('lrm-modal-skin', LRM_URL . 'assets/lrm-skin.css', false, LRM_ASSETS_VER);

        $ajax_url = add_query_arg( 'lrm', '1', site_url('/') );
        if ( defined("LRM_AJAX_URL_USE_ADMIN") ) {
	        $ajax_url = add_query_arg( 'lrm', '1', admin_url('admin-ajax.php') );
        }

        if ( LRM_WPML_Integration::is_wpml_active() ) {
            $ajax_url = apply_filters( 'wpml_permalink', $ajax_url );
        }

        $script_params = array(
            'password_zxcvbn_js_src' => includes_url( '/js/zxcvbn.min.js' ),
            'allow_weak_password' => apply_filters( 'lrm/js/allow_weak_password', false ),
            'password_strength_lib' => lrm_setting('general_pro/all/password_strength_lib'),
            'redirect_url'       => '',
            'ajax_url'           => $ajax_url,
            //'ajax_url'           => add_query_arg( 'lrm', '1', admin_url('admin-ajax.php') ),
            'is_user_logged_in'  => is_user_logged_in(),
            'reload_after_login' => LRM_Settings::get()->setting('general/registration/reload_after_login'),
            'selectors_mapping'  => array(
                'login'     => LRM_Settings::get()->setting('advanced/selectors_mapping/login'),
                'register'  => LRM_Settings::get()->setting('advanced/selectors_mapping/register'),
            ),
            'is_customize_preview' => is_customize_preview(),
            'l10n'  => array(
                'password_is_good'  => LRM_Settings::get()->setting('messages/password/password_is_good'),
                'password_is_strong'  => LRM_Settings::get()->setting('messages/password/password_is_strong'),
                'password_is_short'  => LRM_Settings::get()->setting('messages/password/password_is_short'),
                'password_is_bad'  => LRM_Settings::get()->setting('messages/password/password_is_bad'),
                'passwords_is_mismatch'  => LRM_Settings::get()->setting('messages/password/passwords_is_mismatch'),
                'passwords_is_weak'  => LRM_Settings::get()->setting('messages/password/password_is_weak'),
            ),
        );

        wp_localize_script('lrm-modal', 'LRM', $script_params);
    }

    /**
     * @param bool $is_inline
     * @param string $default_tab array('login', 'register', 'lost-password')
     * @param string $role
     * @param bool $role_silent
     */
    public function render_form( $is_inline = false, $default_tab = 'login', $role = '', $role_silent = false ) {

        if ( !in_array($default_tab, array('login', 'register', 'lost-password')) ) {
            $default_tab = 'login';
        }

        require LRM_PATH . 'views/form.php';

    }


    /**
     * @return LRM_Core
     */
    public static function get(){
        if ( ! isset( self::$instance ) ) {
            return self::$instance = new self();
        }

        return self::$instance;
    }
}