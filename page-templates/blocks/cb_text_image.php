<?php
$colour = strtolower(get_field('theme'));
$parts = preg_split('/-/', $colour);
$colour = $parts[0];
$breakout = '';
$background = '';
if ($colour != '') {
    $background = 'bg--' . $colour;
}
if (get_field('breakout')[0] ?? null && get_field('breakout')[0] == 'Yes') {
    $breakout = 'bg--' . $colour;
    $background = '';
}

$splitText = 'col-md-6';
$splitImage = 'col-md-6';

if (get_field('split') == '6040') {
    $splitText = 'col-md-8';
    $splitImage = 'col-md-4';
}
if (get_field('split') == '7030') {
    $splitText = 'col-md-10';
    $splitImage = 'col-md-2';
}

$orderText = 'order-2 order-md-1';
$orderImage = 'order-1 order-md-2';

if (get_field('order') == 'image-text') {
    $orderText = 'order-2 order-md-2';
    $orderImage = 'order-1 order-md-1';
}

$classes = $block['className'] ?? null;

?>
<!-- text_image -->
<section
    class="text_image <?=$breakout?> <?=$classes?>">
    <div class="container-xl <?=$background?> py-4">
        <h2 class="d-md-none">
            <?=get_field('title')?>
        </h2>
        <div class="row align-items-center">
            <div
                class="<?=$splitText?> <?=$orderText?>">
                <h2 class="d-none d-md-block">
                    <?=get_field('title')?>
                </h2>
                <div><?=get_field('content')?>
                </div>
            </div>
            <div
                class="<?=$splitImage?> <?=$orderImage?> text-center">
                <?=wp_get_attachment_image(get_field('image'), 'large', null, array('class' => 'wow'))?>
            </div>
        </div>
    </div>
</section>
<?php
    if (get_field('cta')) {
        $cta = get_field('cta');
        ?>
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-4 text-center halfcircle-container">
            <div
                class="div-rounded ss-halfcircle halfcircle-<?=$colour?>">
                <div class="halfcircle-content fw-bold">
                    <a class="anim-arrow--pulse"
                        href="<?=$cta['url']?>"><?=$cta['title']?>
                        <span class="arrow mt-2"></span></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    }
?>