<?php
/**
 * Template for displaying the CRA tool results.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

ob_start();
get_header();
$header = ob_get_clean();
$header = preg_replace( '#<title>(.*?)<\/title>#', '<title>Change Readiness Assessment Results | Afiniti</title>', $header );
echo $header; // phpcs:ignore

$page_id = get_field( 'cra_tool_page_id', 'options' );
$data    = get_field( 'data' );
$scores  = get_field( 'scores' );
$levers  = array( 'Leadership', 'Drivers', 'Culture', 'Engagement', 'Capability', 'Method' );

// Both come back from a JSON payload stored by cra.php, so treat them as
// untrusted shapes rather than reading keys off them blind.
$data   = is_array( $data ) ? $data : array();
$scores = is_array( $scores ) ? $scores : array();

?>
<style>
    .results__grid {
        display: grid;
        gap: 1rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
        margin-bottom: 0.5rem;
    }

    @media (min-width:768px) {
        .results__grid {
            grid-template-columns: 2fr 1fr 6fr 3fr;
        }
    }

    .fa-ul {
        margin-left: 1.5rem;
    }

    .post-image-flag {
        position: absolute;
        top: 0;
        left: 0;
        background-color: var(--col-green-700);
        color: white;
        padding: 0.25rem 0.5rem;
        z-index: 9999;
        font-size: 0.8rem;
    }

    .slick-next::before, .slick-prev::before {
        color: var(--col-green-700) !important;
    }
    a[target=_blank]::after {
        content: "" !important;
    }
</style>
<main id="main">
    <section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
        <div class="hero__inner container-xl text-center">
            <h1><span>Change Readiness</span> Assessment</h1>
            <div class="hero__cta">
                <a class="btn btn--green" href="/contact-us/">Contact us</a>
            </div>
        </div>
    </section>
    <?php
    require get_stylesheet_directory() . '/page-templates/anim/business-change.php';
    ?>
    <div class="container-xl">
        <section class="contact mb-5">
            <div class="row bg--grey-700 p-4">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-sm-6 fw-bold">Company Name</div>
                        <div class="col-sm-6">
                            <?= esc_html( $data['orgName'] ?? '' ); ?>
                        </div>
                        <div class="col-sm-6 fw-bold">Date</div>
                        <div class="col-sm-6">
                            <?php // The date the assessment was taken, not today - this page is meant to be bookmarked and revisited. ?>
                            <?= esc_html( get_the_date( 'd M Y' ) ); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <ul class="fa-ul mt-2">
                        <li><span class="fa-li"><i class="fa-solid fa-map-pin"></i></span> <a
                                href="<?= esc_url( get_the_permalink() ); ?>"
                                class="text-white">Bookmark this link</a> for future reference.</li>
                        <li><span class="fa-li"><i class="fa-solid fa-envelope"></i></span> <a
                                href="mailto:?subject=Afiniti Change Readiness Assessment&body=<?= esc_url( get_the_permalink() ); ?>"
                                class="text-white">Share via email</a></li>
                        <li><span class="fa-li"><i class="fa-solid fa-star"></i></span> Found your results useful? Help others by <a
                                href="https://g.page/r/Cfn508DiV5pLEAI/review"
                                target="_blank"
                                class="text-white">leaving a review</a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="graphs mb-5">
            <h2>Graphical Results</h2>
            <div class="row">
                <div class="col-md-4">
                    <canvas id="radar"></canvas>
                </div>
                <div class="col-md-8">
                    <canvas id="chart"></canvas>
                </div>
            </div>
        </section>

        <section class="summary mb-5">
            <h2>Summary Assessment</h2>
            <div>
                <?php
                foreach ( $levers as $l ) {
                    $the_score = round( ( ( $scores[ $l ] ?? 0 ) / 30 ) * 100 );
                    $field     = strtolower( $l ) . '_analysis';
                    while ( have_rows( $field, $page_id ) ) {
                        the_row();
                        if ( $the_score >= get_sub_field( 'low_score' ) && $the_score <= get_sub_field( 'high_score' ) ) {
                            echo str_replace( array( '<p>', '</p>' ), '', apply_filters( 'the_content', get_sub_field( 'summary' ) ) ) . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                    }
                }
                ?>
            </div>
        </section>

        <section class="results mb-5">
            <h2>Detailed Results</h2>
            <div class="results__grid d-none d-md-grid">
                <div class="fw-bold">Lever</div>
                <div class="fw-bold">Score</div>
                <div class="fw-bold">Analysis</div>
                <div class="fw-bold">Recommended Action</div>
            </div>

            <?php
            $pcts = array();
            foreach ( $levers as $l ) {
                $the_score  = round( ( ( $scores[ $l ] ?? 0 ) / 30 ) * 100 );
                $pcts[ $l ] = $the_score;
                ?>
            <div class="results__grid">
                <div class="d-flex justify-content-between">
                    <div class="fw-bold"><?= esc_html( $l ); ?></div>
                    <div class="d-md-none fw-normal"><?= esc_html( $the_score ); ?>%
                    </div>
                </div>
                <div class="d-none d-md-block"><?= esc_html( $the_score ); ?>%</div>
                <?php
                $field   = strtolower( $l ) . '_analysis';
                $matched = false;
                while ( have_rows( $field, $page_id ) ) {
                    the_row();
                    if ( $the_score >= get_sub_field( 'low_score' ) && $the_score <= get_sub_field( 'high_score' ) ) {
                        $matched = true;
                        ?>
                <div>
                        <?= wp_kses_post( apply_filters( 'the_content', get_sub_field( 'analysis' ) ) ); ?>
                </div>
                <div>
                        <?= wp_kses_post( apply_filters( 'the_content', cb_list( get_sub_field( 'recommendations' ) ) ) ); ?>
                </div>
                        <?php
                    }
                }

                // No band covers this score - keep the four column grid intact
                // rather than letting the row collapse to two cells.
                if ( ! $matched ) {
                    echo '<div></div><div></div>';
                }
                ?>
            </div>
                <?php
            }
            ?>
    </section>

    <section>
        <div class="container-xl">
        This online version of the Afiniti 6Lever<sup>TM</sup> diagnostic tool provides a general overview of your change readiness strengths and weaknesses. Our consultants regularly conduct the full change readiness assessment across our clients' organisations, which allows them to deliver specific, tailored analysis and recommendations for your specific change projects, as well as help implementing these. Please <a href="/contact-us/">get in touch</a> if you'd like to know more.  
        </div>
    </section>

    <!-- latest_insights -->
    <section class="latest_news py-5">
        <div class="container">
            <h2 class="mb-4">Related <span>Insights</span></h2>
            <div class="slider mb-4">
                <?php
                asort($pcts);
                $keys = array_slice(array_keys($pcts), 0, 2);

                /*  two from lowest $keys[0] */
                /*  one from second lowest $keys[1] */
                /*  three of the latest */

                // $maxcount is the total across all three queries - two lever
                // posts, one for the second lever, then the latest to fill up.
                // This used to be 3 and $remaining was computed the wrong way
                // round ($postcount - $maxcount), so it was never above zero and
                // the third query never ran.
                $maxcount  = 6;
                $postcount = 0;
                $theIDs    = array();

                $lowest = new WP_Query(
                    array(
                        'post_type'      => 'post',
                        'posts_per_page' => 2,
                        'post_status'    => 'publish',
                        'tax_query'      => array( // phpcs:ignore
                            'relation' => 'AND',
                            array(
                                'taxonomy' => 'category',
                                'field'    => 'slug',
                                'terms'    => 'team-insight',
                                'operator' => 'NOT IN'
                            ),
                            array(
                                'taxonomy' => 'lever',
                                'field'    => 'name',
                                'terms'    => array( $keys[0] ),
                            )
                        ),
                    )
                );

                while ($lowest->have_posts()) {
                    $lowest->the_post();
                    $postcount++;
                    $theIDs[] = get_the_ID();

                    $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    if (!$img) {
                        $img = get_stylesheet_directory_uri() . '/img/default-blog.jpg';
                    }

    ?>
    <div class="slider__item insight px-3">
        <a href="<?=get_the_permalink()?>">
            <div class="post-image-container">
                <div class="post-image-flag"><?=$keys[0]?></div>
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

wp_reset_postdata();

$second = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => 1,
    'post_status' => 'publish',
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => 'team-insight',
            'operator' => 'NOT IN'
        ),
        array(
            'taxonomy' => 'lever',
            'field'    => 'name',
            'terms'    => array($keys[1]),
        )
    ),
));

