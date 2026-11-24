<?php
/**
 * Event banners grid.
 *
 * @package Nydla_Cmsp
 * @var array $items
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="nydla-cmsp nydla-cmsp--events" data-nydla-cmsp="events">
	<?php if ( empty( $items ) ) : ?>
		<p class="nydla-cmsp__empty"><?php esc_html_e( 'No active events yet. Add banners in WP Admin → NYDLA CMSP → Events.', 'nydla-cmsp' ); ?></p>
	<?php else : ?>
		<ul class="nydla-cmsp__event-grid">
			<?php foreach ( $items as $item ) : ?>
				<li class="nydla-cmsp__event">
					<?php if ( $item['image'] ) : ?>
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
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
