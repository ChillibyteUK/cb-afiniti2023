<!-- related_case_studies -->
<?php
$cs = get_field('case_studies');

$r = new WP_Query(array(
    'post_type' => 'case-studies',
    'posts_per_page' => 3,
    'post__in' => $cs
));

$classes = $block['className'] ?? null;

if ($r->have_posts()) {
    ?>
<div class="container-xl py-4 <?=$classes?>">
    <h2 class="mb-4">Related <span>Case Studies</span></h2>
    <div class="row g-4">
        <?php
    while ($r->have_posts()) {
        $r->the_post();
        $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
        ?>
        <div class="insight col-12 col-lg-4 mb-4">
            <a href="<?=get_the_permalink()?>">
                <div class="post-image-container">
                    <div class="post-image mb-2"
                        style="background-image:url('<?=$img?>')">
                        <div class="img-overlay">
                            <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                        </div>
                    </div>
                </div>
                <div class="article-title mt-2"><?=get_the_title()?>
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
    echo '</div>';
    echo '</div>';
}
?>