<?php
/**
 * Podcast tiles + category filters.
 *
 * @package Nydla_Cmsp
 * @var array $items
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$terms = get_terms(
	array(
		'taxonomy'   => 'nydla_podcast_cat',
		'hide_empty' => true,
	)
);
$active_cat = isset( $_GET['cat'] ) ? sanitize_title( wp_unslash( $_GET['cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<div class="nydla-cmsp nydla-cmsp--podcasts" data-nydla-cmsp="podcasts" data-nydla-podcast-filters>
	<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
		<nav class="nydla-cmsp__chips" aria-label="<?php esc_attr_e( 'Podcast categories', 'nydla-cmsp' ); ?>">
			<a class="nydla-cmsp__chip<?php echo '' === $active_cat ? ' is-on' : ''; ?>" href="<?php echo esc_url( remove_query_arg( 'cat' ) ); ?>" data-nydla-cat=""><?php esc_html_e( 'All', 'nydla-cmsp' ); ?></a>
			<?php foreach ( $terms as $term ) : ?>
				<a
					class="nydla-cmsp__chip<?php echo $active_cat === $term->slug ? ' is-on' : ''; ?>"
					href="<?php echo esc_url( add_query_arg( 'cat', $term->slug ) ); ?>"
					data-nydla-cat="<?php echo esc_attr( $term->slug ); ?>"
				><?php echo esc_html( $term->name ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<?php if ( empty( $items ) ) : ?>
		<p class="nydla-cmsp__empty"><?php esc_html_e( 'No active podcasts yet. Add tiles in WP Admin → NYDLA CMSP → Podcasts.', 'nydla-cmsp' ); ?></p>
	<?php else : ?>
		<ul class="nydla-cmsp__podcast-grid">
			<?php foreach ( $items as $item ) : ?>
				<li
					class="nydla-cmsp__podcast"
					data-cats="<?php echo esc_attr( implode( ' ', $item['category_slugs'] ?? array() ) ); ?>"
				>
					<?php if ( $item['image'] ) : ?>
						<div class="nydla-cmsp__podcast-media">
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" decoding="async">
						</div>
					<?php endif; ?>
					<div class="nydla-cmsp__podcast-body">
						<h3 class="nydla-cmsp__podcast-title"><?php echo esc_html( $item['title'] ); ?></h3>
						<?php if ( ! empty( $item['categories'] ) ) : ?>
							<p class="nydla-cmsp__podcast-cats"><?php echo esc_html( implode( ' · ', $item['categories'] ) ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $item['bio'] ) ) : ?>
							<p class="nydla-cmsp__podcast-bio"><?php echo esc_html( $item['bio'] ); ?></p>
						<?php elseif ( ! empty( $item['excerpt'] ) ) : ?>
							<p class="nydla-cmsp__podcast-bio"><?php echo esc_html( $item['excerpt'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $item['url'] ) ) : ?>
							<p><a class="nydla-cmsp__btn" href="<?php echo esc_url( $item['url'] ); ?>"><?php esc_html_e( 'Watch / listen', 'nydla-cmsp' ); ?></a></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
