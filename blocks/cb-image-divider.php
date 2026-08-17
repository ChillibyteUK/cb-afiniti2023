<?php
/**
 * Block template for CB Image Divider.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$colour = get_field( 'title_colour' );
$parts  = preg_split( '/-/', $colour );
$colour = $parts[0];

$classes = $block['className'] ?? null;

?>
<!-- image_divider -->
<section class="image_divider pt-5 <?= esc_url( $classes ); ?>">
	<div class="container-xl">
		<div class="row">
			<div class="col-12 text-center"><img
					src="<?= esc_url( wp_get_attachment_image_url( get_field( 'image' ), 'large' ) ); ?>"
					class="border-dash-circle image_divider__icon"></div>
			<div class="col-6 border-dash-top-left image_divider__h-60"></div>
			<div class="col-6 border-dash-top-right image_divider__h-60"></div>
			<div class="col-12 image_divider__title">
				<h2 class="h3 fw-bold text--<?= esc_attr( $colour ); ?>">
					<?= wp_kses_post( get_field( 'title' ) ); ?>
				</h2>
			</div>
		</div>
	</div>
</section>