<?php
/**
 * Block Name: CB Text/Image
 *
 * This template displays a configurable two-col text image component.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;


$legacy_image = get_field( 'image' );
$gallery      = get_field( 'gallery' ) ? get_field( 'gallery' ) : array();
$image_ids    = array();

if ( is_array( $legacy_image ) ) {
    $legacy_image = $legacy_image['ID'] ?? null;
}

if ( $legacy_image ) {
    $image_ids[] = (int) $legacy_image;
}

foreach ( $gallery as $gallery_image ) {
    if ( is_array( $gallery_image ) ) {
        $gallery_image = $gallery_image['ID'] ?? null;
    }

    $gallery_image = (int) $gallery_image;

    if ( $gallery_image ) {
        $image_ids[] = $gallery_image;
    }
}

$image_ids = array_values( array_unique( $image_ids ) );

$has_gallery_carousel = count( $image_ids ) > 1;
$display_image_id     = $image_ids[0] ?? null;

$colour     = strtolower( get_field( 'theme' ) );
$parts      = preg_split( '/-/', $colour );
$colour     = $parts[0];
$breakout   = '';
$background = '';
if ( '' !== $colour ) {
    $background = 'bg--' . $colour;
}
if ( get_field( 'breakout' )[0] ?? null && 'Yes' === get_field( 'breakout' )[0] ) {
    $breakout   = 'bg--' . $colour;
    $background = '';
}

$split_text  = 'col-md-6';
$split_image = 'col-md-6';

if ( '6040' === get_field( 'split' ) ) {
    $split_text  = 'col-md-8';
    $split_image = 'col-md-4';
}
if ( '7030' === get_field( 'split' ) ) {
    $split_text  = 'col-md-10';
    $split_image = 'col-md-2';
}

$order_text  = 'order-2 order-md-1';
$order_image = 'order-1 order-md-2';

if ( 'image-text' === get_field( 'order' ) ) {
    $order_text  = 'order-2 order-md-2';
    $order_image = 'order-1 order-md-1';
}

$classes = $block['className'] ?? null;

?>
<!-- text_image -->
<section
    class="text_image <?= esc_attr( $breakout ); ?> <?= esc_attr( $classes ); ?>">
    <div class="container-xl <?= esc_attr( $background ); ?> py-4">
        <h2 class="d-md-none">
            <?= wp_kses_post( get_field( 'title' ) ); ?>
        </h2>
        <div class="row align-items-center">
            <div
                class="<?= esc_attr( $split_text ); ?> <?= esc_attr( $order_text ); ?>">
                <h2 class="d-none d-md-block">
                    <?= wp_kses_post( get_field( 'title' ) ); ?>
                </h2>
                <div><?= wp_kses_post( get_field( 'content' ) ); ?></div>
            </div>
            <div
                class="<?= esc_attr( $split_image ); ?> <?= esc_attr( $order_image ); ?> text-center">
                <?php
				if ( $has_gallery_carousel ) {
					?>
                <div class="text_image__carousel swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ( $image_ids as $image_id ) : ?>
                        <div class="swiper-slide">
                            <?= wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'wow text_image__image' ) ); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                	<?php
				} elseif ( $display_image_id ) {
					?>
                	<?= wp_get_attachment_image( $display_image_id, 'large', false, array( 'class' => 'wow text_image__image' ) ); ?>
                	<?php
				}
				?>
            </div>
        </div>
    </div>
</section>
<?php
if ( get_field( 'cta' ) ) {
	$cta = get_field( 'cta' );
	?>
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-4 text-center halfcircle-container">
            <div
                class="div-rounded ss-halfcircle halfcircle-<?= esc_attr( $colour ); ?>">
                <div class="halfcircle-content fw-bold">
                    <a class="anim-arrow--pulse"
                        href="<?= esc_url( $cta['url'] ); ?>"><?= esc_html( $cta['title'] ); ?>
                        <span class="arrow mt-2"></span></a>
                </div>
            </div>
        </div>
    </div>
</div>
	<?php
}
