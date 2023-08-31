<?php
$colour = 'text--' . strtolower(get_field('title_colour'));

$classes = $block['className'] ?? null;

?>
<!-- image_divider -->
<section class="image_divider pt-5 <?=$classes?>">
    <div class="container-xl">
        <div class="row">
            <div class="col-12 text-center"><img
                    src="<?=wp_get_attachment_image_url(get_field('image'), 'large')?>"
                    class="border-dash-circle image_divider__icon"></div>
            <div class="col-6 border-dash-top-left image_divider__h-60"></div>
            <div class="col-6 border-dash-top-right image_divider__h-60"></div>
            <div class="col-12 image_divider__title">
                <h2 class="h3 fw-bold <?=$colour?>">
                    <?=get_field('title')?>
                </h2>
            </div>
        </div>
    </div>
</section>