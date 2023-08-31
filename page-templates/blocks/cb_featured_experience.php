<?php
$classes = $block['className'] ?? null;
?>
<!-- featured_experience -->
<div class="container-xl pb-5 <?=$classes?>">
    <div class="row">
        <div class="col-lg-4">
            <h2 class="h2 mb-4">Featured Story</h2>
            <?php

// featured

$featured = new WP_Query(
    array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key'		=> 'featured',
                'value'	    => 'Yes',
                'compare'   => 'LIKE'
            )
        )
    )
);

if ($featured->have_posts()) {
    echo '<div class="row">';
    while ($featured->have_posts()) {
        $featured->the_post();
        $thumb_id = get_post_thumbnail_id();
        $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
        $thumb_url = $thumb_url_array[0];
        ?>
            <div class="col-12 insight insight--short">
                <a href="<?=get_the_permalink()?>">
                    <div class="featured-image-container">
                        <div class="featured-image mb-2"
                            style="background-image:url('<?=$thumb_url?>')">
                            <div class="img-overlay">
                                <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="text--green fw-bold my-2">Afiniti News</div>
                    <div class="article-title"><?=get_the_title()?>
                    </div>
                    <div class="fw-bold py-2 arrow-link">
                        <div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
                    </div>
                </a>
            </div>
            <?php
    }
    echo '</div>';
} else {
    echo 'NO FEATURED STORY';
}
?>
        </div>
        <div class="col-lg-8">
            <h2 class="h2 mb-4">Our Experience</h2>
            <?php

$the_query = new WP_Query(
    array(
            'post_type' => 'case-studies',
            'posts_per_page' => 4,
            'post_status' => 'publish',
        )
);


if ($the_query->have_posts()) {
    echo '<div class="row g-4">';
    while ($the_query->have_posts()) {
        $the_query->the_post();
        $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
        ?>
            <div class="col-12 col-lg-6 insight insight--short">
                <a href="<?=get_the_permalink()?>">
                    <div class="post-image-container">
                        <div class="post-image mb-2"
                            style="background-image:url('<?=$img?>')">
                            <div class="img-overlay">
                                <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="text--green fw-bold my-2">Case Study</div>
                    <div class="article-title mt-2">
                        <?=get_the_title()?>
                    </div>
                    <div class="fw-bold py-2 arrow-link">
                        <div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
                    </div>
                </a>
            </div>
            <?php
    }
    wp_reset_postdata();
    echo '</div>';
}
?>
        </div>
    </div>
</div>