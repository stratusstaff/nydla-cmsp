<?php
/**
 * One-time demo seed when CPTs are empty.
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seed demo content once.
 */
function nydla_cmsp_maybe_seed() {
	if ( get_option( 'nydla_cmsp_seeded_v1' ) ) {
		return;
	}
	if ( wp_count_posts( 'nydla_sponsor' )->publish > 0 ) {
		update_option( 'nydla_cmsp_seeded_v1', 1, false );
		return;
	}

	$sponsors = array( 'GoTo', 'Verizon', 'Microsoft', 'Zoom', 'Cisco', 'AWS', 'Google', 'IBM' );
	foreach ( $sponsors as $i => $name ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'nydla_sponsor',
				'post_title'  => $name,
				'post_status' => 'publish',
				'menu_order'  => $i + 1,
			),
			true
		);
		if ( ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_nydla_url', 'https://nydla.com/' );
			update_post_meta( $id, '_nydla_active', '1' );
		}
	}

	$event_id = wp_insert_post(
		array(
			'post_type'    => 'nydla_event',
			'post_title'   => 'NYDLA Spring Gathering',
			'post_excerpt' => 'Members, sponsors, and partners — conversations that move distance learning forward.',
			'post_status'  => 'publish',
			'menu_order'   => 1,
		),
		true
	);
	if ( ! is_wp_error( $event_id ) ) {
		update_post_meta( $event_id, '_nydla_url', home_url( '/events/' ) );
		update_post_meta( $event_id, '_nydla_date', 'Spring 2027' );
		update_post_meta( $event_id, '_nydla_venue', 'New York · Hybrid' );
		update_post_meta( $event_id, '_nydla_active', '1' );
	}

	$cats = array(
		'actors'         => 'Actors',
		'authors'        => 'Authors',
		'comedians'      => 'Comedians',
		'entrepreneurs'  => 'Entrepreneurs',
		'musicians'      => 'Musicians',
		'women-in-tech'  => 'Women in Tech',
	);
	$term_ids = array();
	foreach ( $cats as $slug => $label ) {
		$term = wp_insert_term( $label, 'nydla_podcast_cat', array( 'slug' => $slug ) );
		if ( ! is_wp_error( $term ) ) {
			$term_ids[ $slug ] = (int) $term['term_id'];
		} else {
			$existing = get_term_by( 'slug', $slug, 'nydla_podcast_cat' );
			if ( $existing ) {
				$term_ids[ $slug ] = (int) $existing->term_id;
			}
		}
	}

	$podcasts = array(
		array( 'Guest Conversation — Leadership in the Cloud', 'entrepreneurs', 'A NYDLA conversation on teaching and leading across time zones.' ),
		array( 'Guest Conversation — Story & Stage', 'actors', 'Distance learning meets performance and presence.' ),
		array( 'Guest Conversation — Women Building Tech', 'women-in-tech', 'Builders, mentors, and the networks that scale talent.' ),
	);
	foreach ( $podcasts as $i => $row ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'nydla_podcast',
				'post_title'   => $row[0],
				'post_excerpt' => $row[2],
				'post_status'  => 'publish',
				'menu_order'   => $i + 1,
			),
			true
		);
		if ( ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_nydla_url', home_url( '/podcasts/' ) );
			update_post_meta( $id, '_nydla_bio', $row[2] );
			update_post_meta( $id, '_nydla_active', '1' );
			if ( ! empty( $term_ids[ $row[1] ] ) ) {
				wp_set_object_terms( $id, array( $term_ids[ $row[1] ] ), 'nydla_podcast_cat' );
			}
		}
	}

	update_option( 'nydla_cmsp_seeded_v1', 1, false );
}
add_action( 'admin_init', 'nydla_cmsp_maybe_seed' );
