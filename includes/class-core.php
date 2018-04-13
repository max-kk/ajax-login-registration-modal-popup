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

        require_once LRM_PATH . '/includes/class-settings.php';
        require_once LRM_PATH . '/includes/class-ajax.php';

        LRM_Settings::get();

        add_action('wp_footer', array($this, 'wp_footer__action'), 8);

        if ( !class_exists('LRM_Pro') ) {
            add_action('lrm_login_form', array($this, 'form_fblogin__action'));
            add_action('lrm_register_form', array($this, 'form_fblogin__action'));
            add_action('lrm_lostpassword_form', array($this, 'form_fblogin__action'));
        }

        add_action('init', array($this, 'process_ajax'), 11);
        //if ( class_exists('LRM_Pro') ) {
        //} else {
        //    $this->process_ajax();
        //}

        add_filter('plugin_action_links_' . LRM_BASENAME, array($this, 'add_settings_link'));
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
            'lrm', false, dirname( LRM_BASENAME ) . '/languages/'
        );
    }


    /**
     *
     * @since 1.0
     */
    public function process_ajax() {

        add_action('wp_ajax_nopriv_lrm_login', array('LRM_AJAX', 'login'));
        add_action('wp_ajax_nopriv_lrm_signup', array('LRM_AJAX', 'signup'));
        add_action('wp_ajax_nopriv_lrm_lostpassword', array('LRM_AJAX', 'lostpassword'));

        //var_dump( function_exists('cptch_login_check') );
        //add_filter('authenticate', 'cptch_login_check', 21, 1);

        if ( !empty($_REQUEST['lrm_action']) ) {
            $lrm_action = sanitize_key($_REQUEST['lrm_action']);

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
    public function call_pro($function) {
        if ( class_exists('LRM_Pro') ) {
            return LRM_Pro::get()->$function();
        }
    }

    public function wp_footer__action() {
        /**
         * @since 1.01
         */
        require LRM_PATH . '/views/footer_styles.php';

        if (is_user_logged_in()) {
            return;
        }

        $this->render_form();
    }

    public function render_form() {
        wp_enqueue_script('lrm-modal', LRM_URL . '/assets/lrm-core.js', array('jquery'), LRM_ASSETS_VER, true);

        wp_enqueue_style('lrm-modal', LRM_URL . '/assets/lrm-core.css', false, LRM_ASSETS_VER);

        require LRM_PATH . '/views/form.php';

        $script_params = array(
            'ajax_url'           => add_query_arg( 'lrm', '1', site_url('/') ),
            'is_user_logged_in'  => is_user_logged_in(),
            'reload_after_login' => LRM_Settings::get()->setting('general/registration/reload_after_login'),
            'selectors_mapping' => array(
                'login'     => LRM_Settings::get()->setting('advanced/selectors_mapping/login'),
                'register'  => LRM_Settings::get()->setting('advanced/selectors_mapping/register'),
            ),
            'l10n'  => array(
                'password_is_good'  => LRM_Settings::get()->setting('messages/registration/password_is_good'),
                'password_is_strong'  => LRM_Settings::get()->setting('messages/registration/password_is_strong'),
                'password_is_short'  => LRM_Settings::get()->setting('messages/registration/password_is_short'),
                'password_is_bad'  => LRM_Settings::get()->setting('messages/registration/password_is_bad'),
            ),
        );

        wp_localize_script('lrm-modal', 'LRM', $script_params);

        wp_enqueue_script( 'password-strength-meter' );
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