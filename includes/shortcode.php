<?php
/**
 * [NYDLA-CMSP] shortcode.
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode callback.
 *
 * @param array|string $atts Attributes.
 * @return string
 */
function nydla_cmsp_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'type'     => 'sponsors',
			'layout'   => '',
			'category' => '',
			'limit'    => 0,
		),
		$atts,
		'NYDLA-CMSP'
	);

	$type = sanitize_key( $atts['type'] );
	if ( 'partners' === $type ) {
		$type = 'sponsors';
	}
	if ( ! in_array( $type, array( 'sponsors', 'events', 'podcasts' ), true ) ) {
		return '';
	}

	$args = array();
	if ( (int) $atts['limit'] > 0 ) {
		$args['posts_per_page'] = (int) $atts['limit'];
	}
	if ( $atts['category'] ) {
		$args['category'] = sanitize_title( $atts['category'] );
	}

	// URL filter stub for podcasts: ?cat=authors
	if ( 'podcasts' === $type && empty( $atts['category'] ) && isset( $_GET['cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$args['category'] = sanitize_title( wp_unslash( $_GET['cat'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	$posts = nydla_cmsp_get_items( $type, $args );
	$items = array_map( 'nydla_cmsp_item_data', $posts );

	$layout = sanitize_key( $atts['layout'] );
	if ( ! $layout ) {
		$layout = ( 'sponsors' === $type ) ? 'grid' : $type;
	}

	ob_start();
	$template = NYDLA_CMSP_DIR . 'templates/' . $type . '.php';
	if ( file_exists( $template ) ) {
		include $template;
	}
	return (string) ob_get_clean();
}
add_shortcode( 'NYDLA-CMSP', 'nydla_cmsp_shortcode' );
