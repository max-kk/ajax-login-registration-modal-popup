<?php

/**
 * Update related functions and actions.
 *
 * @since 1.51
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * EVF_Install Class.
 */
class LRM_Updater {

    /**
     * DB updates and callbacks that need to be run per version.
     *
     * 
     * @var array
     */
    private static $db_updates = array(
        '2.00' => array(
            '_update_2_00',
        ),
    );

    /**
     * Hook in tabs.
     */
    public static function init() {
        // For tests
        // delete_option('lrm_version');

        if ( LRM_VERSION !== get_option('lrm_version') ) {

            // Check if we are not already running this routine.
            if ( 'yes' === get_transient( 'lrm_updating' ) ) {
                return;
            }

            // If we made it till here nothing is running yet, lets set the transient now.
            set_transient( 'lrm_updating', 'yes', MINUTE_IN_SECONDS * 1 );

            if ( self::needs_run_update() ) {
                self::update();
            }
            self::update_version();
        }

    }

    /**
     * Is this a brand new EVF install?
     *
     * @return boolean
     */
    private static function is_new_install() {
        return is_null( get_option( 'lrm_version', null ) );
    }

    /**
     * Is a DB update needed?
     *
     * @return boolean
     */
    private static function needs_run_update() {
        $curr_version = get_option( 'lrm_version' );
        $updates      = self::get_db_update_callbacks();

        return LRM_VERSION && version_compare( $curr_version, max( array_keys( $updates ) ), '<' );
    }

    /**
     * See if we need the wizard or not.
     */
//    private static function maybe_enable_setup_wizard() {
//        if ( apply_filters( 'everest_forms_enable_setup_wizard', self::is_new_install() ) ) {
//            set_transient( '_evf_activation_redirect', 1, 30 );
//        }
//    }

    /**
     * Get list of DB update callbacks.
     *
     * @return array
     */
    public static function get_db_update_callbacks() {
        return self::$db_updates;
    }


    /**
     * Update DB version to current.
     *
     * @param string|null $version New EverestForms DB version or null.
     */
    public static function update_version($version = null ) {
        delete_option( 'lrm_version' );
        add_option( 'lrm_version', is_null( $version ) ? LRM_VERSION : $version );
    }


    /**
     * Push all needed DB updates to the queue for processing.
     */
    private static function update() {
        $current_db_version = get_option( 'lrm_version' );

        foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
            if ( version_compare( $current_db_version, $version, '<' ) ) {


                foreach ( $update_callbacks as $update_callback ) {
                    self::$update_callback();
                    lrm_log( "Run update callback", "LRM_Updater::".$update_callback );
//                    $logger->info(
//                        sprintf( 'Queuing %s - %s', $version, $update_callback ),
//                        array( 'source' => 'evf_db_updates' )
//                    );
                }
            }
        }
        lrm_log( "LRM_Updater - update done to version", LRM_VERSION );
    }

    /**
     * Update to version 2.00
     */
    public static function _update_2_00() {

        if ( lrm_is_pro() ) {

            $strings_to_migrate = [
                'messages_pro/registration/password_is_good'  => 'messages/password/password_is_good',
                'messages_pro/registration/password_is_strong'  => 'messages/password/password_is_strong',
                'messages_pro/registration/password_is_short'  => 'messages/password/password_is_short',
                'messages_pro/registration/password_is_bad'  => 'messages/password/password_is_bad',
                'messages_pro/registration/passwords_is_mismatch'  => 'messages/password/passwords_is_mismatch',
            ];

            self::_update_strings( $strings_to_migrate );

            self::_move_options([
                'lrm_btn_color' => 'lrm_default__btn_color',
                'lrm_btn_bg' => 'lrm_default__btn_bg',
            ]);

        }
    }

    /**
     * Helper
     *
     * @param $strings_to_migrate
     * @throws Exception
     */
    public static function _update_strings( $strings_to_migrate ) {
        $strings_to_update = [];

        foreach ($strings_to_migrate as $old_key => $new_key) {
            if ( lrm_setting($new_key) != lrm_setting($old_key) ) {

                $strings_to_update[$new_key] = lrm_setting($old_key);
            }
        }

        if ( $strings_to_update ) {
            $options = [];
            foreach ($strings_to_update as $setting_slug => $new_value) {
                $setting_path = explode('/', $setting_slug);

                if ( count($setting_path) !== 3 ) {
                    throw new Exception('Invalid $setting_slug: ' . $setting_slug);
                }

                $options[$setting_path[0]][$setting_path[1]][$setting_path[2]] = $new_value;
            }

            $option_from_bd = false;
            foreach ($options as $option_key => $option_data) {
                $option_from_bd = get_option('lrm_' . $option_key, []);

                update_option( 'lrm_' . $option_key, array_merge($option_from_bd, $option_data) );
            }

        }

    }

    /**
     * Helper
     *
     * @param array $options_to_migrate
     * @throws Exception
     */
    public static function _move_options( $options_to_migrate ) {

        $old_opt_val = null;
        foreach ($options_to_migrate as $old_opt => $new_opt) {
            $old_opt_val = get_option($old_opt, null);
            if ( ! is_null($old_opt_val) ) {
                add_option($new_opt, $old_opt_val);
                $deleted = delete_option($old_opt);
                lrm_log( 'LRM_Updater::_move_options: ' . $old_opt . ' to ' . $new_opt );
            }
        }
    }
}
