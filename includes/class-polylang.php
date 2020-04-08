<?php

/**
 * Class LRM_Polylang_Integration
 * @since 2.11
 */
class LRM_Polylang_Integration {

	/**
	 * @return bool
	 */
    public static function is_active() {
        return function_exists('pll_current_language');
    }

	/**
	 * @return string|bool
	 */
    public static function get_locale() {
        return pll_current_language('locale');
    }

	/**
	 * @return string
	 */
    public static function get_locale_suffix() {
	    if ( self::is_active() && self::get_locale() && !self::is_default_locale() ) {
		    return '_' . pll_current_language( 'locale' );
	    }
	    return '';
    }

    public static function is_default_locale() {
        return self::get_locale() === pll_default_language('locale');
    }

	/**
	 * Force Load the mo file for current WPML switcher language
	 */
	public static function switch_locale() {
		if ( !self::is_active() || self::is_default_locale() ) {
			return;
		}
		switch_to_locale( pll_current_language('locale') );
	    LRM_Core::get()->load_plugin_textdomain();
	}

	public static function restore_locale() {
		if ( !self::is_active() || self::is_default_locale() ) {
			return;
		}
		/* @var WP_Locale_Switcher $wp_locale_switcher */
		global $wp_locale_switcher;

		$wp_locale_switcher->restore_previous_locale();
	}
}