<?php

namespace SSB;

class AdminMenu  {
    function __construct() {
        add_action('admin_menu', [$this, 'ssb_add_demo_submenu']);
    }

    function ssb_add_demo_submenu(){
        add_submenu_page(
            'edit.php?post_type=services_section',
            'Help & Demos',
            '<span style="color: #f18500; font-weight: 600;">Help & Demos</span>', 
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

}