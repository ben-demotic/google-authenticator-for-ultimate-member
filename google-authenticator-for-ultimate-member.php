<?php

/**
 *
 * @link              https://www.demotic.co.uk/
 * @since             1.0.0
 * @package           Google_Authenticator_for_Ultimate_Member
 *
 * @wordpress-plugin
 * Plugin Name:       Google Authenticator for Ultimate Member
 * Plugin URI:        https://www.demotic.co.uk/google-authenticator-for-ultimate-member/
 * Description:       Modifies Ultimate Member to remove a security vulnerability which causes a bypass of Google Authenticator OTPs.
 * Version:           1.0.0
 * Author:            Demotic Limited
 * Author URI:        https://www.demotic.co.uk/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       google-authenticator-for-ultimate-member
 * Domain Path:       /languages
 * 
 * Copyright (C) 2017 Demotic Limited
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Replace UM filters/actions.
 *
 * @since    1.0.0
 */
add_action( 'init', 'gaum_replace_hooks' );
function gaum_replace_hooks() {
	
	remove_filter( 'authenticate', 'um_wp_form_errors_hook_logincheck', 999, 3 );
	remove_action( 'wp_authenticate_username_password_before', 'um_auth_username_password_before', 10, 3 );
	add_filter( 'authenticate', 'gaum_wp_form_errors_hook_logincheck', 999, 3 );
	
}

/**
 * New UM login check.
 *
 * @since    1.0.0
 */
function gaum_wp_form_errors_hook_logincheck( $user, $username, $password ) {

	if ( is_wp_error( $user ) ) {
	   return $user;
	}

	if ( isset( $user->ID ) ) {

		um_fetch_user( $user->ID );
		$status = um_user('account_status');
		switch( $status ) {
			case 'inactive':
				return new WP_Error( $status, __('Your account has been disabled.','ultimate-member') );
				break;
			case 'awaiting_admin_review':
				return new WP_Error( $status, __('Your account has not been approved yet.','ultimate-member') );
				break;
			case 'awaiting_email_confirmation':
				return new WP_Error( $status, __('Your account is awaiting e-mail verification.','ultimate-member') );
				break;
			case 'rejected':
				return new WP_Error( $status, __('Your membership request has been rejected.','ultimate-member') );
				break;
		}

	}
	
	return $user;
}

?>