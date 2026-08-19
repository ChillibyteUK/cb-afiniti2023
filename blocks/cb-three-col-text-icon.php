<?php
/**
 * Block template for CB Three Col Text Icon.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$classes = $block['className'] ?? null;
?>
<!-- three_col_text_icon -->
<div class="container-xl text_icon py-4 <?= esc_attr( $classes ); ?>">
	<div class="row g-4">
		<div class="col-lg-4 d-flex gap-4">
			<div>
				<div class="text_icon__icon">
					<?= wp_get_attachment_image( get_field( 'col1_icon' ), 'medium' ); ?>
				</div>
			</div>
			<div class="text_icon__text">
				<h3 class="text--green">
					<?= wp_kses_post( get_field( 'col1_title' ) ); ?>
				</h3>
				<p><?= wp_kses_post( get_field( 'col1_content' ) ); ?>
				</p>
			</div>
		</div>
		<div class="col-lg-4 d-flex gap-4">
			<div>
				<div class="text_icon__icon">
					<?= wp_get_attachment_image( get_field( 'col2_icon' ), 'medium' ); ?>
				</div>
			</div>
			<div class="text_icon__text">
				<h3 class="text--green">
					<?= wp_kses_post( get_field( 'col2_title' ) ); ?>
				</h3>
				<p><?= wp_kses_post( get_field( 'col2_content' ) ); ?>
				</p>
			</div>
		</div>
		<div class="col-lg-4 d-flex gap-4">
			<div>
				<div class="text_icon__icon">
					<?= wp_get_attachment_image( get_field( 'col3_icon' ), 'medium' ); ?>
				</div>
			</div>
			<div class="text_icon__text">
				<h3 class="text--green">
					<?= wp_kses_post( get_field( 'col3_title' ) ); ?>
				</h3>
				<p><?= wp_kses_post( get_field( 'col3_content' ) ); ?>
				</p>
			</div>
		</div>
	</div>
</div>