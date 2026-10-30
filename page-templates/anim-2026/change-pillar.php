<?php
/**
 * Hero illustration 2026: change-pillar.
 *
 * Markup only. Position, size and motion live in
 * src/sass/theme/blocks/_cb_hero_illustration_2026.scss.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$cb_anim_img = get_stylesheet_directory_uri() . '/img/anim/';

?>
<div class="container-xl hero_illus hero_illus--change-pillar">
	<div class="row">
		<div class="col-2 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--star-1 hero_illus__item--end hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Star_3-63x47.png' ); ?>" alt="">
		</div>
		<div class="col-2 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--star-2 hero_illus__item--end hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Star_1-46x51.png' ); ?>" alt="">
		</div>
		<div class="col-5 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--ship hero_illus__item--end hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Flying-Ship_Telescope-clouds-519x436.png' ); ?>" alt="">
		</div>
	</div>
</div>
