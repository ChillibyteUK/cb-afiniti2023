<?php
/**
 * Block Name: CB Quote
 *
 * Displays one or more quotes in a slick slider.
 * Expects an ACF repeater field named 'quotes' with sub-fields:
 *   - quote  (wysiwyg / textarea)
 *   - name   (text)
 *   - role   (text)
 *   - image  (image, return: url)
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$classes = $block['className'] ?? '';
$quotes  = get_field( 'quotes' );

if ( empty( $quotes ) ) {
	return;
}

// Unique ID so multiple instances on the same page each get their own slider.
$slider_id = 'cb-quote-slider-' . ( $block['id'] ?? uniqid() );
?>
<!-- quote -->
<div class="container my-5 <?= esc_attr( $classes ); ?>">
	<div id="<?= esc_attr( $slider_id ); ?>" class="cb-quote-slider">
		<?php foreach ( $quotes as $q ) : ?>
		<div class="cb-quote-slide">
			<div class="row g-4 pb-4 align-items-center">
				<div class="col-lg-8 order-2 order-lg-1 my-auto">
					<div class="quote pb-3">
						<?= wp_kses_post( $q['quote'] ); ?>
					</div>
					<div class="mb-3">
						<span class="quote__name"><?= esc_html( $q['name'] ); ?></span>,
						<span class="quote_role"><?= esc_html( $q['role'] ); ?></span>
					</div>
				</div>
				<?php if ( ! empty( $q['image'] ) ) : ?>
				<div class="col-lg-4 text-center order-1 order-lg-2">
					<img src="<?= esc_url( $q['image'] ); ?>" class="quote__image mb-4" alt="<?= esc_attr( $q['name'] ); ?>">
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<?php

add_action( 'wp_footer', function () use ( $slider_id, $quotes ) {
	?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"
	integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw=="
	crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"
	integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A=="
	crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
	integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
	crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
jQuery(function ($) {
	var $slider = $('#<?= esc_js( $slider_id ); ?>');
	<?php if ( count( $quotes ) > 1 ) : ?>
	$slider.slick({
		infinite:      true,
		slidesToShow:  1,
		slidesToScroll: 1,
		autoplay:      true,
		autoplaySpeed: 5000,
		dots:          true,
		arrows:        true,
	});
	<?php endif; ?>
});
</script>
	<?php
}, 9999 );
