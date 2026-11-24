<?php
/**
 * Plugin Name: NYDLA CMSP
 * Plugin URI: https://bezledastudios.com/
 * Description: Content-management shortcodes for NYDLA — sponsors logos, event banners, and podcast tiles. Admin-managed; front end via [NYDLA-CMSP].
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: Bez Leda Studios
 * Author URI: https://bezledastudios.com/
 * License: GPL-2.0-or-later
 * Text Domain: nydla-cmsp
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NYDLA_CMSP_VERSION', '1.0.0' );
define( 'NYDLA_CMSP_FILE', __FILE__ );
define( 'NYDLA_CMSP_DIR', plugin_dir_path( __FILE__ ) );
define( 'NYDLA_CMSP_URI', plugin_dir_url( __FILE__ ) );

require_once NYDLA_CMSP_DIR . 'includes/post-types.php';
require_once NYDLA_CMSP_DIR . 'includes/meta.php';
require_once NYDLA_CMSP_DIR . 'includes/query.php';
require_once NYDLA_CMSP_DIR . 'includes/shortcode.php';
require_once NYDLA_CMSP_DIR . 'includes/assets.php';
require_once NYDLA_CMSP_DIR . 'includes/admin.php';
require_once NYDLA_CMSP_DIR . 'includes/seed.php';

/**
 * Activation: register types, flush rewrite rules.
 */
function nydla_cmsp_activate() {
	nydla_cmsp_register_post_types();
	nydla_cmsp_register_taxonomies();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'nydla_cmsp_activate' );

/**
 * Deactivation: flush rewrite rules.
 */
function nydla_cmsp_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'nydla_cmsp_deactivate' );
