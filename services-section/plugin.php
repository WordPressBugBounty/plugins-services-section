<?php
/**
 * Plugin Name: Services Section - Block
 * Description: Use Services Section Block to provide services of your business to clients with customizable settings.
 * Version: 1.3.3
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: services-section
 */

// ABS PATH
if ( !defined( 'ABSPATH' ) ) { exit; }

// Constant
define( 'SSB_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.3.3' );
define( 'SSB_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'SSB_DIR_PATH', plugin_dir_path( __FILE__ ) );

require_once SSB_DIR_PATH . 'inc/block.php';
require_once SSB_DIR_PATH . 'inc/child/block.php';