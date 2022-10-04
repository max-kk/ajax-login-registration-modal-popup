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

//    	var_dump( pll_current_language('locale') );
//	    switch_to_locale( pll_current_language('locale') );
//	    LRM_Core::get()->load_plugin_textdomain();

        if ( ! self::is_wpml_active() ){
            return;
        }

        //LRM_WPML_Integration::pre_AJAX();

//        echo "wpml pre switch_locale";

        // SKIP if we on Default language
        global $sitepress;
        if ( !$sitepress ) {
            return;
        }

        $locale = self::get_locale();
        $current_language           = $sitepress->get_current_language();
        //var_dump( $current_language );
        $current_language_code = $sitepress->get_locale_from_language_code( $current_language );

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

        // Fix in case EN is not default language
        // WPML when return translated option fill with a default LANG values for a missing strings
        // So if the default is RU and current is EN the non translated strings will be on RU, not EN from MO files
        global $sitepress;
        if ( !$sitepress ) {
            return;
        }
        $current_lang = $sitepress->get_current_language();

        if ( 'en' !== $sitepress->get_default_language() && $sitepress->get_default_language() !== $current_lang ) {
//            var_dump($sitepress->get_default_language());
//            var_dump($current_lang);
            add_filter( "option_lrm_messages_{$current_lang}", 'LRM_WPML_Integration::pre_option_filter', 99, 2 );
            add_filter( "option_lrm_messages_pro_{$current_lang}", 'LRM_WPML_Integration::pre_option_filter', 99, 2 );
            add_filter( "option_lrm_mails_{$current_lang}", 'LRM_WPML_Integration::pre_option_filter', 99, 2 );
        }
    }

    public static function pre_option_filter( $value, $option_name ) {
        if ( !$value ) {
            return $value;
        }

        $section_name = 'messages';
        $option_name = str_replace('lrm_', $option_name);
        if ( false !== strpos($option_name, 'mails') ) {
            $section_name = 'mails';
        } elseif ( false !== strpos($option_name, 'mails') ) {
            $section_name = 'messages_pro';
        }

        $sections = LRM_Settings::get()->get_sections();
        $section = $sections[$section_name]; // TODO: DYNAMIC

        foreach ( $section->get_groups() as $group_slug => $group ) {
            foreach ( $group->get_fields() as $field_slug => $field ) {
                if ( !isset($value[$group_slug][$field_slug]) ) {
                    $value[$group_slug][$field_slug] = $field->default_value();
                }
            }
        }

        return $value;
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