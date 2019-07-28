<?php

defined( 'ABSPATH' ) || exit;

use underDEV\Utils\Settings\CoreFields;
/**
 * Actions/Redirects manager
 *
 * @since      2.05
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRM_Roles_Manager {

    /**
     * Register settings
     * @param \underDEV\Utils\Settings $settings_class
     * @throws Exception
     */
    public static function register_settings( $settings_class ) {

	    $SECTION = $settings_class->add_section( __( 'Registration User role  > PRO', 'ajax-login-and-registration-modal-popup' ), 'user_role' );

        $SECTION->add_group( __( 'User role selector during Registration', 'ajax-login-and-registration-modal-popup' ), 'general' )
	        ->add_field( array(
		        'slug'        => 'on',
		        'name'        => __( 'Enable User role selector?', 'ajax-login-and-registration-modal-popup' ),
		        'description' => __( 'This will add a roles dropdown to the registration form.', 'ajax-login-and-registration-modal-popup' ),
		        'default'     => false,
		        'addons'      => array('label' => __( 'Yes' )),
		        'render'      => array( new CoreFields\Checkbox(), 'input' ),
		        'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
	        ) )
            ->add_field( array(
                'slug'        => 'active_roles',
                'name'        => __( 'Active roles:', 'ajax-login-and-registration-modal-popup' ),
	            //'description' => __( 'First role will be selected as default', 'ajax-login-and-registration-modal-popup' ),
                'addons'      => array(
                    'options'     => LRM_Roles_Manager::get_wp_roles_flat(),
	                'pretty' => true,
	                'multiple' => true,
                ),
                'default'     => '',
                'render'      => array( new LRM_Field_Roles(), 'input' ),
                'sanitize'    => array( new LRM_Field_Roles(), 'sanitize' ),
            ) )
	        ->add_field( array(
		        'slug'        => 'silent',
		        'name'        => __( 'Enable silent role assign?', 'ajax-login-and-registration-modal-popup' ),
		        'description' => __( '<strong>Modal:</strong> With this option you could add a "data" attributes to your registration button for auto-assign user-role and probably hide role selector from the user.', 'ajax-login-and-registration-modal-popup' )
                         . '<br/>'
                    . sprintf(__( 'Use <code>data-lrm-role="Customer"</code> with the label of your role and <code>data-lrm-role-silent</code> to select specified role and hide role selector.<br/>Example: <code>%s</code>', 'ajax-login-and-registration-modal-popup' ), esc_attr('<a href="#register" class="lrm-register" data-lrm-role="Customer" data-lrm-role-silent>Register</a>'))
                         . '<br/>'
                     . __( '<strong>Inline:</strong> Use the shortcode attributes <code>role="Customer"</code> and <code>role_silent="yes"</code> (optional) to select specified role and hide role selector. Example: <code>[lrm_form default_tab="login" logged_in_message="You have been already logged in!" role="customer" role-silent="yes"]</code>', 'ajax-login-and-registration-modal-popup' ),
		        'default'     => false,
		        'addons'      => array('label' => __( 'Yes' )),
		        'render'      => array( new CoreFields\Checkbox(), 'input' ),
		        'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
	        ) )
        ->description('Allow user to select a role during registration or even silently assign user role from specific button or via shortcode params. Will work only with a PRO version installed.');

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

        return apply_filters('lrm/redirect_url', $redirect_to, $action);
    }

    /**
     * @return array
     */
    public static function get_active_allowed_roles_flat (  )
    {
		$allowed_roles = [];

        if ( lrm_is_pro( '1.65' ) ) {
            $redirect_to = LRM_PRO_Roles_Manager::get_redirect();
        }

        return apply_filters('lrm/active_allowed_roles ', $allowed_roles);
    }

    public static function get_wp_roles_flat() {

    	require_once ABSPATH . 'wp-admin/includes/user.php';

        $editable_roles = get_editable_roles();
        $roles = [];
        foreach ($editable_roles as $role => $details) {
            $roles[ $role ] = translate_user_role($details['name']);
        }

        return $roles;

    }

}
