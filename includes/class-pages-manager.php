<?php

defined( 'ABSPATH' ) || exit;

use underDEV\Utils\Settings\CoreFields;
/**
 * Pages management - get url, create, etc
 *
 * @since      2.00
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRM_Pages_Manager {

    public static function init() {
        add_filter( 'login_url', [__CLASS__, 'custom_login_url'], 99, 2 );
        add_filter( 'register_url', [__CLASS__, 'custom_register_url'], 99, 1 );
        if ( is_admin() ) {
	        add_filter( 'wp_new_user_notification_email', [__CLASS__, 'wp_new_user_notification_email__filter'], 99, 3 );
        }
    }

	static function wp_new_user_notification_email__filter($wp_new_user_notification_email, $user, $blogname) {
		$re = '/<http?s?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/\/=]*)>/m';
		//$str = '<http://dev.maxim-kaminsky.com/wp-login.php?action=rp&key=gc359IE3QKkAL7cLJQwI&login=cu2>';

		preg_match_all($re, $wp_new_user_notification_email['message'], $matches);

		if ( empty($matches) || empty($matches[0]) ) {
			return $wp_new_user_notification_email;
		}

		$parsed_url = parse_url( $matches[0][0] );
		parse_str( $parsed_url['query'], $parts );

		if ( empty($parts['key']) ) {
			return $wp_new_user_notification_email;
		}

		$wp_new_user_notification_email['message'] = str_replace(
			$matches[0],
			LRM_Pages_Manager::get_password_reset_url( $parts['key'], $user ),
			$wp_new_user_notification_email['message']
		);

		return $wp_new_user_notification_email;
	}

    /**
     * Register settings
     * @param \underDEV\Utils\Settings $settings_class
     * @throws Exception
     */
    public static function register_settings( $settings_class ) {

        $PAGES_SECTION = $settings_class->add_section( __( 'Pages', 'ajax-login-and-registration-modal-popup' ), 'pages', false );

	    $wp_pages_arr = self::_get_pages_arr();

        $PAGES_SECTION->add_group( __( 'Login', 'ajax-login-and-registration-modal-popup' ), 'login' )

            ->add_field( array(
                'slug'        => 'page',
                'name'        => __('Login page', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array('wp-login' => 'WP-LOGIN.PHP [default]') + $wp_pages_arr,
                ),
                'default'     => 'wp-login',
                'description' => __('Please make sure that page content contains shortcode:<br> <code>[lrm_form default_tab="login" logged_in_message="You are currently logged in!"]</code>', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) )
        ->description(
            __( 'Here you could override default WP pages (login, registration, restore password) to your custom pages. ', 'ajax-login-and-registration-modal-popup' )
            . ' <a href="https://docs.maxim-kaminsky.com/lrm/kb/how-to-create-custom-login-registration-pages/" target="_blank">Docs ></a>'
        );

        $PAGES_SECTION->add_group( __( 'Registration', 'ajax-login-and-registration-modal-popup' ), 'registration' )
            ->add_field( array(
                'slug'        => 'page',
                'name'        => __('Registration page', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array('wp-login' => 'WP-LOGIN.PHP [default]') + $wp_pages_arr,
                ),
                'default'     => 'wp-login',
                'description' => __('Please make sure that page content contains shortcode:<br> <code>[lrm_form default_tab="register" logged_in_message="You are currently logged in!"]</code>', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) );

        $PAGES_SECTION->add_group( __( 'Restore password', 'ajax-login-and-registration-modal-popup' ), 'restore-password' )
            ->add_field( array(
                'slug'        => 'page',
                'name'        => __('Restore password page', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array('wp-login' => 'WP-LOGIN.PHP [default]') + $wp_pages_arr,
                ),
                'default'     => 'wp-login',
                'description' => __('Please make sure that page content contains shortcode:<br> <code>[lrm_lostpassword_form logged_in_message="You are currently logged in!"]</code>', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) );

    }

    /**
     * @param bool $cached
     * @return array
     */
    public static function _get_pages_arr( $cached = true ) {
	    if ( ! ( is_admin() && isset($_GET['page']) && 'login-and-register-popup' === $_GET['page'] ) ) {
			return [];
	    }

	    if ( $cached && $pages_list = wp_cache_get( 'lrm_pages_list', 'lrm' ) ) {
            return $pages_list;
        }

        $pages_list = array();
        $post_title = '';


        $args = array(
            'post_type' => 'page',
            'suppress_filters' => false,
            'post_status' => 'publish',
            'perm' => 'readable',
            'posts_per_page' => 500,
            //'fields' => 'ids',
        );

        $query = new WP_Query($args);

        foreach ($query->posts as $page) {
            $post_title = $page->post_title;
            if ( 'publish' != $page->post_status ) {
                $post_title .= ' [' . $page->post_status . ']';
            }
            $pages_list[(string)$page->ID] = $post_title . ' [#' . $page->ID . ']';
        }



//        global $wpdb;
//
//        $pages = $wpdb->get_results(
//            "SELECT `ID`,`post_title`,`post_status` FROM `{$wpdb->posts}`  WHERE (`post_type` = 'page' AND `post_status` IN ('publish', 'private', 'draft')) ORDER BY `ID` DESC LIMIT 0, 500;"
//        );
//
//        $pages_list = array();
//        $post_title = '';
//        foreach ( $pages as $page ) {
//            $post_title = $page->post_title;
//            if ( 'publish' != $page->post_status ) {
//                $post_title .= ' [' . $page->post_status . ']';
//            }
//            $pages_list[(string)$page->ID] = $post_title . ' [#' . $page->ID . ']';
//        }

        if ( $cached ) {
            wp_cache_add( 'lrm_pages_list', $pages_list, 'lrm' );
        }

        return $pages_list;
    }

    /**
     * @param $password_reset_key
     * @param $user
     * @return string
     */
    public static function get_password_reset_url ( $password_reset_key, $user ) {
        $reset_pass_url = '';

        if ( lrm_is_pro() && lrm_setting('integrations/woo/use_wc_reset_page') && class_exists( 'WooCommerce' ) ) {
            $reset_pass_url = add_query_arg( array( 'key' => $password_reset_key, 'login' => rawurlencode( $user->user_login ) ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) );
        } else if ( 'wp-login' !== lrm_setting('pages/restore-password/page') && lrm_setting('pages/restore-password/page') ) {
            $page_id = absint( lrm_setting('pages/restore-password/page') );

            if ( $page_id ) {
                $reset_pass_url = get_page_link($page_id);
            }
        }

        if ( ! $reset_pass_url ) {
            if (is_multisite()) {
                $reset_pass_url = network_site_url('wp-login.php', 'login');
            } else {
                //$reset_pass_url = wp_login_url();
                $reset_pass_url = site_url('wp-login.php', 'login');
            }

        }

        $reset_pass_url = add_query_arg(
            array('action' => 'rp', 'key' => $password_reset_key, 'login' => rawurlencode($user->user_login)),
            $reset_pass_url
        );

        return apply_filters( 'lrm/lost_password/link', $reset_pass_url, $password_reset_key, $user );
    }

    /**
     * @param string $page_type One of values: login, registration, restore-password
     * @return integer
     *
     */
    public static function get_page_id ( $page_type = 'login' )
    {

        if ( ! in_array($page_type, ['login', 'registration', 'restore-password']) ) {
            return false;
        }

        $page = lrm_setting('pages/' . $page_type . '/page');
        if ( 'wp-login' == $page || ! $page || ! is_numeric($page) ) {
            return false;
        }

        return $page;
    }


    /**
     * Override WP login page url if needed
     *
     * @param string $login_url    The login URL. Not HTML-encoded.
     * @param string $redirect     The path to redirect to on login, if supplied.
     *
     * @return string
     */
    static function custom_login_url( $login_url, $redirect ){
        // This will append /custom-login/ to you main site URL as configured in general settings (ie https://domain.com/custom-login/)

        $login_page_ID = self::get_page_id( 'login' );

        if ( !$login_page_ID ) {
            return $login_url;
        }

        $login_url_new = false;
        if ( $login_page_ID ) {
            $login_url_new = get_page_link($login_page_ID);
        }

        if ( !$login_url_new ) {
            return $login_url;
        }

        $login_url = $login_url_new;
        if ( ! empty( $redirect ) ) {
            $login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
        }

        return $login_url;
    }

    /**
     * Override WP login page url if needed
     * @param $register_url
     * @return bool|string
     */
    static function custom_register_url( $register_url ) {
        //$register_url

        $page_ID = self::get_page_id( 'registration' );

        if ( !$page_ID ) {
            return $register_url;
        }

        $url_new = false;
        if ( $page_ID ) {
            $url_new = get_page_link($page_ID);
        }

        if ( ! $url_new ) {
            return $register_url;
        }

        return $url_new;
    }

}
