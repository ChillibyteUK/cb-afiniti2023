<?php
/**
 * Block template for CB Vimeo Video Feature.
 *
 * @package cb-afiniti
 */

defined ( 'ABSPATH' ) || exit;

$colour     = strtolower( get_field( 'background' ) ) ?? null;
$background = 'bg--' . $colour;

$bg_size = get_field( 'bg_size' ) ?? null;

$bg_inner = '';
$bg_outer = '';

if ( 'Full Width' === $bg_size ) {
	$bg_outer = $background;
} else {
	$bg_inner = $background;
}

$featured_video = get_field( 'video_id', get_field( 'featured_video' ) );
$featured_img   = get_vimeo_data_from_id( $featured_video, 'thumbnail_url' );

?>
<section class="video_feature <?= esc_attr( $bg_outer ); ?> py-4">
    <div class="container-xl <?= esc_attr( $bg_inner ); ?> p-4">
        <div class="row">
            <div class="col-md-6">
                <img src="<?= esc_url( $featured_img ); ?>">
            </div>
            <div class="col-md-6">
                <h2><?= esc_html( get_field( 'feature_title' ) ); ?></h2>
                <p><?= esc_html( get_field( 'feature_description' ) ); ?></p>
            </div>
        </div>
    </div>
</section>
