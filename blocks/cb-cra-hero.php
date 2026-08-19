<?php
/**
 * Block Name: CB CRA Hero
 *
 * Renders both heroes used by the CRA Tool template: the primary hero shown on
 * the intro screen, and a compact hero shown while the user is in the form
 * steps. The CRA Tool template lifts this block out of the_content() so the
 * compact hero can stay visible once the intro is hidden.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$eyebrow  = get_field( 'eyebrow' );
$btitle   = get_field( 'title' );
$subtitle = get_field( 'subtitle' );
$intro    = get_field( 'intro' );

$start_label = get_field( 'start_label' );
$sample      = get_field( 'sample_report' );

$form_title = get_field( 'form_title' );
$form_text  = get_field( 'form_text' );

if ( ! $start_label ) {
	$start_label = __( 'Get Started', 'cb-afiniti2023' );
}

// The link field can come back as an array, a plain URL string, or empty
// depending on the field's return format, so normalise it before use.
$sample_url    = '';
$sample_title  = '';
$sample_target = '';

if ( is_array( $sample ) ) {
	$sample_url    = $sample['url'] ?? '';
	$sample_title  = $sample['title'] ?? '';
	$sample_target = $sample['target'] ?? '';
} elseif ( is_string( $sample ) ) {
	$sample_url = $sample;
}

if ( $sample_url && ! $sample_title ) {
	$sample_title = __( 'View a sample report', 'cb-afiniti2023' );
}

// In the editor both heroes are shown stacked so the client can see and edit
// each one; on the front end the compact hero is revealed by cra.js.
$is_editor = ! empty( $is_preview );

?>
<?php // Display is set in CSS, not with d-flex - that is !important and cannot be toggled from JS. ?>
<section id="hero"
	class="hero cra-hero cra-hero--primary align-items-start pt-lg-0 align-items-lg-center">
	<div class="hero__inner container-xl text-center">
		<?php if ( $eyebrow ) { ?>
		<p class="cra-hero__eyebrow"><?= esc_html( $eyebrow ); ?></p>
		<?php } ?>
		<?php if ( $btitle ) { ?>
		<h1 class="mb-3"><?= wp_kses_post( $btitle ); ?></h1>
		<?php } ?>
		<?php if ( $subtitle ) { ?>
		<p class="cra-hero__subtitle"><?= wp_kses_post( $subtitle ); ?></p>
		<?php } ?>
		<?php if ( $intro ) { ?>
		<div class="cra-hero__intro"><?= wp_kses_post( $intro ); ?></div>
		<?php } ?>
		<div class="hero__cta">
			<button id="step0" class="btn btn-lg btn--orange"><?= esc_html( $start_label ); ?></button>
			<?php if ( $sample_url ) { ?>
			<a class="btn btn-lg btn--ghost" href="<?= esc_url( $sample_url ); ?>"
				<?php
				if ( $sample_target ) {
					?>
					target="<?= esc_attr( $sample_target ); ?>" rel="noopener"<?php } ?>><?= esc_html( $sample_title ); ?></a>
			<?php } ?>
		</div>
	</div>
</section>
<?php if ( $form_title || $form_text || $is_editor ) { ?>
	<?php // No d-flex here - it is display:flex !important, which would override the hidden state. ?>
<section id="cra-form-hero"
	class="hero cra-hero cra-hero--compact<?= $is_editor ? ' cra-hero--editor' : ''; ?>">
	<div class="hero__inner container-xl text-center">
		<?php if ( $form_title ) { ?>
		<p class="cra-hero__form-title h1"><?= wp_kses_post( $form_title ); ?></p>
		<?php } ?>
		<?php if ( $form_text ) { ?>
		<p class="cra-hero__form-text mb-0"><?= wp_kses_post( $form_text ); ?></p>
		<?php } ?>
	</div>
</section>
<?php } ?>
