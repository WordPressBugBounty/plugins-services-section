<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'ssbIsPremium' ) ) {
	function ssbIsPremium() {
		return SSB_HAS_FRMS ? ss_fs()->can_use_premium_code() : false;
	}
}


if ( ! function_exists( 'ssb_restrict_free_user_access' ) ) {
	add_action( 'load-plugin-editor.php', function() {
		if ( ! ssbIsPremium() && isset( $_GET['file'] ) ) {
			$file = sanitize_text_field( wp_unslash( $_GET['file'] ) );

			$restricted_files = [
				'services-section/includes/utility/functions.php',
				'services-section/includes/ssbPlugin/plugin.php'
			];

			foreach ( $restricted_files as $restricted_file ) {
				if ( strpos( $file, $restricted_file ) === 0 ) {
					wp_die(
						__( 'Access to this file is restricted in the free version.', 'services-section' ),
						__( 'Permission Denied', 'services-section' ),
						array( 'response' => 403 )
					);
				}
			}
		}
	});
}


