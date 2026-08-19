<?php
/**
 * Block template for CB Tab Divider.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$colour = strtolower( get_field( 'title_colour' ) );
$parts  = preg_split( '/-/', $colour );
$colour = $parts[0];

if ( 'tab_right' === get_field( 'position' ) ) {
	?>
<div class="d-none d-lg-flex tab_divider mb-4">
	<div
		class="tab_divider__tab tab_divider__tab--right text--<?= esc_attr( $colour ); ?>">
		<?= wp_kses_post( get_field( 'title' ) ); ?>
	</div>
	<div class="tab_divider__middle tab_divider__middle--right"></div>
	<div class="tab_divider__image">
		<?= wp_get_attachment_image( get_field( 'image' ), 'medium' ); ?>
	</div>
</div>
<div class="d-lg-none">
	<h2 class="text--<?= esc_attr( $colour ); ?>">
		<?= wp_kses_post( get_field( 'title' ) ); ?>
	</h2>
</div>
	<?php
} else {
	?>
<div class="d-none d-lg-flex tab_divider mb-4">
	<div class="tab_divider__image">
		<?= wp_get_attachment_image( get_field( 'image' ), 'medium' ); ?>
	</div>
	<div class="tab_divider__middle tab_divider__middle--left"></div>
	<div
		class="tab_divider__tab tab_divider__tab--left text--<?= esc_attr( $colour ); ?>">
		<?= wp_kses_post( get_field( 'title' ) ); ?>
	</div>
</div>
<div class="d-lg-none">
	<h2 class="text--<?= esc_attr( $colour ); ?>">
		<?= wp_kses_post( get_field( 'title' ) ); ?>
	</h2>
</div>
	<?php
}
?>