while ($second->have_posts()) {
    $second->the_post();
    $postcount++;
    $theIDs[] = get_the_ID();

    $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
    if (!$img) {
        $img = get_stylesheet_directory_uri() . '/img/default-blog.jpg';
    }

    ?>
    <div class="slider__item insight px-3">
        <a href="<?=get_the_permalink()?>">
            <div class="post-image-container">
                <div class="post-image-flag"><?=$keys[1]?></div>
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

wp_reset_postdata();

$remaining = $maxcount - $postcount;

if ($remaining > 0) {

    $other = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $remaining,
        'post_status' => 'publish',
        'post__not_in' => $theIDs,
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'team-insight',
                'operator' => 'NOT IN'
            )
        ),
    ));

    while ($other->have_posts()) {
        $other->the_post();

        $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
        if (!$img) {
            $img = get_stylesheet_directory_uri() . '/img/default-blog.jpg';
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

    wp_reset_postdata();
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
    <script src="<?=get_stylesheet_directory_uri()?>/js/slick.min.js"></script>
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
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const bar = document.getElementById('chart');

    new Chart(bar, {
        type: 'bar',
        data: {
            labels: ['Leadership', 'Drivers', 'Culture', 'Engagement', 'Capability', 'Method'],
            datasets: [{
                    label: 'Your Score',
                    yAxisID: 'score',
                    data: [
                        <?=getPercentOfNumber($scores['Leadership'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Drivers'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Culture'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Engagement'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Capability'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Method'], 30)?>
                    ],
                    borderWidth: 1,
                    backgroundColor: "#f07d19",
                    pointBackgroundColor: "#f07d19",
                    pointBorderColor: "#f07d19",
                    pointHoverBackgroundColor: "#f07d19",
                    pointHoverBorderColor: "#f07d19"
                },
                {
                    label: 'Afiniti Change Index',
                    yAxisID: 'acr',
                    data: [90, 80, 70, 75, 85, 75],
                    borderWidth: 1,
                    backgroundColor: "#87bd75",
                    pointBackgroundColor: "#87bd75",
                    pointBorderColor: "#87bd75",
                    pointHoverBackgroundColor: "#87bd75",
                    pointHoverBorderColor: "#87bd75"
                }
            ]
        },
        options: {
            scales: {
                acr: {
                    display: false,
                    max: 100
                },
                score: {
                    type: 'linear',
                    position: 'left',
                    max: 100
                },
                rank: {
                    type: 'linear',
                    position: 'right',
                    ticks: {
                        // min: 0,
                        // max: 1
                        callback: function(value, index) {
                            // console.log(this.getLabelForValue(value));
                            if (this.getLabelForValue(index) == 1) {
                                return 'Immediate action';
                            } else if (this.getLabelForValue(index) == 5) {
                                return 'Some improvements';
                            } else if (this.getLabelForValue(index) == 9) {
                                return 'No action';
                            } else {
                                return;
                            }
                        }
                    }
                }
            }
        }
    });

    const radar = document.getElementById('radar');

    new Chart(radar, {
        type: 'radar',
        data: {
            labels: ['Leadership', 'Drivers', 'Culture', 'Engagement', 'Capability', 'Method'],
            datasets: [{
                    label: 'Actual',
                    data: [
                        <?=getPercentOfNumber($scores['Leadership'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Drivers'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Culture'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Engagement'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Capability'], 30)?>
                        ,
                        <?=getPercentOfNumber($scores['Method'], 30)?>
                    ],
                    backgroundColor: "#f07d1966",
                    pointBackgroundColor: "#f07d19",
                    pointBorderColor: "#f07d19",
                    pointHoverBackgroundColor: "#f07d19",
                    pointHoverBorderColor: "#f07d19"
                },
                {
                    label: 'Afiniti Change Index',
                    data: [90, 80, 70, 75, 85, 75],
                    backgroundColor: "#87bd7566",
                    pointBackgroundColor: "#87bd75",
                    pointBorderColor: "#87bd75",
                    pointHoverBackgroundColor: "#87bd75",
                    pointHoverBorderColor: "#87bd75"
                }
            ]
        },
        options: {
            elements: {
                line: {
                    borderWidth: 3
                }
            },
            scales: {
                r: {
                    min: 0,
                    max: 100
                }
            }
        }
    })
</script>
<?php

function getPercentOfNumber($number, $percent)
{
    return round(($number / $percent) * 100);
}


get_footer();
?>