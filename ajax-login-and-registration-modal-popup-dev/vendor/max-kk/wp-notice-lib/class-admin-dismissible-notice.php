<?php

if (!defined('ABSPATH')) {
    die('Access denied.');
}

/**
 * Allows create notice that can be dismissed
 *
 * Class WP_Admin_Dismissible_Notice
 */
class WP_Admin_Dismissible_Notice
{
    // Declare variables and constants
    protected static $instance;
    protected $notices = [];
    protected $types = ['info', 'success', 'error', 'warning'];
    protected $option_prefix = '-dismissed-notice-';

    protected $slug = 'wp';
    protected $printed = false;

    /**
     * Constructor
     */
    protected function __construct( $slug = '' )
    {

        if ( $slug ) {
            $this->slug = $slug;
        }
        $this->option_prefix = $this->slug . $this->option_prefix;

        if (is_admin()) {
            add_action('admin_notices', array($this, 'print_notices'));
        }
        add_action('wp_ajax_' . $this->slug .'_dismiss_notice', [$this, 'AJAX_dismiss_notice']);

    }

    public function AJAX_dismiss_notice(){
        // Process Dismiss
        if ( ! array_diff_key(['dismiss_notice', 'key', 'save_to', 'hash', '_wpnonce2'], array_keys($_GET)) ) {
            // Verify Nonce
            if ( !wp_verify_nonce($_GET['_wpnonce'], 'dismissible-notice') ) {
                wp_send_json_error();
            }

            $key = $_GET['key'];

//            // Verify that data was not changed
//            if ( $_GET['hash'] !== md5($_GET['key'] . NONCE_SALT . $_GET['save_to']) ) {
//                wp_send_json_error();
//            }

            if ( !isset($this->notices[$key]) ) {
                wp_send_json_error( 'Notice does not exists!' );
            }

            // Print only for users that have access to contest
            if ( ! current_user_can( $this->notices[$key]['required_capability'] ) ) {
                wp_send_json_error( 'You does not have required capability to dismiss!' );
            }


            // Save data
            $option_key = $this->option_prefix . $key;

            if ( 'option' == $this->notices[$key]['save_to'] ) {
                add_option( $option_key, current_time('timestamp') );
            } else if ( 'user_meta' == $this->notices[$key]['save_to'] ) {
                add_user_meta( get_current_user_id(), $option_key, current_time('timestamp') );
            }
            wp_send_json_success();
        }
    }

    public function get_dismiss_url( $notice ) {
        return add_query_arg(
            [
                'action'        => $this->slug . '_dismiss_notice',
                'dismiss_notice'=> 1,
                'key'           => $notice['key'],
                'save_to'       => $notice['save_to'],
                //'hash'          => md5($notice['key'] . NONCE_SALT . $notice['save_to']),
                '_wpnonce'      => wp_create_nonce( 'dismissible-notice' ),
            ],
            add_query_arg( 'ModPagespeed', 'off', admin_url('admin-ajax.php') )
        );
    }

    public function is_dismissed( $notice ) {
        $key = $this->option_prefix . $notice['key'];

        if ( 'option' == $notice['save_to'] ) {
            return get_option( $key, false );
        } else if ( 'user_meta' == $notice['save_to'] ) {
            return get_user_meta( get_current_user_id(), $key, true );
        }
    }

    /**
     * Queues up a message to be displayed to the user
     *
     * @param string $key Unique key
     * @param string $message The text to show the user
     * @param string $type 'info', 'success', 'error', 'warning'
     * @param string string $required_capability Capability to view and dismiss 'manage_options' by default
     * @param string $save_state_to 'options', 'user_meta'
     */
    public function enqueue($key, $message, $type = 'info', $required_capability = 'manage_options', $save_state_to = 'option')
    {
        if ( empty($type) || ! in_array($type, $this->types) ) {
            trigger_error("{$type} type is not allowed!", E_USER_WARNING);
        }

        if ( !isset($this->notices[$key]) ) {

            $notice = [
                'key' => $key,
                'type' => $type,
                'save_to' => $save_state_to,
                'message' => (string)$message,
                'required_capability' => (string)$required_capability,
            ];

            if ( !$this->is_dismissed($notice) ) {
                $this->notices[$key] = $notice;
            }
        }
    }

    /**
     * Displays updates and errors
     */
    public function print_notices()
    {

        if ( $this->printed ) {
            return;
        }

        foreach ($this->notices as $notice) {

            // Print only for users that have access to contest
            if ( ! current_user_can( $notice['required_capability'] ) ) {
                continue;
            }

            require('views/admin-dismissible-notice.php');
        }

        if ( $this->notices ) {
            add_action('admin_footer', [$this, 'admin_footer_js']);
        }

        $this->printed = true;
    }

    function admin_footer_js () {
        require_once "views/admin-footer.php";
    }


    /**
     * Provides access to a single instances of the class using the singleton pattern
     *
     * @return self
     */
    public static function get()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
} // end Admin_Notice_Helper