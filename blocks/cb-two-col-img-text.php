<?php
/**
 * Block template for CB Two Column Image Text.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$classes = $block['className'] ?? null;
?>
<!-- two_col_img_text -->
<div class="container-xl py-4 <?= esc_attr( $classes ); ?>">
	<div class="row g-4 justify-content-center">
		<?php
		while ( have_rows( 'items' ) ) {
			the_row();
			?>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-4 d-flex flex-column justify-content-center">
					<?= wp_get_attachment_image( get_sub_field( 'image' ), 'large' ); ?>
				</div>
				<div class="col-md-8 d-flex flex-column justify-content-center">
					<h4 class="fw-bold">
						<?= wp_kses_post( get_sub_field( 'title' ) ); ?>
					</h4>
					<?= wp_kses_post( get_sub_field( 'content' ) ); ?>
				</div>
			</div>
		</div>
			<?php
		}
		?>
	</div>
</div>