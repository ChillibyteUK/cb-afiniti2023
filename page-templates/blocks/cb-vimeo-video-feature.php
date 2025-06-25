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

$orderText = 'order-2 order-md-2';
$orderImage = 'order-1 order-md-1';

if (get_field('order') == 'Image Text') {
    $orderText = 'order-2 order-md-1';
    $orderImage = 'order-1 order-md-2';
}


$embed_url = 'https://player.vimeo.com/video/' . get_field( 'featured_video_id' ) . '?title=0&byline=0&portrait=0&badge=0';

$featured_video = get_field( 'video_id', get_field( 'featured_video' ) );
$featured_img   = get_vimeo_data_from_id( $featured_video, 'thumbnail_url' );

?>
<section class="video_feature <?= esc_attr( $bg_outer ); ?> py-4">
    <div class="container-xl <?= esc_attr( $bg_inner ); ?> p-4">
        <div class="row">
            <div class="col-md-6 <?= esc_attr( $orderImage ); ?>"">
				<div class="ratio ratio-16x9">
            		<iframe height=400 allowfullscreen="allowfullscreen" src="<?= esc_url( $embed_url ); ?>"></iframe>
        		</div>
            </div>
            <div class="col-md-6 <?= esc_attr( $orderText ); ?>">
                <h2><?= esc_html( get_field( 'feature_title' ) ); ?></h2>
                <p><?= esc_html( get_field( 'feature_description' ) ); ?></p>
            </div>
        </div>
    </div>
</section>
