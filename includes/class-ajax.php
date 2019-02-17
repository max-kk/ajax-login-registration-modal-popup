<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class LRM_AJAX
 *
 * Handles common public AJAX actions
 * @since 1.0
 */
class LRM_AJAX
{

    public static function login() {
        $start = microtime(true);

        // First check the nonce, if it fails the function will break
        self::_verify_nonce( 'security-login', 'ajax-login-nonce' );

        LRM_Core::get()->call_pro('check_captcha', 'login');

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
            $user_name = sanitize_user($info['user_login']);
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

            $action = lrm_setting('redirects/login/action');
            $redirect_url = LRM_Redirects_Manager::get_redirect( 'login', $user_signon->ID );

            wp_send_json_success(array(
                'logged_in' => true,
                'user_id'   => $user_signon->ID,
                'message'   => $message,
                'action'    => $redirect_url ? 'redirect' : $action,
                'redirect_url'=> $redirect_url,
            ));
        }
    }

    public static function signup() {
        // Verify nonce
        self::_verify_nonce( 'security-signup', 'ajax-signup-nonce' );

        LRM_Core::get()->call_pro('check_captcha', 'signup' );

        if ( !apply_filters('lrm/users_can_register', get_option("users_can_register") ) ) :
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/registration/disabled')));
        endif;

        $email = sanitize_email($_POST['email']);

        // Post values
        if ( ! LRM_Settings::get()->setting('general_pro/all/hide_username') ) {
            $user_login = sanitize_user(trim($_POST['username']));
        } else {
            $email_arr = explode('@', $email);
            $user_login = sanitize_user(trim($email_arr[0]), true);

            $user_exists = get_user_by( 'login', $user_login );

            if ( $user_exists ) {
                $user_login .= '_' . rand(99, 999);
            }

        }

        $display_first_and_last_name = LRM_Settings::get()->setting('general/registration/display_first_and_last_name');

        $first_name = '';
        $last_name = '';
        if ( $display_first_and_last_name ) {
            $first_name = sanitize_text_field( $_POST['first-name'] );
            $last_name  = ! empty($_POST['last-name']) ? sanitize_text_field( $_POST['last-name'] ) : '';
        }
        
        if ( !empty( $_POST['password'] ) && LRM_Settings::get()->setting('general_pro/all/allow_user_set_password') ) {
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

            /**
             * Tweak in case other plugins has changed user login during insert to DB
             * @since 1.41
             */
            $user = get_user_by( 'ID', $user_id );

            // Is user logged in?
            $user_signon = false;

            //if ( ! LRM_Settings::get()->setting('general/registration/user_must_confirm_email') ) {
            // TODO - migrate setting??

            if ( in_array( lrm_setting('redirects/registration/action'), ['auto-login', 'reload', 'redirect'] ) ) {
                $info = array();
                $info['user_login'] = $user->user_login;
                $info['user_password'] = $userdata['user_pass'];
                $info['remember'] = true;

                $user_signon = wp_signon( $info );
            }


            if ( apply_filters( "lrm/mails/registration/is_need_send", true, $user_id, $userdata, $user_signon) ) {

                $subject = LRM_Settings::get()->setting('mails/registration/subject');

                $mail_body = str_replace(
                    array(
                        '{{FIRST_NAME}}',
                        '{{LAST_NAME}}',
                        '{{USERNAME}}',
                        '{{PASSWORD}}',
                        '{{LOGIN_URL}}',
                    ),
                    array(
                        $user->first_name,
                        $user->last_name,
                        $user->user_login,
                        $userdata['user_pass'],
                        wp_login_url(),
                    ),
                    LRM_Settings::get()->setting('mails/registration/body')
                );

                $mail_body = apply_filters("lrm/mails/registration/body", $mail_body, $user->user_login, $userdata);

                $mail_sent = LRM_Mailer::send($email, $subject, $mail_body, 'registration');

            }

            if ( LRM_Settings::get()->setting('mails/admin_new_user/on') ) {

                // Admin Notification
                $switched_locale = switch_to_locale(get_locale());

                $mail_body = str_replace(
                    array(
                        '{{FIRST_NAME}}',
                        '{{LAST_NAME}}',
                        '{{USERNAME}}',
                        '{{EMAIL}}',
                        '{{USER_ADMIN_URL}}',
                    ),
                    array(
                        $user->first_name,
                        $user->last_name,
                        $user->user_login,
                        $email,
                        admin_url( 'user-edit.php?user_id=' . $user_id ),
                    ),
                    LRM_Settings::get()->setting('mails/admin_new_user/body')
                );

                $wp_new_user_notification_email_admin = array(
                    'to' => get_option('admin_email'),
                    /* translators: Password change notification email subject. %s: Site title */
                    'subject' => LRM_Settings::get()->setting('mails/admin_new_user/subject'),
                    'message' => $mail_body,
                    'headers' => '',
                );

                /**
                 * Filters the contents of the new user notification email sent to the site admin.
                 *
                 * @since 4.9.0
                 *
                 * @param array $wp_new_user_notification_email {
                 *     Used to build wp_mail().
                 *
                 * @type string $to The intended recipient - site admin email address.
                 * @type string $subject The subject of the email.
                 * @type string $message The body of the email.
                 * @type string $headers The headers of the email.
                 * }
                 * @param WP_User $user User object for new user.
                 * @param string $blogname The site title.
                 */
                $wp_new_user_notification_email_admin = apply_filters('wp_new_user_notification_email_admin', $wp_new_user_notification_email_admin, $user_id, $blogname);

                LRM_Mailer::send(
                    $wp_new_user_notification_email_admin['to'],
                    wp_specialchars_decode($wp_new_user_notification_email_admin['subject']),
                    $wp_new_user_notification_email_admin['message'],
                    'registration_admin',
                    $wp_new_user_notification_email_admin['headers']
                );

                if ($switched_locale) {
                    restore_previous_locale();
                }

            }

            if ( class_exists( 'WCVendors_Pro' ) ) {
                /**
                 * Tweaks for WC Vendors plugin
                 * @since 1.38
                 */
                do_action('woocommerce_created_customer', $user_id, $userdata, $userdata['user_pass']);
            }

            if ( is_wp_error($user_signon) ) {
                wp_send_json_success( array(
                    'logged_in' => false,
                    'message'   => $user_signon->get_error_message(),
                ) );
            }

            $action = lrm_setting('redirects/registration/action');
            $redirect_url = $user_signon ? LRM_Redirects_Manager::get_redirect( 'registration', $user_signon->ID ) : '';

            wp_send_json_success( array(
                'logged_in' => $user_signon ? true : false,
                'user_id'   => $user_id ? $user_id : false,
                'message'   => $user_signon ? lrm_setting( 'messages/registration/success' ) : lrm_setting( 'messages/registration/success_please_login' ),

                'redirect_url' => $redirect_url,
                'action'       => $action,
            ) );
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

        LRM_Core::get()->call_pro('check_captcha', 'lostpassword');

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

                $reset_pass_url = LRM_Pages_Manager::get_password_reset_url($password_reset_key, $user);

                // The blogname option is escaped with esc_html on the way into the database in sanitize_option
                // we want to reverse this for the plain text arena of emails.
                $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

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

                $mail_sent = LRM_Mailer::send( $to, $subject, $mail_body, 'lost_password' );

                if( !$mail_sent ) {
                    $errors->add('unable_send', LRM_Settings::get()->setting('messages/lost_password/unable_send'));
                }
            } else {
                $err_msg = LRM_Settings::get()->setting('messages/lost_password/something_wrong');
                if ( is_wp_error($password_reset_key) ) {
                    $err_msg .= ' ' . $password_reset_key->get_error_message();
                }
                $errors->add('something_wrong', $err_msg);
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

    /**
     * AJAX call
     */
    public static function password_reset() {

        self::_verify_nonce('security-password-reset2', 'ajax-password-reset-nonce' );

        if ( ! isset( $_POST['password1'] ) || empty( trim($_POST['password1']) ) ) {
            wp_send_json_error(array('message' => lrm_setting('messages/password/password_is_missing'), 'for'=>'password1'));
        }

        $errors = new WP_Error();

        $rp_data = self::_validate_password_reset($errors);

        if ( $errors->get_error_code() ) {
            wp_send_json_error(array(
                'message'=> implode('<br/>', $errors->get_error_messages())
            ));
        }

        $new_pass = wp_unslash( trim($_POST['password1']) );

        list($rp_key, $rp_login, $rp_path, $user) = $rp_data;

        $rp_cookie = 'wp-resetpass-' . COOKIEHASH;

        /**
         * Fires before the password reset procedure is validated.
         *
         * @since 3.5.0
         *
         * @param object           $errors WP Error object.
         * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
         */
        do_action( 'validate_password_reset', $errors, $user );


        if ( ( ! $errors->get_error_code() ) && $new_pass ) {
            reset_password($user, $new_pass);
            setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );

            wp_send_json_success(array(
                'message'=> __( 'Your password has been reset.' ) . ' <a href="' . esc_url( wp_login_url() ) . '" class="lrm-login">' . __( 'Log in' ) . '</a>'
            ));
        }

        wp_send_json_error(array(
            'message'=> implode('<br/>', $errors->get_error_messages())
        ));

    }

    /**
     * @param WP_Error $errors
     * @return array
     */
    public static function _validate_password_reset($errors ) {
        if ( ! isset( $_REQUEST['key'] ) || empty( $_REQUEST['key'] ) ) {
            $errors->add( 'empty_key', __( 'Your password reset link key is missing.' ) );
        }

        if ( ! isset( $_REQUEST['login'] ) || empty( $_REQUEST['login'] ) ) {
            $errors->add( 'empty_login', __( 'Your password reset link login is missing.' ) );
        }

        if ( $errors->get_error_code() ) {
            return [];
        }

        list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
        $rp_key = wp_unslash( $_REQUEST['key'] );
        $rp_login = wp_unslash( $_REQUEST['login'] );

//
//        if ( isset( $_GET['key'] ) ) {
//            $value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
//            setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
//            wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
//            exit;
//        }

        $user = false;
        if ( $rp_key && $rp_login ) {
            $user = check_password_reset_key( $rp_key, $rp_login );
            if ( $user && ! hash_equals( $rp_key, $rp_key ) ) {
                $user = false;
            }
        } else {
            $user = false;
        }

        if ( ! $user || is_wp_error( $user ) ) {

            $request_msg = ' ' . sprintf( __( 'Please <a href="%s" class="lrm-forgot-password">request a new link.</a>'), site_url('wp-login.php?action=lostpassword') );

            //setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
            if ( $user && $user->get_error_code() === 'expired_key' ) {
                $errors->add('invalidkey', __('Your password reset link appears to be invalid.') . $request_msg);
                //wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=expiredkey' ) );
            } else {
                $errors->add('expiredkey', __('Your password reset link has expired.') . $request_msg);
                //wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=invalidkey' ) );
            }
        }

        return [$rp_key, $rp_login, $rp_path, $user];

    }

    public static function _verify_nonce( $post_key, $nonce_key ) {
        if ( defined("WP_CACHE") ) {
            return true;
        }

        if ( !isset($_POST[$post_key]) || !wp_verify_nonce($_POST[$post_key], $nonce_key) ) {
            wp_send_json_error(array('message' => LRM_Settings::get()->setting('messages/other/invalid_nonce')));
        }
    }

}