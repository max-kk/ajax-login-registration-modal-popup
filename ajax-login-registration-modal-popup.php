<?php
/**
	Plugin Name:    AJAX Login and Registration modal popup DEV
	Plugin URI:     https://maxim-kaminsky.com/shop/product/ajax-login-and-registration-modal-popup-pro/
	Description:    Easy to integrate modal with Login and Registration features.
	Version:        1.35
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
define("LRM_VERSION", '1.35');

define("LRM_ASSETS_VER", 18);

//define("LRM/SETTINGS/TRY_GET_TRANSLATED", 1);

require_once( LRM_PATH . '/vendor/autoload.php' );

require_once LRM_PATH . '/includes/class-core.php';

add_action('plugins_loaded', array('LRM_Core', 'get'), 11);

/**
 * Helper function to determine is PRO version installed
 * @since 1.20
 *
 * @param float $required_version
 *
 * @return bool
 */
function lrm_is_pro( $required_version = false ) {
	if ( ! class_exists("LRM_Pro") || !defined('LRM_PRO_VERSION') ) {
		return false;
	}
	if ( !$required_version ) {
		return true;
	}
	return version_compare(LRM_PRO_VERSION, $required_version, '>=');
}

/**
 * Get single setting value
 * @uses   SettingsAPI Settings API class
 * @param  string $setting_slug setting section/group/field separated with /
 * @param  bool do_stripslashes
 * @return mixed           field value or null if name not found
 */
function lrm_setting( $setting_slug, $do_stripslashes = false ) {
    return LRM_Settings::get()->setting( $setting_slug, $do_stripslashes = false );
}