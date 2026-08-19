<?php
/**
 * Block template for CB Vimeo Video.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$embed_url = get_field( 'embed_url' );
$width     = get_field( 'width' );
$colour    = strtolower( get_field( 'background' ) );
$breakout  = '';
if ( '' !== $colour ) {
	$breakout = 'breakout bg--' . $colour;
}

$classes = $block['className'] ?? null;

?>
<!-- vimeo -->
<section
	class="<?= esc_attr( $breakout ); ?> py-4 <?= esc_attr( $classes ); ?>">
	<div class="container-xl vimeo mx-auto <?= esc_attr( $width ); ?>">
		<div class="ratio ratio-16x9">
			<iframe height=400 allowfullscreen="allowfullscreen"
				src="<?= esc_url( $embed_url ); ?>"></iframe>
		</div>
	</div>
</section>