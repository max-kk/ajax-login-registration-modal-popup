<?php

/**
 * Class LRM_Mailer
 * @since 1.14
 */
class LRM_Mailer {
	/**
	 * Send mail, similar to PHP's mail
	 *
	 * A true return value does not automatically mean that the user received the
	 * email successfully. It just only means that the method used was able to
	 * process the request without any errors.
	 *
	 * Using the two 'wp_mail_from' and 'wp_mail_from_name' hooks allow from
	 * creating a from address like 'Name <email@address.com>' when both are set. If
	 * just 'wp_mail_from' is set, then just the email address will be used with no
	 * name.
	 *
	 * The default content type is 'text/plain' which does not allow using HTML.
	 * However, you can set the content type of the email by using the
	 * {@see 'wp_mail_content_type'} filter.
	 *
	 * The default charset is based on the charset used on the blog. The charset can
	 * be set using the {@see 'wp_mail_charset'} filter.
	 *
	 * @since 1.14
	 *
	 * @global PHPMailer $phpmailer
	 *
	 * @param string|array $to          Array or comma-separated list of email addresses to send message.
	 * @param string       $subject     Email subject
	 * @param string       $mail_body     Message contents
	 * @param string       $mail_key     Email ID, like "registration"
	 * @param string|array $headers     Optional. Additional headers.
	 * @return bool Whether the email contents were sent successfully.
	 */
	public static function send( $to, $subject, $mail_body, $mail_key = '', $headers = '' ) {

	    $email_format = LRM_Settings::get()->setting('mails/mail/format');
        $is_html_format = in_array( $email_format, array('wc-text/html', 'text/html') );

		if ( $is_html_format  ) {
			// Convert links to html
			//$mail_body = make_clickable($mail_body);
			// EOR to <BR>
			$mail_body = nl2br($mail_body);
			add_filter( 'wp_mail_content_type', array( 'LRM_Mailer', 'set_mail_type' ) );
		}

        // The blogname option is escaped with esc_html on the way into the database in sanitize_option
        // we want to reverse this for the plain text arena of emails.
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $site_url = site_url();
        $home_url = home_url();

        // Replace Site-wide tags
        // @since 1.41
        $subject = str_replace(
            array(
                'YOUR BLOG NAME',
                '{{SITE_NAME}}',
                '{{SITE_URL}}',
                '{{HOME_URL}}',
                '{{EMAIL}}',
            ),
            array(
                $blogname,
                $blogname,
                $site_url,
                $home_url,
                $to,
            ),
            $subject
        );

        $mail_body = str_replace(
            array(
                'YOUR BLOG NAME',
                '{{SITE_NAME}}',
                '{{SITE_URL}}',
                '{{HOME_URL}}',
                '{{EMAIL}}',
            ),
            array(
                $blogname,
                $blogname,
                $site_url,
                $home_url,
                $to,
            ),
            $mail_body
        );

		if ( 'text/html' === $email_format || ('wc-text/html' === $email_format && !class_exists('WC_Emails')) ) {
            // Apply custom template
            $mail_body = str_replace('{{CONTENT}}', $mail_body, LRM_Settings::get()->setting('mails/template/code'));
        } elseif ( 'wc-text/html' === $email_format ) {
		    // Use WooCommerce template
            $mail_body = self::set_wc_style( $mail_body, $subject );
        }

		do_action( "lrm/mail/before_sent", $mail_key );

		$mail_sent = wp_mail( $to, $subject, $mail_body );

        do_action( "lrm/mail/after_sent", $mail_key );

		if ( $is_html_format ) {
			remove_filter( 'wp_mail_content_type', array( 'LRM_Mailer', 'set_mail_type' ) );
		}

		return $mail_sent;
	}

    /**
     * @param string $content
     * @param string $subject
     *
     * @return false|string
     *
     * @since 1.41
     */
	public static function set_wc_style( $content, $subject ) {
        ob_start();
        WC_Emails::instance();
        do_action( 'woocommerce_email_header', $subject, null );
        echo $content;
        do_action( 'woocommerce_email_footer', null );
        $content = ob_get_clean();

        ob_start();
        wc_get_template( 'emails/email-styles.php' );
        $css = apply_filters( 'woocommerce_email_styles', ob_get_clean() );

        if ( lrm_wc_version_gte('3.6') ) {
            $emogrifier_class = '\\Pelago\\Emogrifier';
        } else {
            $emogrifier_class = 'Emogrifier';
        }

        if ( ! class_exists( $emogrifier_class ) ) {
            include_once WC()->plugin_path() . '/includes/libraries/class-emogrifier.php';
        }
        try {
            $emogrifier = new $emogrifier_class( $content, $css );
            $content    = $emogrifier->emogrify();
        } catch ( Exception $e ) {
            $content = '<style type="text/css">' . $css . '</style>' . $content;
            lrm_log( 'LRM_Mailer::set_wc_style error', $e->getMessage() );
        }

        return $content;

    }

	/**
	 * Sets mail type to text/html for wp_mail
	 * @return  string  mail type
	 */
	public static function set_mail_type() {
		return 'text/html';
	}
}