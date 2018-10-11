<?php

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

        require_once LRM_PATH . '/includes/class-mailer.php';
        require_once LRM_PATH . '/includes/class-settings.php';
        require_once LRM_PATH . '/includes/class-ajax.php';
        require_once LRM_PATH . '/includes/class-admin-menus.php';

        LRM_Settings::get();

        add_shortcode('lrm_form', array($this, 'shortcode'));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'), 5);
        add_action('wp_footer', array($this, 'wp_footer__action'), 1);

        if ( !class_exists('LRM_Pro') ) {
            add_action('lrm_login_form', array($this, 'form_fblogin__action'));
            add_action('lrm_register_form', array($this, 'form_fblogin__action'));
            add_action('lrm_lostpassword_form', array($this, 'form_fblogin__action'));
        }

        if ( !empty($_REQUEST['lrm_action']) ) {
//            $lrm_advanced_option = get_option('lrm_advanced');
//
//            if ($lrm_advanced_option && isset($lrm_advanced_option['troubleshooting']['hook'])) {
//                $hook_to_use = $lrm_advanced_option['troubleshooting']['hook'];
//            } else {
//                $hook_to_use = ;
//            }

            add_filter( 'wp_redirect', array($this, 'wp_redirect__filter'), 9, 2 );

            // Disable redirect after Login
            add_filter( 'ws_plugin__s2member_login_redirect', '__return_false', 99 );
            // Try to remove all actions from "wp_login" action to avoid redirects
            remove_all_actions('wp_login');

            add_action( 'wp_loaded', array($this, 'process_ajax'), 12 );
        }

        // RUN PRO UPDATER
        if ( is_admin() && lrm_is_pro() ) {

            require 'plugin-update-checker/plugin-update-checker.php';
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

        add_filter('plugin_action_links_' . LRM_BASENAME, array($this, 'add_settings_link'));

        new LRM_Admin_Menus();
    }

    public function shortcode($atts) {
        if ( !is_customize_preview() && is_user_logged_in() ) {
            return;
        }

        $atts = wp_parse_args($atts, array(
            'default_tab'  => 'login',
        ));

        ob_start();
            $this->render_form( true, $atts['default_tab'] );
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

        //var_dump( function_exists('cptch_login_check') );
        //add_filter('authenticate', 'cptch_login_check', 21, 1);
        if ( !empty($_REQUEST['lrm_action']) ) {
            $lrm_action = sanitize_key($_REQUEST['lrm_action']);

            define("LRM_IS_AJAX", true);

            do_action( 'wp_ajax_nopriv_lrm_' . $lrm_action );
            die();
        }
    }

    /**
     * Try to change Hook position to avoid redirect during login/registration
     * Calls only once
     *
     * @param $location
     * @param $status
     * @since 1.36
     *
     * @return mixed
     */
    public function wp_redirect__filter($location, $status) {
        wp_send_json_error(array(
            'message' => sprintf(
                __( 'Some plugin try to redirect during this action to the following url: %s. Please try to disable plugins related to the Security/User Profile/Membership and try again.', 'ajax-login-and-registration-modal-popup' ),
                $location
            )
        ));
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
    public function call_pro($function, $param1 = false) {
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
            require LRM_PATH . '/views/footer_styles.php';
        }

        if ( !$is_customize_preview && is_user_logged_in() ) {
            return;
        }

        $this->render_form();
    }

    public function enqueue_assets() {

        if ( !is_customize_preview() && is_user_logged_in() ) {
            return;
        }

        wp_enqueue_script('lrm-modal', LRM_URL . 'assets/lrm-core.js', array('jquery'), LRM_ASSETS_VER, true);

        wp_enqueue_style('lrm-modal', LRM_URL . 'assets/lrm-core.css', false, LRM_ASSETS_VER);

        $script_params = array(
            'redirect_url'       => '',
            'ajax_url'           => add_query_arg( 'lrm', '1', site_url('/') ),
            //'ajax_url'           => add_query_arg( 'lrm', '1', admin_url('admin-ajax.php') ),
            'is_user_logged_in'  => is_user_logged_in(),
            'reload_after_login' => LRM_Settings::get()->setting('general/registration/reload_after_login'),
            'selectors_mapping' => array(
                'login'     => LRM_Settings::get()->setting('advanced/selectors_mapping/login'),
                'register'  => LRM_Settings::get()->setting('advanced/selectors_mapping/register'),
            ),
            'is_customize_preview' => is_customize_preview(),
        );

        wp_localize_script('lrm-modal', 'LRM', $script_params);
    }

    public function render_form( $is_inline = false, $default_tab = 'login' ) {

        if ( !in_array($default_tab, array('login', 'register', 'lost-password')) ) {
            $default_tab = 'login';
        }

        require LRM_PATH . '/views/form.php';

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