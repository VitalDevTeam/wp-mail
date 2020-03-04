<?php
/*
	Plugin Name: WP Mail Customizations
	Plugin URI: https://vtldesign.com
	Description: Customizes various WordPress email functions
	Version: 1.0
	Author: Vital
	Author URI: https://vtldesign.com
	Text Domain: vital
	License: GPLv2
*/


if (! defined('ABSPATH')) {
	exit;
}

class Vital_WP_Mail {

	public function __construct() {

		add_filter('wp_mail_from_name', [$this, 'mail_from_name'], 10, 1);
		add_filter('wp_mail_from', [$this, 'mail_from'], 10, 1);

		// Fired when a user's email address is changed.
		add_filter('email_change_email', [$this, 'email_change_email'], 10, 3);

		// Fired when a user's password is changed. Does NOT fire during a user password reset.
		add_filter('password_change_email', [$this, 'password_change_email'], 10, 3);
	}

	/**
	 * Sets "From" name on outgoing mail to the site name if it is the default.
	 *
	 * @param string $from_name Name associated with the "from" email address.
	 * @return string Filtered name associated with the "from" email address.
	 */
	public function mail_from_name($from_name) {
		if ($from_name === 'WordPress') {
			$from_name = get_bloginfo('name');
		}
		return $from_name;
	}

	/**
	 * Sets "From" email address on outgoing mail if it is the default.
	 *
	 * @param string $from_email Email address to send from.
	 * @return string Filtered email address to send from.
	 */
	public function mail_from($from_email) {

		$sitename = strtolower($_SERVER['SERVER_NAME']);

		if (substr($sitename, 0, 4) === 'www.') {
			$sitename = substr($sitename, 4);
		}

		$default_from_email = 'wordpress@' . $sitename;

		if ($default_from_email === $from_email) {
			$from_email = 'REPLACEME@EXAMPLE.COM';
		}

		return $from_email;
	}

	/**
	 * Filters the contents of the email sent when the user's email is changed.
	 *
	 * @param array $email_change_email The email settings array.
	 * @param array $user The original user array.
	 * @param array $userdata The updated user array.
	 * @return array Filtered email settings array.
	 */
	public function email_change_email($email_change_email, $user, $userdata) {

		$email_change_email['subject'] = 'Email address changed on your account';

		$email_change_email['message'] = sprintf(
			'Hi %s,

This notice confirms that your email address on ###SITENAME### was changed to ###NEW_EMAIL###.

If you did not change your email, please contact us at REPLACEME@EXAMPLE.COM

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###',
			$user['first_name']
		);

		return $email_change_email;
	}

	/**
	 * Filters the contents of the email sent when the user's password is changed.
	 *
	 * @param array $pass_change_email The email settings array.
	 * @param array $user The original user array.
	 * @param array $userdata The updated user array.
	 * @return array Filtered email settings array.
	 */
	public function password_change_email($pass_change_email, $user, $userdata) {

		$pass_change_email['subject'] = 'Password changed on your account';

		$pass_change_email['message'] = sprintf(
			'Hi %s,

This notice confirms that your password was changed on ###SITENAME###.

If you did not change your password, please contact us at REPLACEME@EXAMPLE.COM

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###',
			$user['first_name']
		);

		return $pass_change_email;
	}

}

new Vital_WP_Mail();
