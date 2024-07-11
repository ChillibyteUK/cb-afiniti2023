<?php
$people = get_field('people');

?>
<section class="people_cta py-5">
    <div class="container-xl">
        <?php
        if (get_field('title') ?? null) {
            ?>
        <div class="h2 mb-4 d-md-none"><?=get_field('title')?></div>
            <?php
        }
        ?>
        <div class="people_cta__grid">
            <div class="people_cta__slider swiper">
                <div class="swiper-wrapper">
                <?php
                foreach ($people as $person) {
                    ?>
                    <div class="swiper-slide">
                        <div class="people_cta__inner">
                        <?=wp_get_attachment_image(get_field('photo',$person),'medium', false, array('class' => 'people_cta__image'))?>
                        <div class="fw-bold"><?=get_the_title($person)?></div>
                        <?=get_field('job_title',$person)?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                </div>
            </div>
            <div class="people_cta__content my-auto">
                <?php
                if (get_field('title') ?? null) {
                    ?>
                <h2 class="mb-4 d-none d-md-block"><?=get_field('title')?></h2>
                    <?php
                }
                if (get_field('content') ?? null) {
                    ?>
                <div class="mb-4"><?=get_field('content')?></div>
                    <?php
                }
                if (get_field('cta') ?? null) {
                    $cta = get_field('cta');
                    ?>
                <a class="btn btn--green"
                href="<?=$cta['url']?>"
                target="<?=$cta['target']?>"><?=$cta['title']?></a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</section>
<?php
add_action('wp_footer', function(){
    ?>
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: {
                delay: 4000,
            },
            speed: 600,
            spaceBetween: 100,
        });
    });
</script>
    <?php
});