<?php
/**
 * Hero illustration 2026: business-change.
 *
 * Markup only. Position, size and motion live in
 * src/sass/theme/blocks/_cb_hero_illustration_2026.scss.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$cb_anim_img = get_stylesheet_directory_uri() . '/img/anim/';

?>
<div class="container-xl hero_illus hero_illus--business-change">
	<div class="row">
		<div class="col-2 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--cloud-1 hero_illus__item--end hero_illus__item--slide-left" src="<?= esc_url( $cb_anim_img . 'Small-Cloud.png' ); ?>" alt="">
			<img class="hero_illus__item hero_illus__item--cloud-2 hero_illus__item--end hero_illus__item--slide-left" src="<?= esc_url( $cb_anim_img . 'Mid-Cloud.png' ); ?>" alt="">
		</div>
		<div class="col-5 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--ship hero_illus__item--end hero_illus__item--slide-left" src="<?= esc_url( $cb_anim_img . 'Flying-Ship.png' ); ?>" alt="">
		</div>
		<div class="col-2 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--person hero_illus__item--start hero_illus__item--slide-right" src="<?= esc_url( $cb_anim_img . 'Thinking-Man-Reverse.png' ); ?>" alt="">
		</div>
		<div class="col-3 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--birds hero_illus__item--start hero_illus__item--slide-right" src="<?= esc_url( $cb_anim_img . 'Birds.png' ); ?>" alt="">
		</div>
	</div>
</div>
