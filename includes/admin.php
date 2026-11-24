<?php
/**
 * Admin menu under one NYDLA CMSP parent.
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Top-level menu + children.
 */
function nydla_cmsp_admin_menu() {
	add_menu_page(
		__( 'NYDLA CMSP', 'nydla-cmsp' ),
		__( 'NYDLA CMSP', 'nydla-cmsp' ),
		'edit_posts',
		'nydla-cmsp',
		'nydla_cmsp_render_dashboard',
		'dashicons-screenoptions',
		58
	);

	add_submenu_page(
		'nydla-cmsp',
		__( 'Dashboard', 'nydla-cmsp' ),
		__( 'Dashboard', 'nydla-cmsp' ),
		'edit_posts',
		'nydla-cmsp',
		'nydla_cmsp_render_dashboard'
	);

	add_submenu_page(
		'nydla-cmsp',
		__( 'Sponsors', 'nydla-cmsp' ),
		__( 'Sponsors', 'nydla-cmsp' ),
		'edit_posts',
		'edit.php?post_type=nydla_sponsor'
	);

	add_submenu_page(
		'nydla-cmsp',
		__( 'Events', 'nydla-cmsp' ),
		__( 'Events', 'nydla-cmsp' ),
		'edit_posts',
		'edit.php?post_type=nydla_event'
	);

	add_submenu_page(
		'nydla-cmsp',
		__( 'Podcasts', 'nydla-cmsp' ),
		__( 'Podcasts', 'nydla-cmsp' ),
		'edit_posts',
		'edit.php?post_type=nydla_podcast'
	);

	add_submenu_page(
		'nydla-cmsp',
		__( 'Podcast Categories', 'nydla-cmsp' ),
		__( 'Podcast Categories', 'nydla-cmsp' ),
		'manage_categories',
		'edit-tags.php?taxonomy=nydla_podcast_cat&post_type=nydla_podcast'
	);
}
add_action( 'admin_menu', 'nydla_cmsp_admin_menu' );

/**
 * Highlight parent menu on CPT screens.
 *
 * @param string $parent Parent file.
 * @return string
 */
function nydla_cmsp_parent_file( $parent ) {
	global $current_screen;
	if ( ! $current_screen ) {
		return $parent;
	}
	if ( in_array( $current_screen->post_type, array( 'nydla_sponsor', 'nydla_event', 'nydla_podcast' ), true ) ) {
		return 'nydla-cmsp';
	}
	if ( 'nydla_podcast_cat' === $current_screen->taxonomy ) {
		return 'nydla-cmsp';
	}
	return $parent;
}
add_filter( 'parent_file', 'nydla_cmsp_parent_file' );

/**
 * Dashboard overview.
 */
function nydla_cmsp_render_dashboard() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	$counts = array(
		'sponsors' => wp_count_posts( 'nydla_sponsor' ),
		'events'   => wp_count_posts( 'nydla_event' ),
		'podcasts' => wp_count_posts( 'nydla_podcast' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'NYDLA CMSP', 'nydla-cmsp' ); ?></h1>
		<p><?php esc_html_e( 'Manage sponsors logos, event banners, and podcast tiles. Place them with [NYDLA-CMSP type="sponsors|events|podcasts"]. Page-1 partner carousel stays in Appearance → Customize.', 'nydla-cmsp' ); ?></p>
		<table class="widefat striped" style="max-width:40rem">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Type', 'nydla-cmsp' ); ?></th>
					<th><?php esc_html_e( 'Published', 'nydla-cmsp' ); ?></th>
					<th><?php esc_html_e( 'Shortcode', 'nydla-cmsp' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=nydla_sponsor' ) ); ?>"><?php esc_html_e( 'Sponsors', 'nydla-cmsp' ); ?></a></td>
					<td><?php echo esc_html( (string) ( $counts['sponsors']->publish ?? 0 ) ); ?></td>
					<td><code>[NYDLA-CMSP type="sponsors"]</code></td>
				</tr>
				<tr>
					<td><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=nydla_event' ) ); ?>"><?php esc_html_e( 'Events', 'nydla-cmsp' ); ?></a></td>
					<td><?php echo esc_html( (string) ( $counts['events']->publish ?? 0 ) ); ?></td>
					<td><code>[NYDLA-CMSP type="events"]</code></td>
				</tr>
				<tr>
					<td><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=nydla_podcast' ) ); ?>"><?php esc_html_e( 'Podcasts', 'nydla-cmsp' ); ?></a></td>
					<td><?php echo esc_html( (string) ( $counts['podcasts']->publish ?? 0 ) ); ?></td>
					<td><code>[NYDLA-CMSP type="podcasts"]</code></td>
				</tr>
			</tbody>
		</table>
		<p style="margin-top:1.5rem"><em><?php esc_html_e( 'Bez Leda Studios · v', 'nydla-cmsp' ); ?><?php echo esc_html( NYDLA_CMSP_VERSION ); ?></em></p>
	</div>
	<?php
}

/**
 * Featured image help text.
 *
 * @param string $content Default HTML.
 * @param int    $post_id Post ID.
 * @return string
 */
function nydla_cmsp_featured_image_html( $content, $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return $content;
	}
	$note = '';
	if ( 'nydla_sponsor' === $post->post_type ) {
		$note = __( 'Set sponsor logo', 'nydla-cmsp' );
	} elseif ( 'nydla_event' === $post->post_type ) {
		$note = __( 'Set event banner', 'nydla-cmsp' );
	} elseif ( 'nydla_podcast' === $post->post_type ) {
		$note = __( 'Set podcast portrait', 'nydla-cmsp' );
	}
	if ( $note ) {
		$content .= '<p class="description">' . esc_html( $note ) . '</p>';
	}
	return $content;
}
add_filter( 'admin_post_thumbnail_html', 'nydla_cmsp_featured_image_html', 10, 2 );
