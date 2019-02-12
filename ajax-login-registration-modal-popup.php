<?php
/**
	Plugin Name:    AJAX Login and Registration modal popup DEV + inline
	Plugin URI:     https://maxim-kaminsky.com/shop/product/ajax-login-and-registration-modal-popup-pro/
	Description:    Easy to integrate modal with Login and Registration features.
<<<<<<< HEAD
	Version:        2.00
=======
	Version:        1.41
>>>>>>> 77157a6b4927006a5788ce89f08bd5719fbafea8
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

<<<<<<< HEAD

// Stop IF Pro version exists > 1.50 with the in-build Free version
if ( class_exists('LRM_Pro') && defined("LRM_URL") && lrm_is_pro('1.50') && ! defined("LRM_ALWAYS_LOAD_FREE") ) {
    return;
}

=======
>>>>>>> 77157a6b4927006a5788ce89f08bd5719fbafea8
define("LRM_URL", plugin_dir_url(__FILE__));
define("LRM_ASSETS_URL", LRM_URL . '/assets/');

define("LRM_PATH", plugin_dir_path(__FILE__));
define("LRM_BASENAME", plugin_basename( __FILE__ ));
<<<<<<< HEAD
define("LRM_VERSION", '2.00');

define("LRM_ASSETS_VER", 24);
=======
define("LRM_VERSION", '1.40');

define("LRM_ASSETS_VER", 18);

//define("LRM/SETTINGS/TRY_GET_TRANSLATED", 1);
>>>>>>> 77157a6b4927006a5788ce89f08bd5719fbafea8

//define("LRM/SETTINGS/TRY_GET_TRANSLATED", 1);

require_once( LRM_PATH . 'includes/helpers.php' );
require_once( LRM_PATH . 'vendor/autoload.php' );

//require_once LRM_PATH . '/includes/class-core.php';

add_action('plugins_loaded', array('LRM_Core', 'get'), 11);

<<<<<<< HEAD

if (!SHORTINIT) {
    /**
     * The code that runs during plugin deactivation.
     */
    register_deactivation_hook( __FILE__, array( 'LRM_Deactivator', 'deactivate' ) );
}
=======
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
>>>>>>> 77157a6b4927006a5788ce89f08bd5719fbafea8
