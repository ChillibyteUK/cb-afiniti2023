<?php
$colour = strtolower(get_field('background')) ?? null;
$background = 'bg--' . $colour;

$featuredVideo = get_field('video_id',get_field('featured_video'));
$featuredImg = get_vimeo_data_from_id($featuredVideo,'thumbnail_url');

?>
<section class="video_feature py-4">
    <div class="container-xl <?=$background?> p-4">
        <div class="row">
            <div class="col-md-6">
                <img src="<?=$featuredImg?>">
            </div>
            <div class="col-md-6">
                <h2><?=get_field('feature_title')?></h2>
                <p><?=get_field('feature_description')?></p>
                <a href="<?=get_the_permalink(get_field('featured_video'))?>" class="btn btn-primary">Watch now</a>
            </div>
        </div>
        <?php
        if (get_field('videos') ?? null) {
            if (get_field('additional_videos_title') ?? null) {
                ?>
                <h2 class="h3 text-center mt-4 mb-0"><?=get_field('additional_videos_title')?></h2>
                <?php
            }
            ?>
        <div class="video_feature__slider pt-4">
            <?php            
foreach (get_field('videos') as $video) {
    $img = get_vimeo_data_from_id(get_field('video_id',$video),'thumbnail_url');
    ?>
    <div class="insight insight--short p-2">
            <a href="<?=get_the_permalink()?>">
                <div class="post-image-container">
                    <div class="post-image mb-2"
                        style="background-image:url('<?=$img?>')">
                        <div class="img-overlay">
                            <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                        </div>
                    </div>
                </div>
                <div class="article-title mt-2">
                    <?=get_the_title($video)?>
                </div>
            </a>
        </div>
    <?php
}
            ?>
        </div>
            <?php
        }
        ?>
    </div>
</section>
<?php
add_action('wp_footer', function () {
    ?>
<script src="<?=get_stylesheet_directory_uri()?>/js/slick.min.js"></script>
<script>
    jQuery(function($) {
        $('.video_feature__slider').slick({
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            autoplay: false,
            autoplaySpeed: 4000,
            arrows: false,
            responsive: [{
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        autoplay: true
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true
                    }
                }
            ]
        });
    });
</script>
<?php
}, 9999);
?>

