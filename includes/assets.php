<?php
/**
 * Front + admin assets.
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front CSS/JS when shortcode may render.
 */
function nydla_cmsp_enqueue_front() {
	if ( is_admin() ) {
		return;
	}
	wp_register_style(
		'nydla-cmsp',
		NYDLA_CMSP_URI . 'assets/css/frontend.css',
		array(),
		NYDLA_CMSP_VERSION
	);
	wp_register_script(
		'nydla-cmsp',
		NYDLA_CMSP_URI . 'assets/js/frontend.js',
		array(),
		NYDLA_CMSP_VERSION,
		true
	);
	wp_enqueue_style( 'nydla-cmsp' );
	wp_enqueue_script( 'nydla-cmsp' );
}
add_action( 'wp_enqueue_scripts', 'nydla_cmsp_enqueue_front' );
