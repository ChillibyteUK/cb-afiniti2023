<?php
/**
 * Block template for CB Five Steps.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="five_steps pt-4 pb-5">
	<div class="container-xl">
		<div class="five_steps__flow">
			<div class="five_steps__card five_steps--<?= esc_attr( get_field( 'colour_1' ) ); ?>">
				<div class="five_steps__inner">
					<h3 class="five_steps__title"><?= wp_kses_post( get_field( 'title_1' ) ); ?></h3>
					<div class="five_steps__content"><?= wp_kses_post( get_field( 'content_1' ) ); ?></div>
					<div class="five_steps__strap"><?= esc_html( get_field( 'strap_1' ) ); ?></div>
				</div>
				<div class="five_steps__step">
					<?= esc_html( get_field( 'step_1' ) ); ?>
				</div>
			</div>
			<div class="five_steps__card five_steps--<?= esc_attr( get_field( 'colour_2' ) ); ?>">
				<div class="five_steps__inner">
					<h3 class="five_steps__title"><?= wp_kses_post( get_field( 'title_2' ) ); ?></h3>
					<div class="five_steps__content"><?= wp_kses_post( get_field( 'content_2' ) ); ?></div>
					<div class="five_steps__strap"><?= esc_html( get_field( 'strap_2' ) ); ?></div>
				</div>
				<div class="five_steps__step">
					<?= esc_html( get_field( 'step_2' ) ); ?>
				</div>
			</div>
			<div class="five_steps__card five_steps--<?= esc_attr( get_field( 'colour_3' ) ); ?>">
				<div class="five_steps__inner">
					<h3 class="five_steps__title"><?= wp_kses_post( get_field( 'title_3' ) ); ?></h3>
					<div class="five_steps__content"><?= wp_kses_post( get_field( 'content_3' ) ); ?></div>
					<div class="five_steps__strap"><?= esc_html( get_field( 'strap_3' ) ); ?></div>
				</div>
				<div class="five_steps__step">
					<?= esc_html( get_field( 'step_3' ) ); ?>
				</div>
			</div>
			<div class="five_steps__card five_steps--<?= esc_attr( get_field( 'colour_4' ) ); ?>">
				<div class="five_steps__inner">
					<h3 class="five_steps__title"><?= wp_kses_post( get_field( 'title_4' ) ); ?></h3>
					<div class="five_steps__content"><?= wp_kses_post( get_field( 'content_4' ) ); ?></div>
					<div class="five_steps__strap"><?= esc_html( get_field( 'strap_4' ) ); ?></div>
				</div>
				<div class="five_steps__step">
					<?= esc_html( get_field( 'step_4' ) ); ?>
				</div>
			</div>
			<div class="five_steps__card five_steps--<?= esc_attr( get_field( 'colour_5' ) ); ?>">
				<div class="five_steps__inner">
					<h3 class="five_steps__title"><?= wp_kses_post( get_field( 'title_5' ) ); ?></h3>
					<div class="five_steps__content"><?= wp_kses_post( get_field( 'content_5' ) ); ?></div>
					<div class="five_steps__strap"><?= esc_html( get_field( 'strap_5' ) ); ?></div>
				</div>
				<div class="five_steps__step">
					<?= esc_html( get_field( 'step_5' ) ); ?>
				</div>
			</div>
		</div>
	</div>
</section>