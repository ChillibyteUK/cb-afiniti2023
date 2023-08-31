<?php
$theme = strtolower(get_field('theme'));
$front = is_front_page() ? 'front' : 'mb-4';
?>    
<!-- hero -->
<section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
    <div class="hero__inner container-xl text-center">
        <h1><?=get_field('title')?></h1>
        <?php
        if (get_field('content')) {
            ?>  
        <div class="hero__content fs-5 mb-4"><?=get_field('content')?></div>
            <?php
        }
        ?>
        <div class="hero__cta">
            <?php
        if (get_field('cta1')) {
            $cta = get_field('cta1');
            $colour = strtolower(get_field('cta1_colour'));
            ?>
            <a class="btn btn--<?=$colour?>" href="<?=$cta['url']?>" target="<?=$cta['target']?>"><?=$cta['title']?></a>
            <?php
        }
        if (get_field('cta2')) {
            $cta = get_field('cta2');
            $colour = strtolower(get_field('cta2_colour'));
            ?>
            <a class="btn btn--<?=$colour?>" href="<?=$cta['url']?>" target="<?=$cta['target']?>"><?=$cta['title']?></a>
            <?php
        }
        ?>
        </div>
    </div>
</section>
