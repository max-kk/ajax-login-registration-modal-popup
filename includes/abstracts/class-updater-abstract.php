<?php
/**
 * Update related functions and actions.
 *
 * @since 2.02
 * @version 1.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * LRM_Updater Class.
 */
abstract class LRM_Updater_Abstract {

    /**
     * DB updates and callbacks that need to be run per version.
     *
     * 
     * @var array
     */
    protected $db_updates = array();
    
    private $slug = '';
    private $option_version_key = '';
    private $curr_version = '';

    /**
     * Hook in tabs.
     */
    public function __construct( $slug, $option_version_key, $version ) {
        // For tests
        //delete_option($this->option_version_key);
        
        $this->slug = $slug;
        $this->option_version_key = $option_version_key;
        $this->curr_version = $version;

        if ( $this->curr_version !== $this->_get_db_version() ) {

            // Check if we are not already running this routine.
            if ( 'yes' === get_transient( $this->slug.'_updating' ) ) {
                return;
            }

            // If we made it till here nothing is running yet, lets set the transient now.
            set_transient( $this->slug.'_updating', 'yes', MINUTE_IN_SECONDS * 1 );

            lrm_log(  "pre check for update to {$this->curr_version} db ver", $this->_get_db_version() );

            if ( $this->needs_run_update() ) {
                $this->update();
            }
            $this->update_version();
        }

    }
    
    public function _get_db_version() {
        return get_option( $this->option_version_key );
    }

    /**
     * Is this a brand new EVF install?
     *
     * @return boolean
     */
    private function is_new_install() {
        return is_null( get_option( $this->option_version_key, null ) );
    }

    /**
     * Is a DB update needed?
     *
     * @return boolean
     */
    private function needs_run_update() {
        $curr_version = get_option( $this->option_version_key );
        $updates      = $this->get_db_update_callbacks();

        return $this->curr_version && version_compare( $curr_version, max( array_keys( $updates ) ), '<' );
    }

    /**
     * See if we need the wizard or not.
     */
//    private function maybe_enable_setup_wizard() {
//        if ( apply_filters( 'everest_forms_enable_setup_wizard', $this->is_new_install() ) ) {
//            set_transient( '_evf_activation_redirect', 1, 30 );
//        }
//    }

    /**
     * Get list of DB update callbacks.
     *
     * @return array
     */
    public function get_db_update_callbacks() {
        return $this->db_updates;
    }


    /**
     * Update DB version to current.
     *
     * @param string|null $version New EverestForms DB version or null.
     */
    public function update_version($version = null ) {
        delete_option( $this->option_version_key );
        add_option( $this->option_version_key, is_null( $version ) ? $this->curr_version : $version );
    }


    /**
     * Push all needed DB updates to the queue for processing.
     */
    private function update() {
        $current_db_version = get_option( $this->option_version_key );

        foreach ( $this->get_db_update_callbacks() as $version => $update_callbacks ) {
            if ( version_compare( $current_db_version, $version, '<' ) ) {

                lrm_log( "Run update to", $version );

                foreach ( $update_callbacks as $update_callback ) {
                    $this->$update_callback();
                    lrm_log( "Run update callback", __CLASS__."::".$update_callback );
//                    $logger->info(
//                        sprintf( 'Queuing %s - %s', $version, $update_callback ),
//                        array( 'source' => 'evf_db_updates' )
//                    );
                }
            }
        }
        lrm_log( "LRM_Updater - update done to the version", $this->curr_version );
    }


    /**
     * Helper
     *
     * @param $strings_to_migrate
     * @throws Exception
     */
    public function _update_strings( $strings_to_migrate ) {
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
    public function _move_options( $options_to_migrate ) {

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
