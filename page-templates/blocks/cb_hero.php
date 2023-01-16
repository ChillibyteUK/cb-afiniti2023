<?php
$bg = wp_get_attachment_image_url( get_field('hero_image'), 'full' ) ?: '/wp-content/uploads/2022/11/placeholder-800x600-1.png';
$title = get_field('title') ? get_field('title') : get_the_title();
$theme = strtolower(get_field('theme'));
$front = is_front_page() ? 'front' : 'mb-4';
?>    
<!-- hero -->
<section id="hero" class="hero d-flex align-items-center hero--<?=$theme?> <?=$front?>">
    <div class="overlay"></div>
    <div class="hero__inner container-xl">
        <div class="row h-100">
            <div class="col-lg-6 hero__content d-flex flex-column justify-content-center order-2 order-lg-1 py-5" data-aos="fade">
                <h1 class="mb-4"><?=$title?></h1>
                <?php
                if (get_field('content')) {
                    ?>
                <div class="hero__content fs-5 mb-4"><?=get_field('content')?></div>
                    <?php
                }
                if (get_field('cta')) {
                    $cta = get_field('cta');
                    ?>
                <div class="hero__cta">
                    <a class="btn btn-primary" href="<?=$cta['url']?>" target="<?=$cta['target']?>"><?=$cta['title']?></a>
                </div>
                    <?php
                }
                ?>
            </div>
            <div class="col-lg-6 hero__image order-1 order-lg-2" style="background-image:url(<?=$bg?>)">
            </div>
        </div>
    </div>
    <div class="overlay--bottom"></div>
</section>
