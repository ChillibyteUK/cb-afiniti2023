<?php
$left_theme = strtolower(get_field('left_theme'));
$right_theme = strtolower(get_field('right_theme'));

$classes = $block['className'] ?? null;

?>
<!-- intro_block -->
<div class="container-xl intro_block pb-4 <?=$classes?>">
    <div class="row">
        <div class="col-lg-5">
            <h2 class="h1 text--<?=$left_theme?> fw-bold">
                <?=get_field('left_title')?>
            </h2>
            <div><?=get_field('left_content')?>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="row">
                <div class="d-none d-lg-block col-6 border-dash-right h-90px"></div>
                <div class="d-none d-lg-block col-6"></div>
                <div class="col-4 offset-4 col-lg-12 offset-lg-0 py-4 py-lg-0"><img
                        src="<?=get_stylesheet_directory_uri()?>/img/anim/Flag-bearer.png"
                        class="border-dash-circle"></div>
                <div class="d-none d-lg-block col-6 border-dash-right h-90px"></div>
                <div class="d-none d-lg-block col-6"></div>
            </div>
        </div>
        <div class="col-lg-5">
            <h2 class="h1 text--<?=$right_theme?> fw-bold">
                <?=get_field('right_title')?>
            </h2>
            <div><?=get_field('right_content')?>
            </div>
        </div>
    </div>
</div>