<?php
/**
 * Front-end queries.
 *
 * @package Nydla_Cmsp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map shortcode type → CPT.
 *
 * @param string $type Shortcode type.
 * @return string
 */
function nydla_cmsp_type_to_post_type( $type ) {
	$map = array(
		'sponsors' => 'nydla_sponsor',
		'events'   => 'nydla_event',
		'podcasts' => 'nydla_podcast',
		'partners' => 'nydla_sponsor',
	);
	return isset( $map[ $type ] ) ? $map[ $type ] : '';
}

/**
 * Active items for a type.
 *
 * @param string $type    sponsors|events|podcasts.
 * @param array  $args    Extra WP_Query args.
 * @return WP_Post[]
 */
function nydla_cmsp_get_items( $type, $args = array() ) {
	$post_type = nydla_cmsp_type_to_post_type( $type );
	if ( ! $post_type ) {
		return array();
	}

	$query_args = array_merge(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => 200,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => ( 'podcasts' === $type ),
			'meta_query'             => array(
				'relation' => 'OR',
				array(
					'key'   => '_nydla_active',
					'value' => '1',
				),
				array(
					'key'     => '_nydla_active',
					'compare' => 'NOT EXISTS',
				),
			),
		),
		$args
	);

	if ( 'podcasts' === $type && ! empty( $args['category'] ) ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'nydla_podcast_cat',
				'field'    => 'slug',
				'terms'    => sanitize_title( $args['category'] ),
			),
		);
		unset( $query_args['category'] );
	}

	$q = new WP_Query( $query_args );
	return $q->posts;
}

/**
 * Item payload for templates.
 *
 * @param WP_Post $post Post.
 * @return array<string, mixed>
 */
function nydla_cmsp_item_data( $post ) {
	$url   = (string) get_post_meta( $post->ID, '_nydla_url', true );
	$image = get_the_post_thumbnail_url( $post, 'large' );
	$data  = array(
		'id'        => $post->ID,
		'title'     => get_the_title( $post ),
		'url'       => $url,
		'image'     => $image ? $image : '',
		'excerpt'   => get_the_excerpt( $post ),
		'menu_order'=> (int) $post->menu_order,
	);

	if ( 'nydla_sponsor' === $post->post_type ) {
		$data['page_url'] = (string) get_post_meta( $post->ID, '_nydla_page_url', true );
	}
	if ( 'nydla_event' === $post->post_type ) {
		$mysql = (string) get_post_meta( $post->ID, '_nydla_datetime', true );
		if ( ! $mysql ) {
			$mysql = nydla_cmsp_normalize_event_datetime( (string) get_post_meta( $post->ID, '_nydla_date', true ) );
		}
		$data['datetime']   = $mysql;
		$data['timestamp']  = nydla_cmsp_event_timestamp( $post );
		$data['date']       = $mysql ? nydla_cmsp_format_event_datetime( $mysql ) : (string) get_post_meta( $post->ID, '_nydla_date', true );
		$data['venue']      = (string) get_post_meta( $post->ID, '_nydla_venue', true );
	}
	if ( 'nydla_podcast' === $post->post_type ) {
		$data['bio'] = (string) get_post_meta( $post->ID, '_nydla_bio', true );
		$terms       = get_the_terms( $post, 'nydla_podcast_cat' );
		$data['categories'] = array();
		$data['category_slugs'] = array();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$data['categories'][]     = $term->name;
				$data['category_slugs'][] = $term->slug;
			}
		}
	}

	return $data;
}

/**
 * Render one event card list (Upcoming / Past).
 *
 * @param array  $list  Items.
 * @param string $empty Empty message.
 */
function nydla_cmsp_render_event_list( $list, $empty ) {
	if ( empty( $list ) ) {
		echo '<p class="nydla-cmsp__empty">' . esc_html( $empty ) . '</p>';
		return;
	}
	echo '<ul class="nydla-cmsp__event-grid">';
	foreach ( $list as $item ) {
		?>
		<li class="nydla-cmsp__event">
			<?php if ( ! empty( $item['image'] ) ) : ?>
				<div class="nydla-cmsp__event-media">
					<img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" decoding="async">
				</div>
			<?php endif; ?>
			<div class="nydla-cmsp__event-body">
				<h3 class="nydla-cmsp__event-title"><?php echo esc_html( $item['title'] ); ?></h3>
				<?php if ( ! empty( $item['date'] ) ) : ?>
					<p class="nydla-cmsp__event-meta"><?php echo esc_html( $item['date'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $item['venue'] ) ) : ?>
					<p class="nydla-cmsp__event-meta"><?php echo esc_html( $item['venue'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $item['excerpt'] ) ) : ?>
					<p class="nydla-cmsp__event-excerpt"><?php echo esc_html( $item['excerpt'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $item['url'] ) ) : ?>
					<p><a class="nydla-cmsp__btn" href="<?php echo esc_url( $item['url'] ); ?>"><?php esc_html_e( 'Learn more', 'nydla-cmsp' ); ?></a></p>
				<?php endif; ?>
			</div>
		</li>
		<?php
	}
	echo '</ul>';
}
