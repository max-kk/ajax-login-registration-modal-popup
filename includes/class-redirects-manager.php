<?php

defined( 'ABSPATH' ) || exit;

use underDEV\Utils\Settings\CoreFields;
/**
 * Actions/Redirects manager
 *
 * @since      2.00
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRM_Redirects_Manager {

    /**
     * Register settings
     * @param \underDEV\Utils\Settings $settings_class
     * @throws Exception
     */
    public static function register_settings( $settings_class ) {

	    $ACTIONS_SECTION = $settings_class->add_section( __( 'Actions / Redirects', 'ajax-login-and-registration-modal-popup' ), 'redirects' );

        //$wp_pages_arr = self::_get_pages_arr();

        $ACTIONS_SECTION->add_group( __( 'After-Login actions', 'ajax-login-and-registration-modal-popup' ), 'login' )
            ->add_field( array(
                'slug'        => 'action',
                'name'        => __('Action after login', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        'none' => 'No action',
                        'hide' => 'Hide the modal and elements with classes ".lrm-hide-if-logged-in"',
                        'reload' => 'Reload (refresh) page',
                        'redirect' => 'Redirect to page [PRO]',
                    ),
                ),
                'default'     => 'none',
                //'description' => __('Select an action', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Select_W_PRO(), 'input' ),
                'sanitize'    => array( new LRM_Field_Select_W_PRO(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'redirect',
                'name'        => __('Redirect to (if "Redirect to page [PRO]" is selected)', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                ),
                'default'     => [],
                //'description' => __('Select an action', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Redirects(), 'input' ),
                'sanitize'    => array( new LRM_Field_Redirects(), 'sanitize' ),
            ) )
        ->description('Actions with a [PRO] label will work only with a PRO version installed.');


        $ACTIONS_SECTION->add_group( __( 'After-Registration actions', 'ajax-login-and-registration-modal-popup' ), 'registration' )
            ->add_field( array(
                'slug'        => 'action',
                'name'        => __('Action after registration', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        'none' => 'No action',
                        'auto-login' => 'Auto-login and stay on the page',
                        'reload' => 'Reload a page and auto-login',
                        'redirect' => 'Redirect to a page and auto-login [PRO]',
                        'email-verification' => 'Email verification (send password to the email)',
                        'email-verification-pro' => 'Email verification [PRO] (send a verify link)',
                        'email-verification-pro-w-redirect' => 'Email verification [PRO] + redirect to page below (send a verify link)',
                    ),
                ),
                'default'     => 'none',
                'description' => __('"Email verification (send password to the email)" is not effective if the user can set password during registration (in PRO)', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Select_W_PRO(), 'input' ),
                'sanitize'    => array( new LRM_Field_Select_W_PRO(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'email-verification-pro-after-action',
                'name'        => __('Action when the user click a verification link from Email verification [PRO]?', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        'login' => 'Redirect to the login page',
                        'default' => 'Auto-login and show plugin "Verification Done" page',
                        'redirect' => 'Auto-login and redirect to the "Registration redirect"',
                        'back' => 'Auto-login and back to the page where user did a registration',
                    ),
                ),
                'default'     => 'default',
                'description' => __('Select an action', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'redirect',
                'name'        => __('Redirect to (only if "Redirect to a page and auto-login [PRO]" or "Email verification [PRO] + redirect" selected)', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'per_role' => false,
                ),
                'default'     => [],
                //'description' => __('Select an action', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Redirects(), 'input' ),
                'sanitize'    => array( new LRM_Field_Redirects(), 'sanitize' ),
            ) );

        $ACTIONS_SECTION->add_group( __( 'After-Logout actions', 'ajax-login-and-registration-modal-popup' ), 'logout' )
            ->add_field( array(
                'slug'        => 'action',
                'name'        => __('Action after Logout', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        'none' => 'Stay on this page',
                        'home' => 'Redirect to the home page',
                        'redirect' => 'Redirect to the custom page [PRO]',
                    ),
                ),
                'default'     => 'none',
                //'description' => __('Select an action', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Select_W_PRO(), 'input' ),
                'sanitize'    => array( new LRM_Field_Select_W_PRO(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'redirect',
                'name'        => __('Custom page redirect', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'per_role' => true,
                ),
                'default'     => [],
                //'description' => __('Select an action', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new LRM_Field_Redirects(), 'input' ),
                'sanitize'    => array( new LRM_Field_Redirects(), 'sanitize' ),
            ) );


    }

    /**
     * @param string $action One of: 'login', 'registration', 'logout'
     * @param $user_ID
     *
     * @return integer
     */
    public static function get_redirect ( $action = 'login', $user_ID )
    {
        $redirect_to = !empty( $_REQUEST['redirect_to'] ) ? urldecode($_REQUEST['redirect_to']) : '';

        if ( !$redirect_to && lrm_is_pro( '1.50' ) ) {
            $redirect_to = LRM_PRO_Redirects_Manager::get_redirect( $action, $user_ID );
        }

        return apply_filters('lrm/redirect_url', $redirect_to, $action, $user_ID);
    }
    
}