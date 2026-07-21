<!-- tab_divider -->
<?php
$colour = strtolower(get_field('title_colour'));
$parts = preg_split('/-/', $colour);
$colour = $parts[0];

if (get_field('position') == 'tab_right') {
    ?>
<div class="d-none d-lg-flex tab_divider mb-4">
    <div
        class="tab_divider__tab tab_divider__tab--right text--<?=$colour?>">
        <?=get_field('title')?>
    </div>
    <div class="tab_divider__middle tab_divider__middle--right"></div>
    <div class="tab_divider__image">
        <?=wp_get_attachment_image(get_field('image'), 'medium')?>
    </div>
</div>
<div class="d-lg-none">
    <h2 class="text--<?=$colour?>">
        <?=get_field('title')?>
    </h2>
</div>
<?php
} else {
    ?>
<div class="d-none d-lg-flex tab_divider mb-4">
    <div class="tab_divider__image">
        <?=wp_get_attachment_image(get_field('image'), 'medium')?>
    </div>
    <div class="tab_divider__middle tab_divider__middle--left"></div>
    <div
        class="tab_divider__tab tab_divider__tab--left text--<?=$colour?>">
        <?=get_field('title')?>
    </div>
</div>
<div class="d-lg-none">
    <h2 class="text--<?=$colour?>">
        <?=get_field('title')?>
    </h2>
</div>
<?php
}
?>