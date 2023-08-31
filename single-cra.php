<?php

$pageID = 1281;
$data = get_field('data');
$scores = get_field('scores');


ob_start();
get_header();
$header = ob_get_clean();
$header = preg_replace('#<title>(.*?)<\/title>#', '<title>Change Readiness Assessment Results | Afiniti</title>', $header);
echo $header;

$pageID = get_field('cra_tool_page_id', 'options');
$data = get_field('data');
$scores = get_field('scores');

$levers = array('Leadership','Drivers','Culture','Engagement','Capability','Method');

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
include get_stylesheet_directory() . '/page-templates/anim/business-change.php';
?>
    <!--
<?=cbdump($data)?>
    <?=cbdump($scores)?>
    -->

    <div class="container-xl">
        <section class="contact mb-5">
            <div class="row bg--grey-700 p-4">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-sm-6 fw-bold">Company Name</div>
                        <div class="col-sm-6">
                            <?=$data['orgName']?>
                        </div>
                        <div class="col-sm-6 fw-bold">Contact Name</div>
                        <div class="col-sm-6">
                            <?=$data['contactName']?>
                        </div>
                        <div class="col-sm-6 fw-bold">Date</div>
                        <div class="col-sm-6">
                            <?=date('d M Y')?>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    The link to this report was emailed to
                    <?=$data['contactEmail']?>.
                    <ul class="fa-ul mt-2">
                        <li><span class="fa-li"><i class="fa-solid fa-map-pin"></i></span> <a
                                href="<?=get_the_permalink()?>"
                                class="text-white">Bookmark this link</a> for future reference.</li>
                        <li><span class="fa-li"><i class="fa-solid fa-envelope"></i></span> <a
                                href="mailto:?subject=Afiniti Change Readiness Assessment&body=<?=get_the_permalink()?>"
                                class="text-white">Share via email</a></li>
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
            foreach ($levers as $l) {
                $theScore = round(($scores[$l] / 30) * 100);
                $field = strtolower($l) . '_analysis';
                $which = '';
                while(have_rows($field, $pageID)) {
                    the_row();
                    if ($theScore >= get_sub_field('low_score') && $theScore <= get_sub_field('high_score')) {
                        echo str_replace(['<p>', '</p>'], '', apply_filters('the_content', get_sub_field('summary'))) . ' ';
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
    foreach ($levers as $l) {
        // $theScore = getPercentOfNumber($scores[$l],30);
        $theScore = round(($scores[$l] / 30) * 100);
        ?>
            <div class="results__grid">
                <div class="d-flex justify-content-between">
                    <div class="fw-bold"><?=$l?></div>
                    <div class="d-md-none fw-normal"><?=$theScore?>%
                    </div>
                </div>
                <div class="d-none d-md-block"><?=$theScore?>%</div>
                <?php
                $field = strtolower($l) . '_analysis';
        $which = '';
        while(have_rows($field, $pageID)) {
            the_row();
            if ($theScore >= get_sub_field('low_score') && $theScore <= get_sub_field('high_score')) {
                ?>
                <div>
                    <?=apply_filters('the_content',get_sub_field('analysis'))?>
                </div>
                <div>
                    <?=apply_filters('the_content', cb_list(get_sub_field('recommendations')))?>
                </div>
                <?php
            }
        }
        ?>
            </div>
            <?php
    }
?>
    </div>
    </section>

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