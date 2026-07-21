<?php
$cta = get_field('link');
$colour = get_field('background') ?? null;
$background = '';
if ($colour) {
    $background = 'bg--' . $colour;
}

?>
<section class="simple_cta">
    <div class="container">
        <div class="simple_cta__content p-4 <?= esc_attr( $background ); ?>">
            <h2><?= esc_html( the_field('title') ); ?></h2>
            <p><?= wp_kses_post( the_field('content') ); ?></p>
        </div>
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
</section>