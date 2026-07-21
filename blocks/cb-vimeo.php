<?php
$embed_url = get_field('embed_url');
$width = get_field('width');
$colour = strtolower(get_field('background'));
$breakout = '';
if ($colour != '') {
    $breakout = 'breakout bg--' . $colour;
}

$classes = $block['className'] ?? null;

?>
<!-- vimeo -->
<section
    class="<?=$breakout?> py-4 <?=$classes?>">
    <div class="container-xl vimeo mx-auto <?=$width?>">
        <div class="ratio ratio-16x9">
            <iframe height=400 allowfullscreen="allowfullscreen"
                src="<?=$embed_url?>"></iframe>
        </div>
    </div>
</section>