<?php

/**
 * Class LRM_Weglot_Integration
 * @since 2.20
 */
class LRM_Weglot_Integration {

	/**
	 * @return bool
	 */
    public static function is_active() {
        return function_exists('weglot_get_current_language');
    }

	/**
	 * @param string $redirect_to
	 *
	 * @return string
	 */
    public static function get_redirect_url( $redirect_to ) {

	    if (  weglot_get_original_language() === weglot_get_current_language() ) {
		    $redirect_to = str_replace( '%%LANG%%/', '', $redirect_to );
	    } else {
		    $redirect_to = str_replace( '%%LANG%%', weglot_get_current_language(), $redirect_to );
	    }

	    return $redirect_to;
    }

	/**
	 * @return string|bool
	 */
    public static function get_ajax_url( $ajax_url ) {

	    if (  weglot_get_original_language() === weglot_get_current_language() ) {
	    	return $ajax_url;
	    }
    	if ( false === strpos($ajax_url, 'admin-ajax.php') ) {
		    $ajax_url = str_replace( '/?', '/' . weglot_get_current_language() . '/?', $ajax_url );
	    } else {
		    $ajax_url = str_replace( '/wp-admin', '/' . weglot_get_current_language() . '/wp-admin', $ajax_url );
	    }
	    return $ajax_url;
    }

}