<?php

namespace SSB;

class RestAPI {
    function __construct() {
        add_action('wp_ajax_ssbPremiumChecker', [$this, 'ssbPremiumChecker']);
        add_action('wp_ajax_nopriv_ssbPremiumChecker', [$this, 'ssbPremiumChecker']);
        add_action('wp_ajax_ssbSaveUninstallOption', [$this, 'ssbSaveUninstallOption']);
        add_action('wp_ajax_ssbGetBlocks', [$this, 'ssbGetBlocks']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('rest_api_init', [$this, 'registerSettings']);
    }

    /**
     * ssbGetBlocks (AJAX) — read/write the list of DISABLED block folder names.
     *
     * GET  (no `data`) → returns the current `ssbBlocks` option.
     * POST (with `data`) → overwrites it with the posted JSON array.
     *
     * The option holds folder names as they appear in build/blocks/, which is what
     * SSB\Init skips at registration time and what the editor reads back through
     * SSB_BLOCK_DATA.disabledBlocks.
     */
    function ssbGetBlocks(){
        $nonce = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) );

        if ( ! wp_verify_nonce( $nonce, 'ssb_admin_nonce' ) ) {
            wp_send_json_error( 'Invalid Request' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'You do not have permission to perform this action.', 'services-section' ) ], 403 );
        }

        $db_data = get_option( 'ssbBlocks', [] );
        if ( ! is_array( $db_data ) ) {
            $db_data = [];
        }

        if ( ! isset( $_POST['data'] ) ) {
            wp_send_json_success( $db_data );
        }

        $data = json_decode( sanitize_text_field( wp_unslash( $_POST['data'] ) ), true );

        if ( ! is_array( $data ) ) {
            wp_send_json_error( 'Invalid Data' );
        }

        // Only ever store plain folder-name strings.
        $data = array_values( array_unique( array_map( 'sanitize_key', $data ) ) );

        update_option( 'ssbBlocks', $data );

        wp_send_json_success( $data );
    }

    // Persist the dashboard "delete data on uninstall" toggle.
    // Contract matches bpl-tools/Admin/Settings: reads $_POST['nonce'] and $_POST['enabled'].
    function ssbSaveUninstallOption(){
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ?? '' ) );

        if ( ! wp_verify_nonce( $nonce, 'ssb_save_uninstall_option' ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid security token.', 'services-section' ) ], 403 );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'You do not have permission to perform this action.', 'services-section' ) ], 403 );
        }

        $raw_enabled = isset( $_POST['enabled'] ) ? sanitize_text_field( wp_unslash( $_POST['enabled'] ) ) : '';
        $enabled     = ( 'true' === $raw_enabled || '1' === $raw_enabled );

        update_option( 'ssbDeleteDataOnUninstall', $enabled );

        wp_send_json_success( [
            'enabled' => $enabled,
            'message' => $enabled
                ? __( 'Data deletion enabled.', 'services-section' )
                : __( 'Data will be preserved on uninstall.', 'services-section' ),
        ] );
    }

    function ssbPremiumChecker(){
        $nonce = sanitize_text_field($_POST['_wpnonce'] ?? null);

        if (!wp_verify_nonce($nonce, 'wp_ajax')) {
            wp_send_json_error('Invalid Request');
        }

        wp_send_json_success([
            'isPipe' => ssbIsPremium()
        ]);
    }

    function registerSettings(){
        register_setting('ssbUtils', 'ssbUtils', [
            'show_in_rest' => [
                'name' => 'ssbUtils',
                'schema' => ['type' => 'string']
            ],
            'type' => 'string',
            'default' => wp_json_encode(['nonce' => wp_create_nonce('wp_ajax')]),
            'sanitize_callback' => 'sanitize_text_field'
        ]);
    }

}