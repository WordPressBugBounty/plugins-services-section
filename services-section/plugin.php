<?php

/**
 * Plugin Name: Services Section - Block
 * Description: Use Services Section Block to provide services of your business to clients with customizable settings.
 * Version: 1.4.2
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: services-section
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'ss_fs' ) ) {
    ss_fs()->set_basename( false, __FILE__ );
} else {
    define( 'SSB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.4.2' ) );
    define( 'SSB_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( 'SSB_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'SSB_HAS_FRMS', file_exists( dirname( __FILE__ ) . '/freemius/start.php' ) );
    if ( !function_exists( 'ss_fs' ) ) {
        function ss_fs() {
            global $ss_fs;
            if ( !isset( $ss_fs ) ) {
                if ( SSB_HAS_FRMS ) {
                    require_once SSB_DIR_PATH . '/freemius/start.php';
                } else {
                    require_once SSB_DIR_PATH . '/freemius-lite/start.php';
                }
                $ssbConfig = [
                    'id'                  => '18628',
                    'slug'                => 'services-section',
                    'premium_slug'        => 'services-section-pro',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_e9793d569da544eb6078cf6a751f1',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'trial'               => array(
                        'days'               => 7,
                        'is_require_payment' => true,
                    ),
                    'menu'                => array(
                        'slug'       => 'edit.php?post_type=services_section',
                        'first-path' => 'edit.php?post_type=services_section&page=ssb_demo_page#/welcome',
                        'support'    => false,
                    ),
                ];
                $ss_fs = ( SSB_HAS_FRMS ? fs_dynamic_init( $ssbConfig ) : fs_lite_dynamic_init( $ssbConfig ) );
            }
            return $ss_fs;
        }

        ss_fs();
        do_action( 'ss_fs_loaded' );
    }
    require_once SSB_DIR_PATH . 'includes/utility/functions.php';
    require_once SSB_DIR_PATH . 'includes/ssbPlugin/plugin.php';
}