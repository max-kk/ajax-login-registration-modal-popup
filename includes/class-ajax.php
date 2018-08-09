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
        self::_verify_nonce( 'security-login', 'ajax-login-nonce' );

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

        $secure_cookie = is_ssl();

        // If the user wants ssl but the session is not ssl, force a secure cookie.
        if ( !$secure_cookie && !empty($info['user_login']) && !force_ssl_admin() ) {
            $user_name = sanitize_user($_POST['log']);
            $user = get_user_by( 'login', $user_name );

            if ( ! $user && strpos( $user_name, '@' ) ) {
                $user = get_user_by( 'email', $user_name );
            }

            if ( $user ) {
                if ( get_user_option('use_ssl', $user->ID) ) {
                    $secure_cookie = true;
                    force_ssl_admin(true);
                }
            }
        }

        $user_signon = wp_signon( $info, $secure_cookie );

        if ( !is_wp_error($user_signon) && empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
            if ( headers_sent() ) {
                /* translators: 1: Browser cookie documentation URL, 2: Support forums URL */
                $user_signon = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.' ),
                    __( 'https://codex.wordpress.org/Cookies' ), __( 'https://wordpress.org/support/' ) ) );
            }
        }

        if ( is_wp_error($user_signon) ){

            do_action('lrm/login_fail', $user_signon);

            wp_send_json_error(array('message'=>implode('<br/>', $user_signon->get_error_messages())));
        } else {

            do_action('lrm/login_successful', $user_signon);

            $message = LRM_Settings::get()->setting('general/registration/reload_after_login') ? LRM_Settings::get()->setting('messages/login/success') : LRM_Settings::get()->setting('messages/login/success_no_reload');

            wp_send_json_success(array('logged_in' => true,'message'=>$message));
        }
    }

    public static function signup() {
        // Verify nonce
        self::_verify_nonce( 'security-signup', 'ajax-signup-nonce' );

        LRM_Core::get()->call_pro('check_captcha');

        if ( !get_option('users_can_register') ) :
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/registration/disabled')));
        endif;

        // Post values
        $user_login = sanitize_user(trim($_POST['username']));
        
        $display_first_and_last_name = LRM_Settings::get()->setting('general/registration/display_first_and_last_name');
        
        if ( $display_first_and_last_name ) {
            $first_name = sanitize_text_field( $_POST['first-name'] );
            $last_name  = sanitize_text_field( $_POST['last-name'] );
        }
        
        $email = sanitize_email($_POST['email']);
        
        if ( isset( $_POST['password'] ) && LRM_Settings::get()->setting('general_pro/all/allow_user_set_password') ) {
            $password =  sanitize_text_field($_POST['password']);

            // Defined in: "\wp-includes\default-filters.php"
            remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
        } else {
            $password = wp_generate_password(10, true);
        }


        if ( !$user_login ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/registration/no_username'), 'for'=>'username'));
        }

        if ( $display_first_and_last_name && !$first_name ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/registration/no_name'), 'for'=>'first-name'));
        }

        if ( !$email || !is_email($email) ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/registration/wrong_email'), 'for'=>'email'));
        }

//        $user_login = sanitize_user( sanitize_title_with_dashes($first_name . '_' . $last_name) );
//
//        $user_login = rtrim($user_login, '_-');

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
        );
        
        if ( $display_first_and_last_name ) {
            $userdata['first_name'] = $first_name;    
            $userdata['last_name'] = $last_name;
            $userdata['nickname'] = $first_name . ' ' . $last_name;
        } else {
            $userdata['nickname'] = $user_login;
        }

        $user_id = wp_update_user( $userdata );

        // Return
        if( !is_wp_error($user_id) ) {

            do_action('lrm/registration_successful', $user_id);

            // Is user logged in?
            $user_signon = false;

            if ( ! LRM_Settings::get()->setting('general/registration/user_must_confirm_email') ) {
                $info = array();
                $info['user_login'] = $user_login;
                $info['user_password'] = $userdata['user_pass'];
                $info['remember'] = true;

                $user_signon = wp_signon( $info, false );
            }

            if ( apply_filters( "lrm/mails/registration/is_need_send", true, $user_id, $userdata, $user_signon) ) {

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

                $mail_body = apply_filters("lrm/mails/registration/body", $mail_body, $user_login, $userdata);

                $mail_sent = LRM_Mailer::send($email, $subject, $mail_body);
            }

            if ( $user_signon && !is_wp_error($user_signon) ) {
                wp_send_json_success( array(
                    'logged_in' => true,
                    'message'   => LRM_Settings::get()->setting( 'messages/registration/success' )
                ) );
            } else {
                wp_send_json_success( array(
                    'logged_in' => false,
                    'message'   => LRM_Settings::get()->setting( 'messages/registration/success_please_login' )
                ) );
            }
        } else {

            do_action('lrm/registration_fail', $user_id);

            wp_send_json_error(array(
                'message'=> implode('<br/>', $user_id->get_error_messages())
            ));
        }
    }

    public static function lostpassword() {
        // First check the nonce, if it fails the function will break
        self::_verify_nonce( 'security-lostpassword', 'ajax-forgot-nonce' );

        $errors = new WP_Error();

        $account = sanitize_text_field( trim($_POST['user_login']) );

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

            // Get user data by field and data, fields are id, slug, email and login
            $user = get_user_by( $get_by, $account );

            $password_reset_key = get_password_reset_key( $user );

            // if  update user return true then lets send user an email containing the new password
            if( $password_reset_key && ! is_wp_error($password_reset_key) ) {
                $to = $user->user_email;
                $subject = LRM_Settings::get()->setting('mails/lost_password/subject');

                $reset_pass_url = '';

                if ( class_exists( 'WooCommerce' ) ) {
                    $reset_pass_url = add_query_arg( array( 'key' => $password_reset_key, 'login' => rawurlencode( $user->user_login ) ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) );
                } else if ( is_multisite() ) {
                    $reset_pass_url = network_site_url( "wp-login.php?action=rp&key=$password_reset_key&login=" . rawurlencode( $user->user_login ), 'login' );
                } else {
                    $reset_pass_url = add_query_arg(
                        array('action'=>'rp', 'key'=>$password_reset_key, 'login'=> rawurlencode( $user->user_login )),
                        wp_login_url()
                    );
                }

                $reset_pass_url = apply_filters( 'lrm/lost_password/link', $reset_pass_url, $password_reset_key, $user );

                $mail_body = str_replace(
                    array(
                        '{{USERNAME}}',
                        '{{CHANGE_PASSWORD_URL}}',
                        '{{LOGIN_URL}}',
                    ),
                    array( 
                        $user->user_login,
                        $reset_pass_url,
                        wp_login_url(),
                    ),
                    LRM_Settings::get()->setting('mails/lost_password/body')
                );

                $mail_sent = LRM_Mailer::send( $to, $subject, $mail_body );

                if( !$mail_sent ) {
                    $errors->add('unable_send', LRM_Settings::get()->setting('messages/lost_password/unable_send'));
                }
            } else {
                $errors->add('something_wrong', LRM_Settings::get()->setting('messages/lost_password/something_wrong'));
            }
        }

        // Return
        if( $errors->get_error_messages() ) {
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

    public function _verify_nonce( $post_key, $nonce_key ) {
        if ( defined("WP_CACHE") ) {
            return true;
        }

        if ( !isset($_POST[$post_key]) || !wp_verify_nonce($_POST[$post_key], $nonce_key) ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/other/invalid_nonce')));
        }
    }

}