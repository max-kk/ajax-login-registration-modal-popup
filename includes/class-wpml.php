<?php

/**
 * Class LRM_WPML_Integration
 * @since 1.50
 */
class LRM_WPML_Integration {

    /**
     * @var string
     */
    static $locale = null;

    public static function is_wpml_active() {
        return class_exists('SitePress');
    }

    public static function get_locale() {
        if ( ! self::$locale ) {
            self::$locale = get_locale();
        }
        return self::$locale;
    }

    public static function is_default_locale() {
        return ! in_array(self::get_locale(), ['en_US', 'en_GB']);
    }

    /**
     * Force Load the mo file for current WPML switcher language
     */
    public static function switch_locale() {

        if ( ! self::is_wpml_active() ){
            return;
        }

        //LRM_WPML_Integration::pre_AJAX();

//        echo "wpml pre switch_locale";

        // SKIP if we on Default language
        global $sitepress;

        $locale = self::get_locale();
        $current_language           = $sitepress->get_current_language();
        //var_dump( $current_language );
        $current_language_code = $sitepress->get_locale_from_language_code( $current_language );

//        var_dump($locale);
//        var_dump($current_language_code);
        if ( $locale == $current_language_code || self::is_default_locale() ) {
            return;
        }

//        echo "wpml switch_locale";

        //if(isset($l10n[$current_language_code])) $backup = $l10n[$current_language_code];

        add_filter('override_load_textdomain', ['LRM_WPML_Integration', 'override_load_textdomain__filter2'], PHP_INT_MAX, 2);

        load_textdomain( 'ajax-login-and-registration-modal-popup', LRM_PATH . '/languages/ajax-login-and-registration-modal-popup-' . $current_language_code . '.mo' );

        remove_filter('override_load_textdomain', ['LRM_WPML_Integration', 'override_load_textdomain__filter2'], PHP_INT_MAX, 2);

        //global $l10n;
        //print_r($l10n);
//
//                    add_filter( 'plugin_locale', function( $determined_locale, $domain ) use($current_language_code) {
//                        return $current_language_code;
//                    }, 10, 2 );
//
//                    load_plugin_textdomain( 'ajax-login-and-registration-modal-popup', false, dirname( LRM_BASENAME ) . '/languages/' );


    }

    public static function override_load_textdomain__filter2($false, $domain) {
        if ( $domain !== 'ajax-login-and-registration-modal-popup' ) {
            return $false;
        }
        return false;
    }

    public static function restore_locale() {

    }


    /**
     * Allow have different settings per language in DB
     */
    public static function register_strings()
    {
        do_action('wpml_multilingual_options', 'lrm_messages');
        do_action('wpml_multilingual_options', 'lrm_mails');
        do_action('wpml_multilingual_options', 'lrm_messages_pro');
        do_action('wpml_multilingual_options', 'lrm_redirects');
    }

    /**
     * Switch Lang for AJAX requests
     */
    public static function pre_AJAX() {
        if ( ! self::is_wpml_active() && empty($_REQUEST['lrm_action']) ){
            return;
        }

        // SKIP if we on Default language
        global $sitepress;

        $current_language = $sitepress->get_current_language();
        //$default_language = $sitepress->get_default_language();

//        ini_set('display_errors',1);
//        ini_set('display_startup_errors',1);
//        error_reporting(-1);

        /**
         * Switch Language for AJAX
         * @since 1.33
         */
        //if ( defined("LRM_IS_AJAX") ) {
            /**
             * @var WPML_Language_Resolution $wpml_language_resolution
             */

            global $wpml_language_resolution;

            if ($current_language != $wpml_language_resolution->get_referrer_language_code()) {
                $sitepress->switch_lang($wpml_language_resolution->get_referrer_language_code());
                $current_language = $sitepress->get_current_language();

                $locale = $sitepress->get_locale_from_language_code( $current_language );

                //switch_to_locale( $locale );
                //load_default_textdomain( $locale );
            }
        //}
    }

}