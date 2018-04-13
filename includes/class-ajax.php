<?php

/**
 * Class LRM_AJAX
 *
 * Handles common public AJAX actions
 * @since 1.0
 */
class LRM_AJAX
{

    public static function login() {
        // First check the nonce, if it fails the function will break
        if (!isset($_POST['security-login']) || !wp_verify_nonce($_POST['security-login'], 'ajax-login-nonce')) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/other/invalid_nonce')));
        }

        LRM_Core::get()->call_pro('check_captcha');

        // Nonce is checked, get the POST data and sign user on
        $info = array();
        $info['user_login'] = sanitize_text_field(trim($_POST['username']));
        $info['user_password'] = sanitize_text_field(trim($_POST['password']));
        $info['remember'] = isset($_POST['remember-me']) ? true : false;

        if ( !$info['user_login'] ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/login/no_login'), 'for'=>'username'));
        }

        if ( !$info['user_password'] ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/login/no_pass'), 'for'=>'password'));
        }

        $user_signon = wp_signon( $info, false );

        if ( is_wp_error($user_signon) ){

            do_action('lrm/login_fail', $user_signon);

            wp_send_json_error(array('message'=>implode('<br/>', $user_signon->get_error_messages())));
        } else {

            do_action('lrm/login_successful', $user_signon);

            wp_send_json_success(array('logged_in' => true,'message'=>LRM_Settings::get()->setting('messages/login/success')));
        }
    }

    public static function signup() {
        // Verify nonce
        if (!isset($_POST['security-signup']) || !wp_verify_nonce($_POST['security-signup'], 'ajax-signup-nonce')) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/other/invalid_nonce')));
        }

        LRM_Core::get()->call_pro('check_captcha');

        if ( !get_option('users_can_register') ) :
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/registration/disabled')));
        endif;

        // Post values
        $first_name = sanitize_text_field($_POST['first-name']);
        $last_name = sanitize_text_field($_POST['last-name']);
        $email    = sanitize_email($_POST['email']);
        
        if ( isset( $_POST['password'] ) && LRM_Settings::get()->setting('general/registration/allow_user_set_password') ) {
            $password =  sanitize_text_field($_POST['password']);

            // Defined in: "\wp-includes\default-filters.php"
            remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
        } else {
            $password = wp_generate_password(10, true);
        }


        if ( !$first_name || !$last_name ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/registration/no_name')));
        }

        if ( !$email || !is_email($email) ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/registration/wrong_email')));
        }

        $user_login = sanitize_user( sanitize_title_with_dashes($first_name . '_' . $last_name) );

        // !! Disable system Emails
        // TODO - allow change this in settings
        // For "wp_update_user"
        remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
        // For "wp_update_user"
        add_filter( 'send_password_change_email', '__return_false' );


        $user_id = register_new_user( $user_login, $email );

        if ( is_wp_error($user_id) ) {
            wp_send_json_error(array(
                'message'   => implode('<br/>' ,$user_id->get_error_messages()),
                'from'      => 'register_new_user'
            ));
        }

        /**
         * IMPORTANT: You should make server side validation here!
         */
        $userdata = array(
	        'ID'         => $user_id,
            'user_pass'  => $password,
            'user_email' => $email,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'nickname'   => $first_name . ' ' . $last_name,
        );

        $user_id = wp_update_user( $userdata );

        // Return
        if( !is_wp_error($user_id) ) {

            do_action('lrm/registration_successful', $user_id);
            
            if ( LRM_Settings::get()->setting('general/registration/auto_login_after_registration') ) {
                $info = array();
                $info['user_login'] = $user_login;
                $info['user_password'] = $userdata['user_pass'];
                $info['remember'] = true;

                $user_signon = wp_signon( $info, false );
            }

            $subject = LRM_Settings::get()->setting('mails/registration/subject');

            $mail_body = str_replace(
                array(
                    '{{USERNAME}}',
                    '{{PASSWORD}}',
                    '{{LOGIN_URL}}',
                ),
                array(
                    $user_login,
                    $userdata['user_pass'],
                    wp_login_url(),
                ),
                LRM_Settings::get()->setting('mails/registration/body')
            );

            wp_mail($email, $subject, $mail_body);

            wp_send_json_success(array(
                'logged_in' => true,
                'message' => LRM_Settings::get()->setting('messages/registration/success')
            ));
        } else {

            do_action('lrm/registration_fail', $user_id);

            wp_send_json_error(array(
                'message'=> implode('<br/>', $user_id->get_error_messages())
            ));
        }
    }

    public static function lostpassword() {
        // First check the nonce, if it fails the function will break
        if (!isset($_POST['security-lostpassword']) || !wp_verify_nonce($_POST['security-lostpassword'], 'ajax-forgot-nonce')) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/other/invalid_nonce')));
        }

        $errors = new WP_Error();

        $account = sanitize_email( trim($_POST['user_login']) );

        LRM_Core::get()->call_pro('check_captcha');

        if( empty( $account ) ) {
            $errors->add('invalid_email', LRM_Settings::get()->setting('messages/lost_password/invalid_email'));
        } else {
            if(is_email( $account )) {
                if( email_exists($account) )
                    $get_by = 'email';
                else
                    $errors->add('email_not_exists', LRM_Settings::get()->setting('messages/lost_password/email_not_exists'));
            }
            else if (validate_username( $account )) {
                if( username_exists($account) )
                    $get_by = 'login';
                else
                    $errors->add('user_not_exists', LRM_Settings::get()->setting('messages/lost_password/user_not_exists'));
            }
            else
                $errors->add('invalid_email_or_username', LRM_Settings::get()->setting('messages/lost_password/invalid_email_or_username'));
        }

        /**
         * Fires before errors are returned from a password reset request.
         *
         * @since 2.1.0
         * @since 4.4.0 Added the `$errors` parameter.
         *
         * @param WP_Error $errors A WP_Error object containing any errors generated
         *                         by using invalid credentials.
         */
        do_action( 'lostpassword_post', $errors );


        if( !$errors->get_error_messages() ) {
            // For "wp_update_user"
            add_filter( 'send_password_change_email', '__return_false' );

            // lets generate our new password
            //$random_password = wp_generate_password( 12, false );
            $random_password = wp_generate_password();

            // Get user data by field and data, fields are id, slug, email and login
            $user = get_user_by( $get_by, $account );

            $update_user = wp_update_user( array ( 'ID' => $user->ID, 'user_pass' => $random_password ) );

            // if  update user return true then lets send user an email containing the new password
            if( $update_user ) {
                $to = $user->user_email;
                $subject = LRM_Settings::get()->setting('mails/lost_password/subject');

                $mail_body = str_replace(
                    array(
                        '{{USERNAME}}',
                        '{{PASSWORD}}',
                        '{{LOGIN_URL}}',
                    ),
                    array(
                        $user->user_login,
                        $random_password,
                        wp_login_url(),
                    ),
                    LRM_Settings::get()->setting('mails/lost_password/body')
                );

                $mail_sent = wp_mail( $to, $subject, $mail_body );

                if( !$mail_sent ) {
                    $errors->add('unable_send', LRM_Settings::get()->setting('messages/lost_password/unable_send'));
                }
            } else {
                $errors->add('something_wrong', LRM_Settings::get()->setting('messages/lost_password/something_wrong'));
            }
        }

        // Return
        if( ! empty( $error ) ) {
            do_action('lrm/lost_password_fail', $errors);

            wp_send_json_error(array(
                'message'=> implode('<br/>', $errors->get_error_messages())
            ));
        } else {
            do_action('lrm/lost_password_successful', false);

            wp_send_json_success(array(
                'message'=>LRM_Settings::get()->setting('messages/lost_password/success')
            ));
        }
    }

}