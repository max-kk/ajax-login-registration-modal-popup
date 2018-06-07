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
	 * @param string|array $headers     Optional. Additional headers.
	 * @return bool Whether the email contents were sent successfully.
	 */
	public static function send( $to, $subject, $mail_body, $headers = '' ) {

		if ( 'text/html' == LRM_Settings::get()->setting('mails/mail/format') ) {
			// Convert links to html
			$mail_body = make_clickable($mail_body);
			// EOR to <BR>
			$mail_body = nl2br($mail_body);
			add_filter( 'wp_mail_content_type', array( 'LRM_Mailer', 'set_mail_type' ) );
		}

		$mail_sent = wp_mail( $to, $subject, $mail_body );

		if ( 'text/html' == LRM_Settings::get()->setting('mails/mail/format') ) {
			remove_filter( 'wp_mail_content_type', array( 'LRM_Mailer', 'set_mail_type' ) );
		}

		return $mail_sent;
	}

	/**
	 * Sets mail type to text/html for wp_mail
	 * @return  string  mail type
	 */
	public static function set_mail_type() {
		return 'text/html';
	}
}