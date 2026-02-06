<?php

namespace SSB;

class Enqueue {
    function __construct() {
        add_action( 'enqueue_block_assets', [$this, 'ssb_enqueue_block_assets'] );
        add_action( 'admin_enqueue_scripts', [$this, 'ssb_admin_enqueue_script']);
    }

    function ssb_enqueue_block_assets(){
        wp_register_style( 'fontAwesome', SSB_DIR_URL . 'includes/assets/css/font-awesome.min.css', [], '6.4.2' );
    }

    function ssb_admin_enqueue_script($screen){
        global $typenow;
        
        if ('services_section' === $typenow) {
            wp_enqueue_style( 'fontAwesome', SSB_DIR_URL . 'includes/assets/css/font-awesome.min.css', [], '6.4.2' );

            wp_enqueue_style( 'services-view-css', SSB_DIR_URL . 'build/services/view.css', [], SSB_VERSION );

            wp_enqueue_script( 'admin-post-js', SSB_DIR_URL . 'build/admin-post.js', [], SSB_VERSION, true );
            wp_enqueue_style( 'admin-post-css', SSB_DIR_URL . 'build/admin-post.css', [], SSB_VERSION );

            if ($screen === "services_section_page_ssb_demo_page") {
                wp_enqueue_script( 'bpl-admin-dashboard-js', SSB_DIR_URL . 'build/admin-dashboard.js', [ 'react', 'react-dom' ], SSB_VERSION, true );
                wp_enqueue_style( 'bpl-admin-dashboard-css', SSB_DIR_URL . 'build/admin-dashboard.css', [], SSB_VERSION );
            }

        }
    }
}