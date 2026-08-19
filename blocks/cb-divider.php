<?php
/**
 * Block template for CB Divider.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$col1 = get_field( 'left' );
$col2 = get_field( 'centre' );
$col3 = get_field( 'right' );

$row1_col1_style = '';
$row2_col1_style = '';
$row1_col2_style = '';
$row2_col2_style = '';
$row1_col3_style = '';
$row2_col3_style = '';

if ( 'down-across' === $col1 ) {
	$row1_col1_style = 'border-dash-bottom-left h-30px';
	$row2_col1_style = '';
}
if ( 'up-across' === $col1 ) {
	$row1_col1_style = '';
	$row2_col1_style = 'border-dash-top-left mt-minus-2 h-30px';
}


if ( 'across-down' === $col2 ) {
	$row1_col2_style = '';
	$row2_col2_style = 'border-dash-top-right mt-minus-2 h-30px';
}
if ( 'down-across' === $col2 ) {
	$row1_col2_style = 'border-dash-bottom-left h-30px';
	$row2_col2_style = '';
}
if ( 'up-across' === $col2 ) {
	$row1_col2_style = '';
	$row2_col2_style = 'border-dash-top-left mt-minus-2 h-30px';
}
if ( 'across-up' === $col2 ) {
	$row1_col2_style = 'border-dash-bottom-right h-30px';
	$row2_col2_style = '';
}
if ( 'across' === $col2 ) {
	$row1_col2_style = 'border-dash-bottom h-30px';
	$row2_col2_style = '';
}

if ( 'vertical-centre' === $col2 ) {
	$row1_col2_style = 'border-dash-centre-vertical h-30px';
	$row2_col2_style = 'border-dash-centre-vertical mt-minus-2 h-30px';
}
if ( 'across-centre-down' === $col2 ) {
	$row1_col2_style = '';
	$row2_col2_style = 'border-dash-top-centre mt-minus-2 h-30px';
}
if ( 'down-centre-across' === $col2 ) {
	$row1_col2_style = 'border-dash-centre-bottom h-30px';
	$row2_col2_style = '';
}
if ( 'across-centre-up' === $col2 ) {
	$row1_col2_style = 'border-dash-bottom-centre h-30px';
	$row2_col2_style = '';
}
if ( 'up-centre-across' === $col2 ) {
	$row1_col2_style = '';
	$row2_col2_style = 'border-dash-centre-top mt-minus-2 h-30px';
}


if ( 'across-up' === $col3 ) {
	$row1_col3_style = 'border-dash-bottom-right h-30px';
	$row2_col3_style = '';
}
if ( 'across-down' === $col3 ) {
	$row1_col3_style = '';
	$row2_col3_style = 'border-dash-top-right mt-minus-2 h-30px';
}


$classes = $block['className'] ?? null;

?>
<!-- divider -->
<div class="d-none d-lg-block container-xl <?= esc_attr( $classes ); ?>">
	<div class="row justify-content-center mx-0">
		<div class="col-lg-8">
			<div class="divider-parent">
				<div class="row1-col1 <?= esc_attr( $row1_col1_style ); ?>"> </div>
				<div class="row1-col2 <?= esc_attr( $row1_col2_style ); ?>"> </div>
				<div class="row1-col3 <?= esc_attr( $row1_col3_style ); ?>"> </div>
				<div class="row2-col1 <?= esc_attr( $row2_col1_style ); ?>"> </div>
				<div class="row2-col2 <?= esc_attr( $row2_col2_style ); ?>"> </div>
				<div class="row2-col3 <?= esc_attr( $row2_col3_style ); ?>"> </div>
			</div>
		</div>
	</div>
</div>