<?php
/**
 * Block Name: Intro Block
 *
 * This is the template that displays the intro block.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$left_theme  = strtolower( get_field( 'left_theme' ) );
$parts       = preg_split( '/-/', $left_theme );
$left_theme  = $parts[0];
$right_theme = strtolower( get_field( 'right_theme' ) );
$parts       = preg_split( '/-/', $right_theme );
$right_theme = $parts[0];

$classes = $block['className'] ?? null;

$image = get_field( 'image' ) ? wp_get_attachment_image_url( get_field( 'image' ), 'full' ) : get_stylesheet_directory_uri() . '/img/anim/Flag-bearer.png';

?>
<!-- intro_block -->
<div class="container-xl intro_block pb-4 <?= esc_attr( $classes ); ?>">
    <div class="row">
        <div class="col-lg-5">
            <h2 class="h1 text--<?= esc_attr( $left_theme ); ?> fw-bold">
                <?= wp_kses_post( get_field( 'left_title' ) ); ?>
            </h2>
            <div><?= wp_kses_post( get_field( 'left_content' ) ); ?>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="row">
                <div class="d-none d-lg-block col-6 border-dash-right h-90px"></div>
                <div class="d-none d-lg-block col-6"></div>
                <div class="col-4 offset-4 col-lg-12 offset-lg-0 py-4 py-lg-0"><img
                        src="<?= esc_url( $image ); ?>"
                        class="border-dash-circle"></div>
                <div class="d-none d-lg-block col-6 border-dash-right h-90px"></div>
                <div class="d-none d-lg-block col-6"></div>
            </div>
        </div>
        <div class="col-lg-5">
            <h2 class="h1 text--<?= esc_attr( $right_theme ); ?> fw-bold">
                <?= wp_kses_post( get_field( 'right_title' ) ); ?>
            </h2>
            <div><?= wp_kses_post( get_field( 'right_content' ) ); ?>
            </div>
        </div>
    </div>
</div>