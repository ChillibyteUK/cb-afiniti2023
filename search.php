<?php
/**
 * The template for displaying search results pages
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$anim = 'insights';
?>
<main id="main">
    <!-- hero -->
    <section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
        <div class="hero__inner container-xl text-center">
            <?php
            if (isset($_GET['post_type'])) {
                $type = $_GET['post_type'];
                if ($type == 'post') {
                    echo '<div class="h1">Key <span>Insights from Afiniti</span></div>';
                } elseif ($type == 'case-studies') {
                    echo '<div class="h1">Business Change <span>Case Studies</span></div>';
                    $anim = 'case-studies';
                }
            } else {
                echo '<div class="h1">Afiniti <span>Search</span></div>';
            }
?>
        </div>
    </section>
    <?php

include get_stylesheet_directory() . '/page-templates/anim/' . $anim . '.php';

$page_for_posts = get_option('page_for_posts');
?>

    <div class="container-xl py-5">
        <?php
        if (have_posts()) {
            ?>
        <h1><?php
                printf(
                esc_html__('Search Results for: %s', 'understrap'),
                '<span>' . get_search_query() . '</span>'
            );
            ?></h1>
        <p>Found <?=$wp_query->found_posts?>
            <?=ngettext('result', 'results', $wp_query->found_posts)?>.
        </p>
        <hr>
        <?php
        if (function_exists('relevanssi_didyoumean')) {
            relevanssi_didyoumean(
                get_search_query(false),
                '<p>Did you mean: ',
                '</p>',
                5
            );
        }
            while (have_posts()) {
                the_post();
                $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                if (!$img) {
                    $img = get_stylesheet_directory_uri() . '/img/default.png';
                }

                $flash = '';
                $types = get_the_terms($post->ID, 'insight-type');
                if ($types) {
                    switch($types[0]->slug) {
                        case 'article':
                            $flash = '<i class="fa-solid fa-newspaper"></i>';
                            break;
                        case 'video':
                            $flash = '<i class="fa-solid fa-video"></i>';
                            break;
                        case 'podcast':
                            $flash = '<i class="fa-solid fa-podcast"></i>';
                            break;
                        default:
                            $flash = '<i class="fa-solid fa-question"></i>';
                    }
                }
                ?>
    <div class="insight">
        <a href="<?=get_the_permalink()?>" class="searchResult">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="post-image-container">
                        <div class="post-image mb-2"
                            style="background-image:url('<?=$img?>')">
                            <div class="img-overlay">
                                <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                            </div>
                        </div>
                        <?php
                        if ($flash ?? null) {
                            echo '<div class="flash">' . $flash . '</div>';
                        }
                ?>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="article-title"><?=get_the_title()?>
                    </div>
                    <div class="article-excerpt">
                        <?php the_excerpt() ?>
                    </div>
                    <div class="fw-bold py-2 arrow-link">
                        <div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
        <?php
            }
        } else {
            get_template_part('loop-templates/content', 'none');
        }
?>
        <?php understrap_pagination(); ?>
    </div>
</main>
<?php

get_footer();
?>