<?php
/**
	Plugin Name:    AJAX Login and Registration modal popup DEV + inline form
	Plugin URI:     https://maxim-kaminsky.com/shop/product/ajax-login-and-registration-modal-popup-pro/
	Description:    Easy to integrate modal with Login and Registration features + inline form using shortcode.
	Version:        2.10
	Author URI:     http://maxim-kaminsky.com/
	Author:         Maxim K
	Text Domain:    ajax-login-and-registration-modal-popup
	Domain Path:    /languages
*/

// If this file is called directly, abort.
if (!class_exists('WP')) {
	die();
}

if ( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) {
	die( 'Access denied.' );
}


// Stop IF Pro version exists > 1.50 with the in-build Free version
if ( class_exists('LRM_Pro') && defined("LRM_URL") && lrm_is_pro('1.50') && ! defined("LRM_ALWAYS_LOAD_FREE") ) {
    return;
}

if ( !defined("LRM_IN_BUILD_FREE") ) {
    define("LRM_URL", plugin_dir_url(__FILE__));
    define("LRM_ASSETS_URL", LRM_URL . '/assets/');

    define("LRM_PATH", plugin_dir_path(__FILE__));
    define("LRM_BASENAME", plugin_basename(__FILE__));
}

define("LRM_VERSION", '2.10');

define("LRM_ASSETS_VER", 32);

//define("LRM/SETTINGS/TRY_GET_TRANSLATED", true);

//define("LRM/SETTINGS/TRY_GET_TRANSLATED", 1);

require_once( LRM_PATH . 'includes/helpers.php' );
require_once( LRM_PATH . 'vendor/autoload.php' );

//require_once LRM_PATH . '/includes/class-core.php';

add_action('plugins_loaded', array('LRM_Core', 'get'), 11);


if (!SHORTINIT && !defined("LRM_IN_BUILD_FREE")) {
    /**
     * The code that runs during plugin deactivation.
     */
    register_deactivation_hook( __FILE__, array( 'LRM_Deactivator', 'deactivate' ) );
}