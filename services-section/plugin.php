<?php

/**
 * Plugin Name: Services Section - Block
 * Description: Use Services Section Block to provide services of your business to clients with customizable settings.
 * Version: 1.3.7
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
    register_activation_hook( __FILE__, function () {
        if ( is_plugin_active( 'services-section/plugin.php' ) ) {
            deactivate_plugins( 'services-section/plugin.php' );
        }
        if ( is_plugin_active( 'services-section-pro/plugin.php' ) ) {
            deactivate_plugins( 'services-section-pro/plugin.php' );
        }
    } );
} else {
    define( 'SSB_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.3.7' ) );
    define( 'SSB_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( 'SSB_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'SSB_HAS_FREE', 'services-section/plugin.php' === plugin_basename( __FILE__ ) );
    define( 'SSB_HAS_PRO', 'services-section-pro/plugin.php' === plugin_basename( __FILE__ ) );
    if ( !function_exists( 'ss_fs' ) ) {
        function ss_fs() {
            global $ss_fs;
            if ( !isset( $ss_fs ) ) {
                $fsStartPath = dirname( __FILE__ ) . '/freemius/start.php';
                $bSDKInitPath = dirname( __FILE__ ) . '/bplugins_sdk/init.php';
                if ( SSB_HAS_PRO && file_exists( $fsStartPath ) ) {
                    require_once $fsStartPath;
                } else {
                    if ( SSB_HAS_FREE && file_exists( $bSDKInitPath ) ) {
                        require_once $bSDKInitPath;
                    }
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
                        'first-path' => 'edit.php?post_type=services_section&page=ssb_demo_page',
                        'support'    => false,
                    ),
                ];
                $ss_fs = ( SSB_HAS_PRO && file_exists( $fsStartPath ) ? fs_dynamic_init( $ssbConfig ) : fs_lite_dynamic_init( $ssbConfig ) );
            }
            return $ss_fs;
        }

        ss_fs();
        do_action( 'ss_fs_loaded' );
    }
    function ssbIsPremium() {
        return ( SSB_HAS_PRO ? ss_fs()->can_use_premium_code() : false );
    }

    if ( !class_exists( 'SSBPlugin' ) ) {
        class SSBPlugin {
            function __construct() {
                add_action( 'init', [$this, 'onInit'] );
                add_action( 'enqueue_block_assets', [$this, 'ssb_enqueue_block_assets'] );
                add_action( 'admin_enqueue_scripts', [$this, 'ssb_admin_enqueue_script'] );
                add_action( 'init', [$this, 'ssb_register_post_type'] );
                add_shortcode( 'services_section', [$this, 'ssb_shortcode'] );
                add_filter( 'manage_services_section_posts_columns', [$this, 'ssb_ManageColumns'], 10 );
                add_action(
                    'manage_services_section_posts_custom_column',
                    [$this, 'ssb_ManageCustomColumns'],
                    10,
                    2
                );
                add_action( 'admin_menu', [$this, 'ssb_add_demo_submenu'] );
                add_action( 'wp_ajax_ssbPremiumChecker', [$this, 'ssbPremiumChecker'] );
                add_action( 'wp_ajax_nopriv_ssbPremiumChecker', [$this, 'ssbPremiumChecker'] );
                add_action( 'admin_init', [$this, 'registerSettings'] );
                add_action( 'rest_api_init', [$this, 'registerSettings'] );
            }

            function onInit() {
                register_block_type( __DIR__ . '/build/services' );
                register_block_type( __DIR__ . '/build/service' );
                wp_deregister_script( 'services-section-service-editor-script' );
            }

            function ssb_enqueue_block_assets() {
                wp_register_style(
                    'fontAwesome',
                    SSB_DIR_URL . 'public/css/font-awesome.min.css',
                    [],
                    '6.4.2'
                );
            }

            function ssb_admin_enqueue_script() {
                global $typenow;
                if ( 'services_section' === $typenow ) {
                    wp_enqueue_script(
                        'admin-post-js',
                        SSB_DIR_URL . 'build/admin-post.js',
                        [],
                        SSB_VERSION,
                        true
                    );
                    wp_enqueue_script(
                        'dashbaord-demo-js',
                        SSB_DIR_URL . 'build/demo.js',
                        [
                            'react',
                            'react-dom',
                            'wp-api',
                            'wp-util',
                            'wp-api-request'
                        ],
                        SSB_VERSION,
                        true
                    );
                    wp_enqueue_style(
                        'fontAwesome',
                        SSB_DIR_URL . 'public/css/font-awesome.min.css',
                        [],
                        '6.4.2'
                    );
                    wp_enqueue_style(
                        'services-view-css',
                        SSB_DIR_URL . 'build/services/view.css',
                        [],
                        SSB_VERSION
                    );
                    wp_enqueue_style(
                        'admin-post-css',
                        SSB_DIR_URL . 'build/admin-post.css',
                        [],
                        SSB_VERSION
                    );
                    wp_enqueue_style(
                        'dashbaord-demo-css',
                        SSB_DIR_URL . 'build/demo.css',
                        [],
                        SSB_VERSION
                    );
                }
            }

            function ssbPremiumChecker() {
                $nonce = sanitize_text_field( $_POST['_wpnonce'] ?? null );
                if ( !wp_verify_nonce( $nonce, 'wp_ajax' ) ) {
                    wp_send_json_error( 'Invalid Request' );
                }
                wp_send_json_success( [
                    'isPipe' => ssbIsPremium(),
                ] );
            }

            function registerSettings() {
                register_setting( 'ssbUtils', 'ssbUtils', [
                    'show_in_rest'      => [
                        'name'   => 'ssbUtils',
                        'schema' => [
                            'type' => 'string',
                        ],
                    ],
                    'type'              => 'string',
                    'default'           => wp_json_encode( [
                        'nonce' => wp_create_nonce( 'wp_ajax' ),
                    ] ),
                    'sanitize_callback' => 'sanitize_text_field',
                ] );
            }

            function ssb_register_post_type() {
                register_post_type( 'services_section', [
                    'label'              => 'Services Section',
                    'labels'             => [
                        'add_new'      => 'Add New',
                        'add_new_item' => 'Add New Service',
                        'edit_item'    => 'Edit Service',
                        'not_found'    => 'There was no Service please add one',
                    ],
                    'show_in_rest'       => true,
                    'public'             => true,
                    'publicly_queryable' => false,
                    'menu_icon'          => 'dashicons-portfolio',
                    'item_published'     => 'Services Section Published',
                    'item_updated'       => 'Services Section Updated',
                    'template'           => [['services-section/services']],
                    'template_lock'      => 'all',
                ] );
            }

            function ssb_add_demo_submenu() {
                add_submenu_page(
                    'edit.php?post_type=services_section',
                    'Demo & Help',
                    'Demo & Help',
                    'manage_options',
                    'ssb_demo_page',
                    [$this, 'ssb_render_demo_page']
                );
            }

            function renderTemplate( $content ) {
                $parseBlocks = parse_blocks( $content );
                return render_block( $parseBlocks[0] );
            }

            function ssb_render_demo_page() {
                $dashboardData = [
                    'version'   => SSB_VERSION,
                    'logo'      => 'https://ps.w.org/services-section/assets/icon-128x128.png?rev=2579643',
                    'isPremium' => ssbIsPremium(),
                ];
                ?>
				
				<style>
					#wpfooter {
						position: relative;
					}
				</style>
	
				<div id="customAdminDashboard" data-dashboard="<?php 
                echo esc_attr( wp_json_encode( $dashboardData ) );
                ?>">
					<div class="renderDashboard"></div>
						<div class="templates" style="display:none;">
								<div class="default">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services -->
									<!-- wp:services-section/service /-->
                                    <!-- wp:services-section/service /-->
                                    <!-- wp:services-section/service /-->
                                    <!-- /wp:services-section/services -->' );
                ?>
								</div>
								<div class="layout1">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services {"layout":"layout1","columnGap":"20px","background":{"color":"#121212","type":"solid"},"itemBorder":{"radius":"15px","width":"0px","style":"solid","color":"#0000","side":"all"},"itemShadow":{"blur":"20px","color":"#0000001a","hOffset":0,"vOffset":0,"spreed":0},"itemHovShadow":{"hOffset":0,"vOffset":0,"blur":"20px","spreed":0,"color":"#0000001a"},"titleTypo":{"fontSize":{"desktop":23,"tablet":20,"mobile":18},"fontWeight":400}} -->
									<!-- wp:services-section/service {"background":{"color":"#0000","type":"solid"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#0000"},"icon":{"class":"fa-brands fa-wordpress","fontSize":70,"colorType":"gradient","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffd800"},"iconHovColor":"#7f246b","iconBorder":{"width":"1px","color":"#777","side":"bottom","radius":"0px"},"titleColor":"#fff","titleHovColor":"#fff","descColor":"#777","descHovColor":"#777","linkIn":"title"} /-->
	
									<!-- wp:services-section/service {"background":{"color":"#0000","type":"solid"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#0000"},"icon":{"class":"fa-brands fa-wordpress","fontSize":70,"colorType":"gradient","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffd800"},"iconHovColor":"#7f246b","iconBorder":{"width":"1px","color":"#777","side":"bottom","radius":"0px"},"titleColor":"#fff","titleHovColor":"#fff","descColor":"#777","descHovColor":"#777","linkIn":"title"} /-->
	
									<!-- wp:services-section/service {"background":{"color":"#0000","type":"solid"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#0000"},"icon":{"class":"fa-brands fa-wordpress","fontSize":70,"colorType":"gradient","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffd800"},"iconHovColor":"#7f246b","iconBorder":{"width":"1px","color":"#777","side":"bottom","radius":"0px"},"titleColor":"#fff","titleHovColor":"#fff","descColor":"#777","descHovColor":"#777","linkIn":"title"} /-->
									<!-- /wp:services-section/services -->' );
                ?>
								</div>
								<div class="layout2">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services {"layout":"layout2","columnGap":"20px","background":{"color":"#0000","type":"solid"},"textAlign":"center","itemBorder":{"radius":"15px","width":"0px","style":"solid","color":"#0000","side":"all"},"itemShadow":{"blur":0,"color":"#0000","hOffset":0,"vOffset":0,"spreed":0},"itemHovShadow":{"hOffset":0,"vOffset":0,"blur":0,"spreed":0,"color":"#0000"},"titleTypo":{"fontSize":{"desktop":23,"tablet":20,"mobile":18},"fontWeight":400}} -->
									<!-- wp:services-section/service {"background":{"color":"#191229","type":"solid"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#212824"},"icon":{"class":"fa-solid fa-coins","fontSize":70,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffd800"},"iconHovColor":"#76C410","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"0px"},"titleColor":"#0000","titleHovColor":"#fff","descColor":"#0000","descHovColor":"#fff","linkIn":"service"} /-->
	
									<!-- wp:services-section/service {"background":{"color":"#191229","type":"solid"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#212824"},"icon":{"class":"fa-solid fa-coins","fontSize":70,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffd800"},"iconHovColor":"#76C410","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"0px"},"titleColor":"#0000","titleHovColor":"#fff","descColor":"#0000","descHovColor":"#fff","linkIn":"service"} /-->
	
									<!-- wp:services-section/service {"background":{"color":"#191229","type":"solid"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#212824"},"icon":{"class":"fa-solid fa-coins","fontSize":70,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffd800"},"iconHovColor":"#76C410","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"0px"},"titleColor":"#0000","titleHovColor":"#fff","descColor":"#0000","descHovColor":"#fff","linkIn":"service"} /-->
									<!-- /wp:services-section/services -->' );
                ?>
								</div>
								<div class="layout3">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services {"layout":"layout3","background":{"color":"#0000","type":"solid"},"itemBorder":{"radius":"15px","width":"0px","style":"solid","color":"#0000","side":"all"},"itemShadow":{"blur":0,"color":"#0000","hOffset":0,"vOffset":0,"spreed":0},"itemHovShadow":{"hOffset":0,"vOffset":0,"blur":0,"spreed":0,"color":"#0000"},"titleTypo":{"fontSize":{"desktop":23,"tablet":20,"mobile":18},"fontWeight":700}} -->
                                    <!-- wp:services-section/service {"background":{"color":"#F7F2F296","type":"solid"},"icon":{"class":"fas fa-laptop-code","fontSize":30,"colorType":"solid","color":"#fff"},"iconHovColor":"#ff4500","iconBgColor":"#ff4500","iconBgHovColor":"#ffff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"5px"},"titleHovColor":"#fff","descHovColor":"#fff","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
                                    
                                    <!-- wp:services-section/service {"background":{"color":"#F7F2F296","type":"solid"},"icon":{"class":"fas fa-laptop-code","fontSize":30,"colorType":"solid","color":"#fff"},"iconHovColor":"#ff4500","iconBgColor":"#ff4500","iconBgHovColor":"#ffff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"5px"},"titleHovColor":"#fff","descHovColor":"#fff","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
                                    
                                    <!-- wp:services-section/service {"background":{"color":"#F7F2F296","type":"solid"},"icon":{"class":"fas fa-laptop-code","fontSize":30,"colorType":"solid","color":"#fff"},"iconHovColor":"#ff4500","iconBgColor":"#ff4500","iconBgHovColor":"#ffff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"5px"},"titleHovColor":"#fff","descHovColor":"#fff","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
                                    <!-- /wp:services-section/services -->' );
                ?>
								</div>
								<div class="layout4">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services {"layout":"layout4","columnGap":"20px","background":{"color":"#0000","type":"solid"},"itemBorder":{"radius":"0px","width":"5px","style":"solid","color":"#37473A","side":"left"},"itemShadow":{"blur":"10px","color":"#cccccc","hOffset":0,"vOffset":0,"spreed":0},"itemHovShadow":{"hOffset":0,"vOffset":0,"blur":"10px","spreed":0,"color":"#cccccc"},"iconPadding":{"side":4,"bottom":"0px"},"titleTypo":{"fontSize":{"desktop":23,"tablet":20,"mobile":18},"fontWeight":600}} -->
									<!-- wp:services-section/service {"background":{"color":"#ffffff","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#212824"},"icon":{"class":"fa-solid fa-cubes","fontSize":40,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffffff"},"iconHovColor":"#37473A","iconBgColor":"#37473A","iconBgHovColor":"#ffffff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"50%"},"titleHovColor":"#ffffff","descHovColor":"#ffffff","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
	
									<!-- wp:services-section/service {"background":{"color":"#ffffff","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#212824"},"icon":{"class":"fa-solid fa-cubes","fontSize":40,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffffff"},"iconHovColor":"#37473A","iconBgColor":"#37473A","iconBgHovColor":"#ffffff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"50%"},"titleHovColor":"#ffffff","descHovColor":"#ffffff","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
	
									<!-- wp:services-section/service {"background":{"color":"#ffffff","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#212824"},"icon":{"class":"fa-solid fa-cubes","fontSize":40,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#ffffff"},"iconHovColor":"#37473A","iconBgColor":"#37473A","iconBgHovColor":"#ffffff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"50%"},"titleHovColor":"#ffffff","descHovColor":"#ffffff","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
									<!-- /wp:services-section/services -->' );
                ?>
								</div>
								<div class="layout5">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services {"layout":"layout5","background":{"color":"#0000","type":"solid"},"textAlign":"center","itemBorder":{"radius":"150px","width":"0px","style":"solid","color":"#37473A","side":"all"},"itemShadow":{"blur":"7px","color":"rgba(0, 0, 0, 0.2)","hOffset":0,"vOffset":"12px","spreed":"-7px"},"iconPadding":{"side":4,"bottom":"0px"},"titleTypo":{"fontSize":{"desktop":23,"tablet":20,"mobile":18},"fontWeight":600}} -->
                                    <!-- wp:services-section/service {"background":{"color":"#0000","type":"gradient","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"icon":{"class":"fa-brands fa-java","fontSize":50,"colorType":"solid","color":"#fff"},"iconHovColor":"#fff","iconBgColor":"#fa5b0f","iconBgHovColor":"#fa5b0f","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":""},"titleColor":"#fff","titleHovColor":"#fff","descColor":"#fff","descHovColor":"#fff","linkIn":"title"} /-->
                                    
                                    <!-- wp:services-section/service {"background":{"color":"#0000","type":"gradient","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"icon":{"class":"fa-brands fa-java","fontSize":50,"colorType":"solid","color":"#fff"},"iconHovColor":"#fff","iconBgColor":"#fa5b0f","iconBgHovColor":"#fa5b0f","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":""},"titleColor":"#fff","titleHovColor":"#fff","descColor":"#fff","descHovColor":"#fff","linkIn":"title"} /-->
                                    
                                    <!-- wp:services-section/service {"background":{"color":"#0000","type":"gradient","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"icon":{"class":"fa-brands fa-java","fontSize":50,"colorType":"solid","color":"#fff"},"iconHovColor":"#fff","iconBgColor":"#fa5b0f","iconBgHovColor":"#fa5b0f","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":""},"titleColor":"#fff","titleHovColor":"#fff","descColor":"#fff","descHovColor":"#fff","linkIn":"title"} /-->
                                    <!-- /wp:services-section/services -->' );
                ?>
								</div>
								<div class="layout6">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services {"layout":"layout6","columnGap":"20px","background":{"color":"#0000","type":"solid"},"textAlign":"center","itemBorder":{"radius":"20px","width":"0px","style":"solid","color":"#37473A","side":"all"},"itemShadow":{"blur":0,"color":"#0000","hOffset":0,"vOffset":0,"spreed":0},"itemHovShadow":{"hOffset":0,"vOffset":0,"blur":0,"spreed":0,"color":"#0000"},"iconPadding":{"side":4,"bottom":"0px"},"titleTypo":{"fontSize":{"desktop":23,"tablet":20,"mobile":18},"fontWeight":700}} -->
									<!-- wp:services-section/service {"background":{"color":"#249eff","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#249eff"},"icon":{"class":"fa-solid fa-charging-station","fontSize":60,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#249eff"},"iconHovColor":"#fff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":""},"titleColor":"#ffffff","titleHovColor":"#ffffff","descColor":"#f6f6f6","descHovColor":"#f6f6f6","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"title"} /-->
	
									<!-- wp:services-section/service {"background":{"color":"#249eff","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#249eff"},"icon":{"class":"fa-solid fa-charging-station","fontSize":60,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#249eff"},"iconHovColor":"#fff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":""},"titleColor":"#ffffff","titleHovColor":"#ffffff","descColor":"#f6f6f6","descHovColor":"#f6f6f6","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"title"} /-->
	
									<!-- wp:services-section/service {"background":{"color":"#249eff","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#249eff"},"icon":{"class":"fa-solid fa-charging-station","fontSize":60,"colorType":"solid","gradient":"linear-gradient(135deg, #7f246b, #c70d2b)","color":"#249eff"},"iconHovColor":"#fff","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":""},"titleColor":"#ffffff","titleHovColor":"#ffffff","descColor":"#f6f6f6","descHovColor":"#f6f6f6","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"title"} /-->
									<!-- /wp:services-section/services -->' );
                ?>
								</div>
								<div class="layout7">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services {"layout":"layout7","background":{"color":"#0000","type":"solid"},"textAlign":"center","itemBorder":{"radius":"10px","width":"0px","style":"solid","color":"#37473A","side":"all"},"itemShadow":{"blur":"8px","color":"rgba(0, 0, 0, 0.1)","hOffset":0,"vOffset":"4px","spreed":0},"itemHovShadow":{"hOffset":0,"vOffset":"8px","blur":"16px","spreed":0,"color":"rgba(0, 0, 0, 0.2)"},"iconPadding":{"side":4,"bottom":"0px"},"titleTypo":{"fontSize":{"desktop":23,"tablet":20,"mobile":18},"fontWeight":600}} -->
                                    <!-- wp:services-section/service {"background":{"color":"#9CD6A661","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#b9d1bd61"},"icon":{"class":"fa-brands fa-chrome","fontSize":50,"colorType":"solid","color":"white"},"iconHovColor":"white","iconBgColor":"#5DBB6DA6","iconBgHovColor":"#7EA384F2","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"10px"},"titleColor":"#555","titleHovColor":"#555","descColor":"#555","descHovColor":"#555","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
                                    
                                    <!-- wp:services-section/service {"background":{"color":"#9CD6A661","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#b9d1bd61"},"icon":{"class":"fa-brands fa-chrome","fontSize":50,"colorType":"solid","color":"white"},"iconHovColor":"white","iconBgColor":"#5DBB6DA6","iconBgHovColor":"#7EA384F2","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"10px"},"titleColor":"#555","titleHovColor":"#555","descColor":"#555","descHovColor":"#555","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
                                    
                                    <!-- wp:services-section/service {"background":{"color":"#9CD6A661","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#b9d1bd61"},"icon":{"class":"fa-brands fa-chrome","fontSize":50,"colorType":"solid","color":"white"},"iconHovColor":"white","iconBgColor":"#5DBB6DA6","iconBgHovColor":"#7EA384F2","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"10px"},"titleColor":"#555","titleHovColor":"#555","descColor":"#555","descHovColor":"#555","circleBefore":{"width":"150px","height":"150px","bg":"#ff4500","opacity":0.5,"hovOpacity":1,"border":{"width":"6px","style":"solid","color":"#504f93","radius":"50%"}},"linkIn":"service"} /-->
                                    <!-- /wp:services-section/services -->' );
                ?>
								</div>
								<div class="layout8">
									<?php 
                echo $this->renderTemplate( '<!-- wp:services-section/services {"layout":"layout8","background":{"color":"#0000","type":"solid"},"itemBorder":{"radius":"8px","width":"0px","style":"solid","color":"#37473A","side":"all"},"itemShadow":{"blur":"15px","color":"rgba(0, 0, 0, 0.05)","hOffset":0,"vOffset":"5px","spreed":0},"itemHovShadow":{"hOffset":0,"vOffset":"15px","blur":"30px","spreed":0,"color":"rgba(0, 0, 0, 0.1)"},"iconPadding":{"side":4,"bottom":"0px"},"titleTypo":{"fontSize":{"desktop":23,"tablet":20,"mobile":18},"fontWeight":600}} -->
                                    <!-- wp:services-section/service {"background":{"color":"rgba(183, 192, 180, 0.45)","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#0000"},"icon":{"class":"fa-solid fa-bag-shopping","fontSize":50,"colorType":"solid","color":"#21759b"},"iconHovColor":"white","iconBgColor":"#f0f9ff","iconBgHovColor":"#21759b","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"50%"},"titleColor":"#23282d","titleHovColor":"#21759b","descColor":"#646970","descHovColor":"#646970","linkIn":"service"} /-->
                                    
                                    <!-- wp:services-section/service {"background":{"color":"rgba(183, 192, 180, 0.45)","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#0000"},"icon":{"class":"fa-solid fa-bag-shopping","fontSize":50,"colorType":"solid","color":"#21759b"},"iconHovColor":"white","iconBgColor":"#f0f9ff","iconBgHovColor":"#21759b","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"50%"},"titleColor":"#23282d","titleHovColor":"#21759b","descColor":"#646970","descHovColor":"#646970","linkIn":"service"} /-->
                                    
                                    <!-- wp:services-section/service {"background":{"color":"rgba(183, 192, 180, 0.45)","type":"solid","gradient":"linear-gradient(transparent 65%, #fa5b0f 65%)"},"hoverBG":{"type":"solid","image":{"url":"https://i.imgur.com/i6qCSdM.png"},"overlayColor":"#0000","position":"bottom left","size":"auto","repeat":"no-repeat","color":"#0000"},"icon":{"class":"fa-solid fa-bag-shopping","fontSize":50,"colorType":"solid","color":"#21759b"},"iconHovColor":"white","iconBgColor":"#f0f9ff","iconBgHovColor":"#21759b","iconBorder":{"width":"0px","color":"#0000","side":"all","radius":"50%"},"titleColor":"#23282d","titleHovColor":"#21759b","descColor":"#646970","descHovColor":"#646970","linkIn":"service"} /-->
                                    <!-- /wp:services-section/services -->' );
                ?>
								</div>
						</div>
					</div>
				</div>
	
				<?php 
            }

            function ssb_shortcode( $atts ) {
                $post_id = $atts['id'];
                $post = get_post( $post_id );
                if ( !$post ) {
                    return '';
                }
                if ( post_password_required( $post ) ) {
                    return get_the_password_form( $post );
                }
                switch ( $post->post_status ) {
                    case 'publish':
                        return $this->displayContent( $post );
                    case 'private':
                        if ( current_user_can( 'read_private_posts' ) ) {
                            return $this->displayContent( $post );
                        }
                        return '';
                    case 'draft':
                    case 'pending':
                    case 'future':
                        if ( current_user_can( 'edit_post', $post_id ) ) {
                            return $this->displayContent( $post );
                        }
                        return '';
                    default:
                        return '';
                }
            }

            function displayContent( $post ) {
                $blocks = parse_blocks( $post->post_content );
                return render_block( $blocks[0] );
            }

            function ssb_ManageColumns( $defaults ) {
                unset($defaults['date']);
                $defaults['shortcode'] = 'ShortCode';
                $defaults['date'] = 'Date';
                return $defaults;
            }

            function ssb_ManageCustomColumns( $column_name, $post_ID ) {
                if ( $column_name == 'shortcode' ) {
                    echo '<div class="bPlAdminShortcode" id="bPlAdminShortcode-' . esc_attr( $post_ID ) . '">
							<input value="[services_section id=' . esc_attr( $post_ID ) . ']" onclick="copyBPlAdminShortcode(\'' . esc_attr( $post_ID ) . '\')" readonly>
							<span class="tooltip">Copy To Clipboard</span>
						  </div>';
                }
            }

        }

        new SSBPlugin();
    }
}