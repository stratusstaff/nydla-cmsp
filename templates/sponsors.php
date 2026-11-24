<?php
/**
 * Sponsors logo gallery.
 *
 * @package Nydla_Cmsp
 * @var array  $items
 * @var string $layout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="nydla-cmsp nydla-cmsp--sponsors nydla-cmsp--<?php echo esc_attr( $layout ); ?>" data-nydla-cmsp="sponsors">
	<?php if ( empty( $items ) ) : ?>
		<p class="nydla-cmsp__empty"><?php esc_html_e( 'No active sponsors yet. Add logos in WP Admin → NYDLA CMSP → Sponsors.', 'nydla-cmsp' ); ?></p>
	<?php else : ?>
		<ul class="nydla-cmsp__logo-grid">
			<?php foreach ( $items as $item ) : ?>
				<li class="nydla-cmsp__logo">
					<?php
					$href = $item['url'] ? $item['url'] : ( $item['page_url'] ?? '' );
					$inner = '';
					if ( $item['image'] ) {
						$inner = sprintf(
							'<img src="%s" alt="%s" loading="lazy" decoding="async">',
							esc_url( $item['image'] ),
							esc_attr( $item['title'] )
						);
					} else {
						$inner = '<span class="nydla-cmsp__logo-fallback">' . esc_html( $item['title'] ) . '</span>';
					}
					if ( $href ) {
						printf(
							'<a href="%s" rel="noopener noreferrer" target="_blank">%s</a>',
							esc_url( $href ),
							$inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						);
					} else {
						echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
