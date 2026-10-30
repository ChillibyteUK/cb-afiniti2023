<?php
/**
 * Hero illustration 2026: insights.
 *
 * Markup only. Position, size and motion live in
 * src/sass/theme/blocks/_cb_hero_illustration_2026.scss.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$cb_anim_img = get_stylesheet_directory_uri() . '/img/anim/';

?>
<div class="container-xl hero_illus hero_illus--insights">
	<div class="row">
		<div class="col-2 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--star-1 hero_illus__item--end hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Star_3-63x47.png' ); ?>" alt="">
		</div>
		<div class="col-2 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--star-2 hero_illus__item--end hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Star_1-46x51.png' ); ?>" alt="">
		</div>
		<div class="col-4 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--insights hero_illus__item--start hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'insights-436x215.png' ); ?>" alt="">
		</div>
		<div class="col-2 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--cloud-1 hero_illus__item--start hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Mid-Cloud.png' ); ?>" alt="">
		</div>
		<div class="col-2 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--cloud-2 hero_illus__item--start hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Small-Cloud.png' ); ?>" alt="">
		</div>
	</div>
</div>
