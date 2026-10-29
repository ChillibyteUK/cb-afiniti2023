<?php
/**
 * Block template for CB Form CTA.
 *
 * A button that expands a panel containing a Gravity Form. Uses Bootstrap's
 * collapse, which is already in the theme bundle, so there is no block JS.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$button_label = trim( (string) get_field( 'button_label' ) );
$form_id      = absint( get_field( 'form_id' ) );
$after_text   = trim( (string) get_field( 'after_text' ) );

if ( '' === $button_label || ! $form_id ) {
	return;
}

// Several of these can sit on one page, so the collapse target needs to be
// unique per instance.
$panel_id = 'cb-form-cta-' . sanitize_html_class( $block['id'] ?? (string) $form_id );

// "Additional CSS class(es)" from the block's Advanced panel. Needs
// 'className' => true in the block's supports (inc/cb-blocks.php) to appear.
$classes = $block['className'] ?? '';

?>
<section class="form_cta <?= esc_attr( $classes ); ?>"
	<?= ! empty( $block['anchor'] ) ? 'id="' . esc_attr( $block['anchor'] ) . '"' : ''; ?>>
	<div class="container-xl">
		<div class="form_cta__card">
			<div class="text-center">
				<button class="form_cta__button btn collapsed" type="button"
					data-bs-toggle="collapse"
					data-bs-target="#<?= esc_attr( $panel_id ); ?>"
					aria-expanded="false"
					aria-controls="<?= esc_attr( $panel_id ); ?>">
					<?= esc_html( $button_label ); ?>
				</button>
			</div>
			<?php if ( '' !== $after_text ) { ?>
			<p class="form_cta__after"><?= esc_html( $after_text ); ?></p>
			<?php } ?>
			<div class="collapse" id="<?= esc_attr( $panel_id ); ?>">
				<div class="form_cta__form">
					<?= do_shortcode( '[gravityform id="' . $form_id . '" title="false" ajax="true"]' ); ?>
				</div>
			</div>
		</div>
	</div>
</section>
