<?php

namespace SSB;

class Init {
    function __construct() {
        add_action( 'init', [ $this, 'onInit' ] );    
    }

    function onInit(){
        register_block_type( SSB_DIR_PATH  . '/build/services' );
        register_block_type( SSB_DIR_PATH  . '/build/service' );
        wp_deregister_script( 'services-section-service-editor-script' );

        register_post_type('services_section', [
            'label' => 'Services Section',
            'labels' => [
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Service',
                'edit_item' => 'Edit Service',
                'not_found' => 'There was no Service please add one'
            ],
            'show_in_rest' => true,
            'public' => true,
            'publicly_queryable' => false,
            'menu_icon' => 'dashicons-portfolio',
            'item_published' => 'Services Section Published',
            'item_updated' => 'Services Section Updated',
            'template' => [['services-section/services']]
        ]);
    }

}