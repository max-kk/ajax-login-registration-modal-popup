<?php

/**
 * Class LRM_New_User_Approve_Integration
 * @since 2.15
 */
class LRM_New_User_Approve_Integration {

	static function init() {
		add_filter( 'new_user_approve_pending_message', ['LRM_New_User_Approve_Integration', 'registration_needs_approval'] );
	}

	static function registration_needs_approval( $success_message ) {
		wp_send_json_success( array(
			'logged_in' => false,
			'message'   => $success_message,
		) );
	}

}
