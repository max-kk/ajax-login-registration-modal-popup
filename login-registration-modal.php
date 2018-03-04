<?php
/**
 * Plugin Name:     AJAX Login and Registration modal popup
 * Plugin URI:      #
 * Description:     Easy to integrate modal with Login and Registration features.
 * Version:         1.00
 * Author:          Maxim K
 * Author URI:      http://maxim-kaminsky.com/
 * Text Domain:     lrm
 * Domain Path:     /languages
*/

define("LRM_URL", plugin_dir_url(__FILE__));
define("LRM_PATH", plugin_dir_path(__FILE__));
define("LRM_BASENAME", plugin_basename( __FILE__ ));

define("LRM_ASSETS_VER", 4);

require_once( LRM_PATH . '/vendor/autoload.php' );

require LRM_PATH . '/includes/class-settings.php';
require LRM_PATH . '/includes/class-ajax.php';
require LRM_PATH . '/includes/class-core.php';

add_action('plugins_loaded', array('LRM_Core', 'get'), 11);
