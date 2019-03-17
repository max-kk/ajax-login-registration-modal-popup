<?php

/**
 * Update related functions and actions.
 *
 * @since 1.51
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * LRM_Updater Class.
 */
class LRM_Updater extends LRM_Updater_Abstract{

    /**
     * DB updates and callbacks that need to be run per version.
     *
     * 
     * @var array
     */
    protected $db_updates = array(
        '2.00' => array(
            '_update_2_00',
        ),
    );

    /**
     * Run the class
     */
    public static function init() {
        new self();
    }

    public function __construct()
    {
        parent::__construct('lrm', 'lrm_version', LRM_VERSION);
    }

    /**
     * Update to version 2.00
     */
    public function _update_2_00() {

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
}
