<?php

$col1 = get_field('left');
$col2 = get_field('centre');
$col3 = get_field('right');

$row1_col1_style = '';
$row2_col1_style = '';
$row1_col2_style = '';
$row2_col2_style = '';
$row1_col3_style = '';
$row2_col3_style = '';

if ($col1 == 'down-across') {
    $row1_col1_style = 'border-dash-bottom-left h-30px';
    $row2_col1_style = '';
}
if ($col1 == 'up-across') {
    $row1_col1_style = '';
    $row2_col1_style = 'border-dash-top-left mt-minus-2 h-30px';
}


if ($col2 == 'across-down') {
    $row1_col2_style = '';
    $row2_col2_style = 'border-dash-top-right mt-minus-2 h-30px';
}
if ($col2 == 'down-across') {
    $row1_col2_style = 'border-dash-bottom-left h-30px';
    $row2_col2_style = '';
}
if ($col2 == 'up-across') {
    $row1_col2_style = '';
    $row2_col2_style = 'border-dash-top-left mt-minus-2 h-30px';
}
if ($col2 == 'across-up') {
    $row1_col2_style = 'border-dash-bottom-right h-30px';
    $row2_col2_style = '';
}
if ($col2 == 'across') {
    $row1_col2_style = 'border-dash-bottom h-30px';
    $row2_col2_style = '';
}


if ($col3 == 'across-up') {
    $row1_col3_style = 'border-dash-bottom-right h-30px';
    $row2_col3_style = '';
}
if ($col3 == 'across-down') {
    $row1_col3_style = '';
    $row2_col3_style = 'border-dash-top-right mt-minus-2 h-30px';
}


$classes = $block['className'] ?? null;

?>
<!-- divider -->
<div class="d-none d-lg-block container-xl <?=$classes?>">
    <div class="row justify-content-center mx-0">
        <div class="col-lg-8">
            <div class="divider-parent">
                <div class="row1-col1 <?=$row1_col1_style?>"> </div>
                <div class="row1-col2 <?=$row1_col2_style?>"> </div>
                <div class="row1-col3 <?=$row1_col3_style?>"> </div>
                <div class="row2-col1 <?=$row2_col1_style?>"> </div>
                <div class="row2-col2 <?=$row2_col2_style?>"> </div>
                <div class="row2-col3 <?=$row2_col3_style?>"> </div>
            </div>
        </div>
    </div>
</div>