<?php
/**
 * Hero illustration 2026: change-readiness.
 *
 * Markup only. Position, size and motion live in
 * src/sass/theme/blocks/_cb_hero_illustration_2026.scss.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$cb_anim_img = get_stylesheet_directory_uri() . '/img/anim/';

?>
<div class="container-xl hero_illus hero_illus--change-readiness">
	<div class="row">
		<div class="col-12 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--machine hero_illus__item--center hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Change-Readiness-Tool.png' ); ?>" alt="">
		</div>
	</div>
</div>
