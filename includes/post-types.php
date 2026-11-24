<?php
/**
 * Custom post types + taxonomies.
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register CPTs.
 */
function nydla_cmsp_register_post_types() {
	$common = array(
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'show_in_rest'        => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
		'menu_icon'           => 'dashicons-images-alt2',
	);

	register_post_type(
		'nydla_sponsor',
		array_merge(
			$common,
			array(
				'labels' => array(
					'name'          => __( 'Sponsors', 'nydla-cmsp' ),
					'singular_name' => __( 'Sponsor', 'nydla-cmsp' ),
					'add_new_item'  => __( 'Add Sponsor', 'nydla-cmsp' ),
					'edit_item'     => __( 'Edit Sponsor', 'nydla-cmsp' ),
					'search_items'  => __( 'Search Sponsors', 'nydla-cmsp' ),
				),
			)
		)
	);

	register_post_type(
		'nydla_event',
		array_merge(
			$common,
			array(
				'labels'   => array(
					'name'          => __( 'Events', 'nydla-cmsp' ),
					'singular_name' => __( 'Event', 'nydla-cmsp' ),
					'add_new_item'  => __( 'Add Event', 'nydla-cmsp' ),
					'edit_item'     => __( 'Edit Event', 'nydla-cmsp' ),
					'search_items'  => __( 'Search Events', 'nydla-cmsp' ),
				),
				'supports' => array( 'title', 'thumbnail', 'page-attributes', 'excerpt' ),
			)
		)
	);

	register_post_type(
		'nydla_podcast',
		array_merge(
			$common,
			array(
				'labels'   => array(
					'name'          => __( 'Podcasts', 'nydla-cmsp' ),
					'singular_name' => __( 'Podcast', 'nydla-cmsp' ),
					'add_new_item'  => __( 'Add Podcast', 'nydla-cmsp' ),
					'edit_item'     => __( 'Edit Podcast', 'nydla-cmsp' ),
					'search_items'  => __( 'Search Podcasts', 'nydla-cmsp' ),
				),
				'supports' => array( 'title', 'thumbnail', 'page-attributes', 'excerpt' ),
			)
		)
	);
}
add_action( 'init', 'nydla_cmsp_register_post_types' );

/**
 * Podcast categories.
 */
function nydla_cmsp_register_taxonomies() {
	register_taxonomy(
		'nydla_podcast_cat',
		'nydla_podcast',
		array(
			'labels'            => array(
				'name'          => __( 'Podcast Categories', 'nydla-cmsp' ),
				'singular_name' => __( 'Podcast Category', 'nydla-cmsp' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => false,
		)
	);
}
add_action( 'init', 'nydla_cmsp_register_taxonomies' );
