<?php
/**
 * Hero illustration 2026: contact.
 *
 * Markup only. Position, size and motion live in
 * src/sass/theme/blocks/_cb_hero_illustration_2026.scss.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$cb_anim_img = get_stylesheet_directory_uri() . '/img/anim/';

?>
<div class="container-xl hero_illus hero_illus--contact">
	<div class="row">
		<div class="col-6 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--man hero_illus__item--end hero_illus__item--slide-left" src="<?= esc_url( $cb_anim_img . 'Thinking-Man-Reverse.png' ); ?>" alt="">
		</div>
		<div class="col-6 hero_illus__col">
			<img class="hero_illus__item hero_illus__item--sign hero_illus__item--start hero_illus__item--fade" src="<?= esc_url( $cb_anim_img . 'Sign-post.png' ); ?>" alt="">
		</div>
	</div>
</div>
