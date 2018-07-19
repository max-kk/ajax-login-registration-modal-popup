<?php
/**
	Plugin Name:    AJAX Login and Registration modal popup DEV
	Plugin URI:     https://maxim-kaminsky.com/shop/product/ajax-login-and-registration-modal-popup-pro/
	Description:    Easy to integrate modal with Login and Registration features.
	Version:        1.24
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

define("LRM_URL", plugin_dir_url(__FILE__));
define("LRM_ASSETS_URL", LRM_URL . '/assets/');

define("LRM_PATH", plugin_dir_path(__FILE__));
define("LRM_BASENAME", plugin_basename( __FILE__ ));
define("LRM_VERSION", '1.24');

define("LRM_ASSETS_VER", 13);

require_once( LRM_PATH . '/vendor/autoload.php' );

require_once LRM_PATH . '/includes/class-core.php';

add_action('plugins_loaded', array('LRM_Core', 'get'), 11);

/**
 * Helper function to determine is PRO version installed
 * @since 1.20
 * @return bool
 */
function lrm_is_pro() {
	return class_exists("LRM_Pro");
}
