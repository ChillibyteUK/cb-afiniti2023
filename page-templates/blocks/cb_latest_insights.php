<?php
$classes = $block['className'] ?? null;
?>
<!-- latest_insights -->
<section class="latest_news py-5 <?=$classes?>">
    <div class="container">
        <h2 class="mb-4">Latest <span>Insights</span></h2>
        <div class="slider mb-4">
            <?php
            $q = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => '6',
                'post_status' => 'publish',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => 'team-insight',
                        'operator' => 'NOT IN'
                    )
                ),
            ));
while ($q->have_posts()) {
    $q->the_post();

    $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
    if (!$img) {
        $img = get_stylesheet_directory_uri() . '/img/default-blog.jpg';
    }

    $types = get_the_terms($q->ID, 'insight-type');
    $type_slugs = wp_list_pluck($types, 'slug');
    if (!empty($types) && !is_wp_error($types)) {
        if (in_array('video', $type_slugs, true)) {
            $img = get_vimeo_data_from_id(get_field('video_id',$post->ID),'thumbnail_url');
        }
    }

    ?>
            <div class="slider__item insight px-3">
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
                        <?=get_the_title()?>
                    </div>
                    <div class="article-excerpt">
                        <?=wp_trim_words(get_the_content(), 20)?>
                    </div>
                    <div class="fw-bold py-2 arrow-link">
                        <div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
                    </div>
                </a>
            </div>
            <?php
}
?>
        </div>
        <div class="text-center"><a href="/insights/" class="btn btn--green">Read more</a></div>
    </div>
</section>
<?php
add_action('wp_footer', function () {
    ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"
    integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"
    integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
    integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $('.slider').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 4000,
        dots: false,
        arrows: true,
        responsive: [{
                breakpoint: 992,
                settings: {
                    arrows: false,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                },
            },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                },
            }
        ]
    });
</script>
<?php
}, 9999);
?>