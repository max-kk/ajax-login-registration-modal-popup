<?php

if ( ! function_exists('lrm_is_pro') ) {
    /**
     * Helper function to determine is PRO version installed
     * @since 1.20
     *
     * @param float $required_version
     *
     * @return bool
     */
    function lrm_is_pro($required_version = false)
    {
        if (!class_exists("LRM_Pro") || !defined('LRM_PRO_VERSION')) {
            return false;
        }
        if (!$required_version) {
            return true;
        }
        return version_compare(LRM_PRO_VERSION, $required_version, '>=');
    }
}

if ( ! function_exists('lrm_setting') ) {
    /**
     * Get single setting value
     * @uses   SettingsAPI Settings API class
     * @param  string $setting_slug setting section/group/field separated with /
     * @param  bool do_stripslashes
     * @return mixed           field value or null if name not found
     */
    function lrm_setting($setting_slug, $do_stripslashes = false)
    {
        return LRM_Settings::get()->setting($setting_slug, $do_stripslashes = false);
    }
}

if (!function_exists('lrm_log')) {
    /**
     * @param string $label
     * @param string $data
     *
     * @since 2.00
     */
    function lrm_log($label, $data = '')
    {
        if ($data && !is_string($data)) {
            $data = print_r($data, true);
        }
        do_action("plain_logger", $label, $data);
    }
}

if (!function_exists('lrm_dismissible_notice')) {
    /**
     * Queues up a message to be displayed to the user
     *
     * @param string $key Unique key
     * @param string $message The text to show the user
     * @param string $type 'info', 'success', 'error', 'warning'
     * @param string string $required_capability Capability to view and dismiss 'manage_options' by default
     * @param string $save_state_to 'option', 'user_meta'
     */

    function lrm_dismissible_notice($key, $message, $type = 'info', $required_capability = 'manage_options', $save_state_to = 'option')
    {
        WP_Admin_Dismissible_Notice::get()->enqueue($key, $message, $type, $required_capability, $save_state_to);
    }
}


if (!function_exists('lrm_wc_version_gte')) {
    /**
     * Check WooCommerce version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    function lrm_wc_version_gte($version)
    {
        if (defined('WC_VERSION') && WC_VERSION) {
            return version_compare(WC_VERSION, $version, '>=');
        } else if (defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION) {
            return version_compare(WOOCOMMERCE_VERSION, $version, '>=');
        } else {
            return false;
        }
    }
}