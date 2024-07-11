<?php

$category = get_field('hub_category');
$term = get_term($category[0], 'hub-category');

$q = new WP_Query(array(
    'post_type' => array('post', 'case-studies'),
    'posts_per_page' => 5,
    'tax_query' => array(
        array(
            'taxonomy' => 'hub-category', // Your custom taxonomy
            'field'    => 'term_id', // Query by term IDs
            'terms'    => $category, // Pass the array of term IDs
        ),
    ),
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish'
));

?>
<section class="hub_insights">
    <div class="container-xl pb-5">
        <h2 class="mb-4">Latest <?=$term->name?> Insights and Case Studies</h2>
        <div class="hub_insights__grid">
            <?php
            while ($q->have_posts()) {
                $q->the_post();
                $t = get_post_type(get_the_ID());
                $o = get_post_type_object($t);
                $n = $o->labels->singular_name;
                $n = $n == 'Post' ? 'Insight' : 'Case Study';
                ?>
            <a class="hub_insights__card" href="<?=get_the_permalink()?>">
                <div class="hub_insights__img">
                    <?=get_the_post_thumbnail(get_the_ID(),'large', array('class' => 'hub_insights__image'))?>
                    <div class="img-overlay">
                        <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                    </div>
                </div>
                <div class="hub_insights__inner">
                    <div class="text--green fw-bold my-2"><?=$n?></div>
                    <div class="fw-bold"><?=get_the_title()?></div>
                    <div class="fw-bold py-2 arrow-link">
                        <div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
                    </div>
                </div>
            </a>
                <?php
            }
            ?>
        </div>
    </div>
</section>