<?php

namespace SSB;

class CustomColumn {
    function __construct() {
        add_filter('manage_services_section_posts_columns', [$this, 'ssb_ManageColumns'], 10);
        add_action('manage_services_section_posts_custom_column', [$this, 'ssb_ManageCustomColumns'], 10, 2);  
    }

    function ssb_ManageColumns($defaults){
        unset($defaults['date']);
        $defaults['shortcode'] = 'ShortCode';
        $defaults['date'] = 'Date';
        return $defaults;
    }

    function ssb_ManageCustomColumns($column_name, $post_ID){
        if ($column_name == 'shortcode') {
            echo '<div class="bPlAdminShortcode" id="bPlAdminShortcode-' . esc_attr($post_ID) . '">
                    <input value="[services_section id=' . esc_attr($post_ID) . ']" onclick="copyBPlAdminShortcode(\'' . esc_attr($post_ID) . '\')" readonly>
                    <span class="tooltip">Copy To Clipboard</span>
                  </div>';
        }
    }

}