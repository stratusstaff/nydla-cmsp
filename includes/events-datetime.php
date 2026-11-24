<?php
/**
 * Event calendar datetime helpers + one-time migration.
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize submitted / legacy value to site-local MySQL datetime or empty.
 *
 * @param string $raw Raw input.
 * @return string Y-m-d H:i:s
 */
function nydla_cmsp_normalize_event_datetime( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}

	// datetime-local: 2027-03-12T18:00
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $raw ) ) {
		$raw = str_replace( 'T', ' ', substr( $raw, 0, 16 ) ) . ':00';
	}

	$tz = wp_timezone();
	$dt = date_create( $raw, $tz );
	if ( ! $dt ) {
		$ts = strtotime( $raw );
		if ( false === $ts ) {
			return '';
		}
		$dt = ( new DateTimeImmutable( '@' . $ts ) )->setTimezone( $tz );
	}

	return $dt->format( 'Y-m-d H:i:s' );
}

/**
 * Format stored datetime for display.
 *
 * @param string $mysql Y-m-d H:i:s.
 * @return string
 */
function nydla_cmsp_format_event_datetime( $mysql ) {
	$dt = date_create( (string) $mysql, wp_timezone() );
	if ( ! $dt ) {
		return '';
	}
	return wp_date( 'F j, Y · g:i A', $dt->getTimestamp() );
}

/**
 * Value for input[type=datetime-local].
 *
 * @param int $post_id Post ID.
 * @return string
 */
function nydla_cmsp_get_event_datetime_local_value( $post_id ) {
	$mysql = (string) get_post_meta( $post_id, '_nydla_datetime', true );
	if ( ! $mysql ) {
		$mysql = nydla_cmsp_normalize_event_datetime( (string) get_post_meta( $post_id, '_nydla_date', true ) );
	}
	if ( ! $mysql ) {
		return '';
	}
	$dt = date_create( $mysql, wp_timezone() );
	if ( ! $dt ) {
		return '';
	}
	return $dt->format( 'Y-m-d\TH:i' );
}

/**
 * Unix timestamp (UTC) for comparison, or 0.
 *
 * @param int|WP_Post $post Post.
 * @return int
 */
function nydla_cmsp_event_timestamp( $post ) {
	$post_id = is_object( $post ) ? (int) $post->ID : (int) $post;
	$mysql   = (string) get_post_meta( $post_id, '_nydla_datetime', true );
	if ( ! $mysql ) {
		$mysql = nydla_cmsp_normalize_event_datetime( (string) get_post_meta( $post_id, '_nydla_date', true ) );
	}
	if ( ! $mysql ) {
		return 0;
	}
	$dt = date_create( $mysql, wp_timezone() );
	return $dt ? $dt->getTimestamp() : 0;
}

/**
 * Expose CMSP meta on REST for bulk/API updates.
 */
function nydla_cmsp_register_rest_meta() {
	$common = array(
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'auth_callback'     => static function () {
			return current_user_can( 'edit_posts' );
		},
		'sanitize_callback' => 'sanitize_text_field',
	);

	foreach ( array( 'nydla_sponsor', 'nydla_event', 'nydla_podcast' ) as $type ) {
		register_post_meta( $type, '_nydla_url', array_merge( $common, array( 'sanitize_callback' => 'esc_url_raw' ) ) );
		register_post_meta( $type, '_nydla_active', $common );
	}

	register_post_meta( 'nydla_sponsor', '_nydla_page_url', array_merge( $common, array( 'sanitize_callback' => 'esc_url_raw' ) ) );
	register_post_meta( 'nydla_event', '_nydla_datetime', $common );
	register_post_meta( 'nydla_event', '_nydla_date', $common );
	register_post_meta( 'nydla_event', '_nydla_venue', $common );
	register_post_meta(
		'nydla_podcast',
		'_nydla_bio',
		array_merge(
			$common,
			array( 'sanitize_callback' => 'sanitize_textarea_field' )
		)
	);
}
add_action( 'init', 'nydla_cmsp_register_rest_meta', 25 );

/**
 * Migrate free-text event dates into _nydla_datetime once.
 */
function nydla_cmsp_migrate_event_datetimes() {
	if ( get_option( 'nydla_cmsp_event_datetime_migrated' ) ) {
		return;
	}

	$q = new WP_Query(
		array(
			'post_type'      => 'nydla_event',
			'post_status'    => 'any',
			'posts_per_page' => 500,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	foreach ( $q->posts as $post_id ) {
		$existing = nydla_cmsp_normalize_event_datetime( (string) get_post_meta( $post_id, '_nydla_datetime', true ) );
		if ( $existing ) {
			update_post_meta( $post_id, '_nydla_datetime', $existing );
			update_post_meta( $post_id, '_nydla_date', nydla_cmsp_format_event_datetime( $existing ) );
			continue;
		}

		$legacy = (string) get_post_meta( $post_id, '_nydla_date', true );
		$mysql  = nydla_cmsp_normalize_event_datetime( $legacy );
		if ( ! $mysql ) {
			$title = get_the_title( $post_id );
			if ( false !== stripos( $title, 'Spring' ) ) {
				$mysql = '2027-03-15 18:00:00';
			} else {
				continue;
			}
		}
		update_post_meta( $post_id, '_nydla_datetime', $mysql );
		update_post_meta( $post_id, '_nydla_date', nydla_cmsp_format_event_datetime( $mysql ) );
	}

	update_option( 'nydla_cmsp_event_datetime_migrated', 1, false );
}
add_action( 'admin_init', 'nydla_cmsp_migrate_event_datetimes' );
add_action( 'init', 'nydla_cmsp_migrate_event_datetimes', 30 );

/**
 * Ensure at least one past demo event exists for the Past Events section.
 */
function nydla_cmsp_ensure_past_demo_event() {
	if ( get_option( 'nydla_cmsp_past_demo_v1' ) ) {
		return;
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'nydla_event',
			'post_title'   => 'NYDLA Winter Meetup 2024',
			'post_excerpt' => 'A past gathering for members and sponsors.',
			'post_status'  => 'publish',
			'menu_order'   => 20,
		),
		true
	);
	if ( ! is_wp_error( $id ) ) {
		$mysql = '2024-01-18 17:00:00';
		update_post_meta( $id, '_nydla_url', home_url( '/events/' ) );
		update_post_meta( $id, '_nydla_datetime', $mysql );
		update_post_meta( $id, '_nydla_date', nydla_cmsp_format_event_datetime( $mysql ) );
		update_post_meta( $id, '_nydla_venue', 'Online' );
		update_post_meta( $id, '_nydla_active', '1' );
	}

	update_option( 'nydla_cmsp_past_demo_v1', 1, false );
}
add_action( 'init', 'nydla_cmsp_ensure_past_demo_event', 35 );
