<?php
/**
 * Event banners — Upcoming vs Past.
 *
 * @package Nydla_Cmsp
 * @var array $items
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$now      = ( new DateTimeImmutable( 'now', wp_timezone() ) )->getTimestamp();
$upcoming = array();
$past     = array();
$undated  = array();

foreach ( $items as $item ) {
	$ts = isset( $item['timestamp'] ) ? (int) $item['timestamp'] : 0;
	if ( $ts <= 0 ) {
		$undated[] = $item;
		continue;
	}
	if ( $ts >= $now ) {
		$upcoming[] = $item;
	} else {
		$past[] = $item;
	}
}

usort(
	$upcoming,
	static function ( $a, $b ) {
		return ( (int) $a['timestamp'] ) <=> ( (int) $b['timestamp'] );
	}
);
usort(
	$past,
	static function ( $a, $b ) {
		return ( (int) $b['timestamp'] ) <=> ( (int) $a['timestamp'] );
	}
);
?>
<div class="nydla-cmsp nydla-cmsp--events" data-nydla-cmsp="events">
	<?php if ( empty( $items ) ) : ?>
		<p class="nydla-cmsp__empty"><?php esc_html_e( 'No active events yet. Add banners in WP Admin → NYDLA CMSP → Events.', 'nydla-cmsp' ); ?></p>
	<?php else : ?>
		<section class="nydla-cmsp__events-section nydla-cmsp__events-section--upcoming" aria-labelledby="nydla-cmsp-upcoming">
			<h2 id="nydla-cmsp-upcoming" class="nydla-cmsp__events-heading"><?php esc_html_e( 'Upcoming Events', 'nydla-cmsp' ); ?></h2>
			<?php
			nydla_cmsp_render_event_list(
				array_merge( $upcoming, $undated ),
				__( 'No upcoming events right now.', 'nydla-cmsp' )
			);
			?>
		</section>

		<hr class="nydla-cmsp__events-divider" />

		<section class="nydla-cmsp__events-section nydla-cmsp__events-section--past" aria-labelledby="nydla-cmsp-past">
			<h2 id="nydla-cmsp-past" class="nydla-cmsp__events-heading"><?php esc_html_e( 'Past Events', 'nydla-cmsp' ); ?></h2>
			<?php
			nydla_cmsp_render_event_list(
				$past,
				__( 'No past events yet.', 'nydla-cmsp' )
			);
			?>
		</section>
	<?php endif; ?>
</div>
