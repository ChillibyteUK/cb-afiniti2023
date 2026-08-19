<?php
/**
 * Block Name: CB Page Intro
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$colour      = strtolower( get_field( 'colour' ) );
$breakout    = '';
$background  = '';
$colour_name = '';
if ( '' !== $colour ) {
	$background  = 'bg--' . $colour;
	$parts       = preg_split( '/-/', $colour );
	$colour_name = $parts[0];
}

$l = get_field( 'cta_link' );

?>
<div class="container-xl pb-4">
	<div class="row">
	<div class="col-12 col-lg-8 pb-2">
		<div class="bg--<?= esc_attr( $colour_name ); ?>-700 p-4">
		<div class="font-large text-white">
			<h2><?= wp_kses_post( get_field( 'title' ) ); ?></h2>
		</div>
		</div>
		<div class="bg--<?= esc_attr( $colour ); ?> text-white p-4">
		<?= wp_kses_post( get_field( 'content' ) ); ?>
		</div>
	</div>
	<div class="col-12 col-lg-4">
		<div class="row">
		<div class="col-12">
			<div class="bg--<?= esc_attr( $colour ); ?> px-5 py-4">
			<div class="fs-5 fw-bold pb-2">
				<?= wp_kses_post( get_field( 'right_title' ) ); ?>
			</div>
			<div>
				<?= wp_kses_post( get_field( 'right_content' ) ); ?>
				<?php
				if ( get_field( 'image' ) ) {
					?>
				<div class="text-center py-4"><img
					src="<?= esc_url( wp_get_attachment_image_url( get_field( 'image' ), 'large' ) ); ?>"
					class="wow fadeIn"></div>
					<?php
				}
				?>
			</div>
			</div>
			<div class="row justify-content-center">
			<div class="col-8 text-center halfcircle-container">
				<div
				class="div-rounded ss-halfcircle halfcircle-<?= esc_attr( $colour_name ); ?>">
				<div class="halfcircle-content halfcircle-content-10 fw-bold">
					<a href="<?= esc_url( $l['url'] ); ?>"
					target="<?= esc_attr( $l['target'] ); ?>"><?= esc_html( $l['title'] ); ?>
					<span class="arrow arrow-block arrow mt-2"></span></a>
				</div>
				</div>
			</div>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>