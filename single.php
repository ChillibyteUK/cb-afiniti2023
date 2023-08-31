<?php
/**
 * The template for displaying all single posts
 *
 * @package cb-carousel
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$bg = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : wp_get_attachment_image_url( get_field('hero_image','options'), 'full' );

$date = get_the_date('jS F, Y');
$categories = get_the_category();
$type;
if ( ! empty( $categories ) ) {
	$type = esc_html( $categories[0]->name );	
}
?>
<main id="main">
<!-- hero -->
<section id="hero" class="hero d-flex align-items-center hero--default">
    <div class="overlay"></div>
    <div class="hero__inner container-xl">
        <div class="hero__content d-flex flex-column justify-content-center order-2 order-lg-1 pt-5"
                data-aos="fade">
            <div class="h1 text-white text-center">Afiniti <span>Insights</span></div>
        </div>
    </div>
</section>
<?php
$anim = 'insights';
include get_stylesheet_directory() . '/page-templates/anim/' . $anim . '.php';
?>
<section class="breadcrumbs container-xl">
    <?php
    if (function_exists('yoast_breadcrumb')) {
        yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
    }
    ?>
</section>
<div class="container-xl pb-5">
    <h1 class="fw-bold mb-4"><?=get_the_title()?></h1>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="single_insight_image mb-4" style="background-image:url(<?=get_the_post_thumbnail_url( get_the_ID(), 'full' )?>)"></div>
            <?php the_content(); ?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="share-icons">Share: 
                        <?php
                            $url = get_permalink();
                        ?>
                        <a target="_blank" href="mailto:?subject=I%27d%20like%20to%20share%20a%20link%20with%20you&body=<?=$url?>"><i class="fa-solid fa-envelope"></i></a>
                        <a target="_blank" href="https://twitter.com/share?url=<?=$url?>"><i class="fa-brands fa-twitter"></i></a>
                        <a target="_blank" href="http://www.facebook.com/sharer.php?u=<?=$url?>"><i class="fa-brands fa-facebook-f"></i></a>
                        <a target="_blank" href="http://www.linkedin.com/shareArticle?url=<?=$url?>"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <?php
            if (get_field('person')) {
                $p = get_field('person')[0];
                ?>
            <div class="author_block mt-4">
                <div class="row g-4">
                    <div class="col-md-2 text-center">
                        <img src="<?=wp_get_attachment_image_url(get_field('photo',$p),'large')?>" alt="<?=get_the_title($p)?>" class="w-75 w-md-100">
                    </div>
                    <div class="col-md-10">
                        <div class="author-title">
                            <div class="author-name">
                                <div class="h2 mb-0"><?=get_the_title($p)?></div>
                                <div class="title mb-2"><?=get_field('job_title',$p)?></div>
                            </div>
                            <div class="author-social">
                                <?php
                                if (get_field('linkedin_url',$p)) {
                                    ?>
                                    <a href="<?=get_field('linkedin_url',$p)?>" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                                    <?php
                                }
                                if (get_field('twitter_url',$p)) {
                                    ?>
                                    <a href="<?=get_field('twitter_url',$p)?>" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="bio">
                            <?php
                            $blocks = parse_blocks( get_the_content(null, false, $p) );
                            $first = render_block( $blocks[0] );
                            $str = strip_tags($first);
                            echo $str;
                            ?>
                        </div>
                        <div class="fw-bold py-2 arrow-link"><a href="<?=get_the_permalink($p)?>" class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></a></div>
                    </div>
                </div>
            </div>
                <?php
            }
            ?>
        </div>
        <div class="col-lg-4">
            <?=single_sidebar()?>
        </div>
    </div>
    <?php
    $r = new WP_Query(array(
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'name',
                'terms'    => $type,
                ),
            ),
    ));
    if ($r->have_posts()) {
        ?>
        <hr class="my-4">
        <h2 class="mb-4">Related <span>Insights</span></h2>
        <div class="row g-4">
            <?php
        while ($r->have_posts()) {
            $r->the_post();
            $img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
            ?>
            <div class="insight col-12 col-lg-4 mb-4">
                <a href="<?=get_the_permalink()?>">
                    <div class="post-image-container">
                        <div class="post-image mb-2" style="background-image:url('<?=$img?>')">
                            <div class="img-overlay">
                                <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="article-title mt-2"><?=get_the_title()?></div>
                    <div class="article-excerpt"><?=wp_trim_words(get_the_content(), 20)?></div>
                    <div class="fw-bold py-2 arrow-link"><div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div></div>
                </a>
            </div>
            <?php
        }
        echo '</div>';
    }
    ?>
</div>
<?php
/*
global $post;
$author_id = $post->post_author;
$authorID = get_the_author_meta('ID',$author_id);
$authPerson = get_field('person', 'user_'.$authorID)[0];
if ($authPerson) {
    $authName = get_the_title($authPerson);
    $authRole = get_field('role',$authPerson);
    $authBio = get_field('bio',$authPerson);
    $authImage = get_the_post_thumbnail_url($authPerson,'thumbnail');
    $authLinkedIn = get_field('linkedin',$authPerson);
    ?>
<section class="author">
    <div class="container-xl">
        <div class="content">
            <div class="row">
                <div class="col-sm-2 text-center mb-4">
                    <img src="<?=$authImage?>" alt="<?=$authName?>" class="rounded-circle">
                </div>
                <div class="col-sm-10">
                    <div class="d-flex justify-content-between">
                        <h3><?=$authName?></h3>
                        <?php
                        if ($authLinkedIn) {
                            echo '<a href="' . $authLinkedIn . '" target="_blank"><i class="fs-5 fa-brands fa-linkedin-in"></i></a>';
                        }
                        ?>
                    </div>
                    <h4 class="fs-5"><?=$authRole?></h4>
                    <div class="fs-7"><?=$authBio?></div>
                </div>
            </div>
        </div>
    </div>
</section>
    <?php
}
*/
?>
</main>
<?php
get_footer();
