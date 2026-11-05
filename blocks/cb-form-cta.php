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

/**
 * Palette slugs from the editor palette fields. Empty means "use the SCSS
 * defaults" - a transparent card and the orange button - so a custom property
 * is only emitted when a colour was actually chosen.
 */
$slug_of = function ( $value ) {
	$slug = strtolower( (string) $value );
	return preg_match( '/^[a-z0-9-]+$/', $slug ) ? $slug : '';
};

$bg_slug   = $slug_of( get_field( 'background' ) );
$text_slug = $slug_of( get_field( 'text_colour' ) );
$btn_slug  = $slug_of( get_field( 'button_colour' ) );

/**
 * Foreground for a chosen palette colour: white, except on white itself, where
 * it would be invisible. Matches .btn--orange and the rest of the theme's
 * buttons, which are white-on-colour throughout.
 */
$text_on = function ( $slug ) {
	return 'white' === $slug ? 'var(--col-grey-700)' : '#fff';
};

$card_vars = array();

if ( '' !== $bg_slug ) {
	$card_vars['--form-cta-bg'] = 'var(--col-' . $bg_slug . ')';
}

// An explicit text colour wins. Otherwise a chosen background gets white text,
// as the card always had. With no background the card is transparent, so the
// text keeps the page's own colour instead.
if ( '' !== $text_slug ) {
	$card_vars['--form-cta-text'] = 'var(--col-' . $text_slug . ')';
} elseif ( '' !== $bg_slug ) {
	$card_vars['--form-cta-text'] = $text_on( $bg_slug );
}

if ( '' !== $btn_slug ) {
	$card_vars['--form-cta-btn-bg']   = 'var(--col-' . $btn_slug . ')';
	$card_vars['--form-cta-btn-text'] = $text_on( $btn_slug );
}

$card_style = '';
foreach ( $card_vars as $prop => $value ) {
	$card_style .= $prop . ':' . $value . ';';
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
