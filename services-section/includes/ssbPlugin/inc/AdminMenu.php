<?php

namespace SSB;

class AdminMenu  {
    function __construct() {
        add_action('admin_menu', [$this, 'ssb_add_demo_submenu']);
        add_action('admin_head', [$this, 'ppb_admin_menu_color']);
    }

    function ssb_add_demo_submenu(){
        add_submenu_page(
            'edit.php?post_type=services_section',
            'Help & Demos',
            'Help & Demos',
            'manage_options',
            'ssb_demo_page',
            [$this, 'ssb_render_demo_page']
        );
    }

    function ssb_render_demo_page(){
        ?>
            <div
                id='ssbCurrentBplDashboard'
                data-info='<?php echo esc_attr( wp_json_encode( [
                    'version' => SSB_VERSION,
                    'isPremium' => ssbIsPremium(),
                    'hasPro' => SSB_HAS_FRMS,
                    'licenseActiveNonce' => wp_create_nonce( 'bPlLicenseActivation' )
                ] ) ); ?>'
            ></div>
        <?php
    }

    function ppb_admin_menu_color() {
        ?>
        <style>
            #adminmenu a[href="edit.php?post_type=services_section&page=ssb_demo_page"] {
                color: #f18500 !important; 
                font-weight: 600 !important;
            }
        </style>
        <?php
    }

}