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

// Palette slugs from the editor palette field. Empty means "use the SCSS
// defaults", so only emit a custom property when a colour was chosen.
$card_style = '';
foreach ( array(
	'--form-cta-bg'   => get_field( 'background' ),
	'--form-cta-text' => get_field( 'text_colour' ),
) as $prop => $slug ) {
	$slug = strtolower( (string) $slug );
	if ( '' !== $slug && preg_match( '/^[a-z0-9-]+$/', $slug ) ) {
		$card_style .= $prop . ':var(--col-' . $slug . ');';
	}
}

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
		<div class="form_cta__card"
			<?= '' !== $card_style ? 'style="' . esc_attr( $card_style ) . '"' : ''; ?>>
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
