<?php
/**
 * Meta boxes and field helpers.
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register meta boxes.
 */
function nydla_cmsp_add_meta_boxes() {
	add_meta_box(
		'nydla_cmsp_sponsor',
		__( 'Sponsor details', 'nydla-cmsp' ),
		'nydla_cmsp_render_sponsor_meta',
		'nydla_sponsor',
		'normal',
		'high'
	);
	add_meta_box(
		'nydla_cmsp_event',
		__( 'Event details', 'nydla-cmsp' ),
		'nydla_cmsp_render_event_meta',
		'nydla_event',
		'normal',
		'high'
	);
	add_meta_box(
		'nydla_cmsp_podcast',
		__( 'Podcast details', 'nydla-cmsp' ),
		'nydla_cmsp_render_podcast_meta',
		'nydla_podcast',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'nydla_cmsp_add_meta_boxes' );

/**
 * Shared URL + active fields.
 *
 * @param WP_Post $post Post.
 * @param string  $url_label URL label.
 */
function nydla_cmsp_render_shared_fields( $post, $url_label ) {
	$url    = (string) get_post_meta( $post->ID, '_nydla_url', true );
	$active = get_post_meta( $post->ID, '_nydla_active', true );
	if ( '' === $active ) {
		$active = '1';
	}
	?>
	<p>
		<label for="nydla_url"><strong><?php echo esc_html( $url_label ); ?></strong></label><br>
		<input type="url" class="widefat" id="nydla_url" name="nydla_url" value="<?php echo esc_attr( $url ); ?>" placeholder="https://">
	</p>
	<p>
		<label>
			<input type="checkbox" name="nydla_active" value="1" <?php checked( $active, '1' ); ?>>
			<?php esc_html_e( 'Active (show on front end)', 'nydla-cmsp' ); ?>
		</label>
	</p>
	<p class="description"><?php esc_html_e( 'Set Order (Attributes) for sort position. Featured image is the logo / cover.', 'nydla-cmsp' ); ?></p>
	<?php
}

/**
 * Sponsor meta.
 *
 * @param WP_Post $post Post.
 */
function nydla_cmsp_render_sponsor_meta( $post ) {
	wp_nonce_field( 'nydla_cmsp_save_meta', 'nydla_cmsp_nonce' );
	nydla_cmsp_render_shared_fields( $post, __( 'Destination URL', 'nydla-cmsp' ) );
	$page = (string) get_post_meta( $post->ID, '_nydla_page_url', true );
	?>
	<p>
		<label for="nydla_page_url"><strong><?php esc_html_e( 'Optional NYDLA / sponsor page URL', 'nydla-cmsp' ); ?></strong></label><br>
		<input type="url" class="widefat" id="nydla_page_url" name="nydla_page_url" value="<?php echo esc_attr( $page ); ?>" placeholder="https://">
	</p>
	<?php
}

/**
 * Event meta.
 *
 * @param WP_Post $post Post.
 */
function nydla_cmsp_render_event_meta( $post ) {
	wp_nonce_field( 'nydla_cmsp_save_meta', 'nydla_cmsp_nonce' );
	nydla_cmsp_render_shared_fields( $post, __( 'Registration / detail URL', 'nydla-cmsp' ) );
	$date  = (string) get_post_meta( $post->ID, '_nydla_date', true );
	$venue = (string) get_post_meta( $post->ID, '_nydla_venue', true );
	?>
	<p>
		<label for="nydla_date"><strong><?php esc_html_e( 'Date / time', 'nydla-cmsp' ); ?></strong></label><br>
		<input type="text" class="widefat" id="nydla_date" name="nydla_date" value="<?php echo esc_attr( $date ); ?>" placeholder="<?php esc_attr_e( 'e.g. March 12, 2027 · 6:00 PM ET', 'nydla-cmsp' ); ?>">
	</p>
	<p>
		<label for="nydla_venue"><strong><?php esc_html_e( 'Venue / location', 'nydla-cmsp' ); ?></strong></label><br>
		<input type="text" class="widefat" id="nydla_venue" name="nydla_venue" value="<?php echo esc_attr( $venue ); ?>" placeholder="<?php esc_attr_e( 'e.g. Online · Zoom', 'nydla-cmsp' ); ?>">
	</p>
	<?php
}

/**
 * Podcast meta.
 *
 * @param WP_Post $post Post.
 */
function nydla_cmsp_render_podcast_meta( $post ) {
	wp_nonce_field( 'nydla_cmsp_save_meta', 'nydla_cmsp_nonce' );
	nydla_cmsp_render_shared_fields( $post, __( 'Watch / listen URL', 'nydla-cmsp' ) );
	$bio = (string) get_post_meta( $post->ID, '_nydla_bio', true );
	?>
	<p>
		<label for="nydla_bio"><strong><?php esc_html_e( 'Short bio', 'nydla-cmsp' ); ?></strong></label><br>
		<textarea class="widefat" rows="4" id="nydla_bio" name="nydla_bio"><?php echo esc_textarea( $bio ); ?></textarea>
	</p>
	<p class="description"><?php esc_html_e( 'Assign Podcast Categories in the sidebar. Excerpt can hold a one-line subtitle.', 'nydla-cmsp' ); ?></p>
	<?php
}

/**
 * Save meta.
 *
 * @param int $post_id Post ID.
 */
function nydla_cmsp_save_meta( $post_id ) {
	if ( ! isset( $_POST['nydla_cmsp_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nydla_cmsp_nonce'] ) ), 'nydla_cmsp_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$post = get_post( $post_id );
	if ( ! $post || ! in_array( $post->post_type, array( 'nydla_sponsor', 'nydla_event', 'nydla_podcast' ), true ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$url = isset( $_POST['nydla_url'] ) ? esc_url_raw( wp_unslash( $_POST['nydla_url'] ) ) : '';
	update_post_meta( $post_id, '_nydla_url', $url );
	update_post_meta( $post_id, '_nydla_active', isset( $_POST['nydla_active'] ) ? '1' : '0' );

	if ( 'nydla_sponsor' === $post->post_type ) {
		$page = isset( $_POST['nydla_page_url'] ) ? esc_url_raw( wp_unslash( $_POST['nydla_page_url'] ) ) : '';
		update_post_meta( $post_id, '_nydla_page_url', $page );
	}
	if ( 'nydla_event' === $post->post_type ) {
		update_post_meta( $post_id, '_nydla_date', isset( $_POST['nydla_date'] ) ? sanitize_text_field( wp_unslash( $_POST['nydla_date'] ) ) : '' );
		update_post_meta( $post_id, '_nydla_venue', isset( $_POST['nydla_venue'] ) ? sanitize_text_field( wp_unslash( $_POST['nydla_venue'] ) ) : '' );
	}
	if ( 'nydla_podcast' === $post->post_type ) {
		update_post_meta( $post_id, '_nydla_bio', isset( $_POST['nydla_bio'] ) ? sanitize_textarea_field( wp_unslash( $_POST['nydla_bio'] ) ) : '' );
	}
}
add_action( 'save_post', 'nydla_cmsp_save_meta' );
