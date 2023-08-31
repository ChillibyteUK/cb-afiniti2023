<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
get_header();
?>
<main id="main">
    <!-- hero -->
    <section id="hero" class="hero d-flex align-items-center hero--default">
        <div class="overlay"></div>
        <div class="hero__inner container-xl">
            <div class="hero__content d-flex flex-column justify-content-center order-2 order-lg-1 pb-5"
                data-aos="fade">
                <div class="h1 hero__meta text-white text-center">Careers at <span>Afiniti</span></div>
            </div>
        </div>
    </section>
    <?php
$anim = 'our-people';
include get_stylesheet_directory() . '/page-templates/anim/' . $anim . '.php';

$cv = get_field('single_career_sidebar', 'options');

?>
    <div class="container-xl">
        <section class="breadcrumbs container-xl">
            <?php
        if (function_exists('yoast_breadcrumb')) {
            yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
        }
?>
        </section>
        <h1 class="mb-4 fw-bold text--green"><?=get_the_title()?>
        </h1>
        <div class="row g-4">
            <div class="col-lg-8 col-xxl-9">
                <?php
    the_content();
?>
            </div>
            <div class="col-lg-4 col-xxl-3">
                <div class="row">
                    <div class="col-12">
                        <div class="bg--green-500 px-5 py-4">
                            <div class="fs-4 fw-bold pb-2">
                                <?=$cv['title']?>
                            </div>
                            <?php if ($cv['subtitle']) {
                                echo '<div class="fw-bold">' . $cv['subtitle'] . '</div>';
                            } ?>
                            <?php if ($cv['body']) {
                                echo '<div>' . $cv['body'] . '</div>';
                            } ?>
                        </div>
                    </div>
                    <div class="col-8 offset-2 text-center halfcircle-container">
                        <div class="div-rounded ss-halfcircle halfcircle-green">
                            <div class="halfcircle-content halfcircle-content-sm fw-bold">
                                <a
                                    href="<?=$cv['link']?>"><?=$cv['link_title']?>
                                    <span class="arrow arrow-block mt-2"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="h3">Current Opportunities</div>
                    <?php
                $careers = new WP_Query(array(
                    'post_type' => 'careers',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'post__not_in' => [get_the_ID()]
                ));

if ($careers->have_posts()) {
    while ($careers->have_posts()) {
        $careers->the_post();
        $url = get_the_permalink($careers->ID);
        ?>
                    <div class="pb-4 single-career">
                        <a href="<?=$url?>">
                            <div class="fw-bold py-2">
                                <?=get_the_title()?>
                            </div>
                            <div class="anim-arrow--slide">Read more <span class="arrow"></span></div>
                        </a>
                    </div>
                    <?php
    }
}
wp_reset_postdata();
?>
                </div>

            </div>
            <hr class="my-4">
            <a class="anchor" id="cv"></a>
            <h2>Submit <span>Your CV</span></h2>
            <div class="container-xl px-2">
            <?=do_shortcode('[gravityform id="4" title="false" description="false" ajax="true" field_values="jobapplied=' . get_the_title() . '"]')?>
            </div>
</main>
<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "JobPosting",
        "title": "<?=get_the_title(get_the_ID())?>",
        "description": <?=json_encode(get_field('description', get_the_ID()));?> ,
        "hiringOrganization": {
            "@type": "Organization",
            "name": "Afiniti",
            "legalName": "Afiniti LLP",
            "url": "https://www.afiniti.co.uk/",
            "logo": "https://www.afiniti.co.uk/wp-content/uploads/2020/01/afiniti-logo-v2.png",
            "description": "Afiniti is an award-winning business change consultancy that delivers change with a people focus. We are passionate about making change a force for good driving progress and innovation.",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Suite 302, Dowgate Hill House,  14-16 Dowgate Hill",
                "addressLocality": "London",
                "addressRegion": "London",
                "postalCode": "EC4R 2SU",
                "addressCountry": "United Kingdom"
            },
            "contactPoint": [{
                "@type": "ContactPoint",
                "email": " enquiries@afiniti.co.uk",
                "contactType": "enquiries"
            }],
            "sameAs": [
                "https://twitter.com/afinitiLLP",
                "https://www.linkedin.com/company/afiniti",
                "https://www.youtube.com/user/Afinitiltd",
                "https://www.crunchbase.com/organization/afinit-ltd",
                "https://www.facebook.com/AfinitiConsultants",
                "https://www.wikidata.org/wiki/Q107664234"
            ]
        },
        "datePosted": "<?=get_the_date('Y-m-d')?>",
        "validThrough": "2030-01-01T00:00",
        "employmentType": "<?=get_field('employmenttype', get_the_ID())?>",
        "baseSalary": {
            "@type": "MonetaryAmount",
            "currency": "GBP",
            "value": {
                "@type": "QuantitativeValue",
                "minValue":
                <?php if (is_numeric(get_field('minimum_salary'))) {
                    echo get_field('minimum_salary');
                } else {
                    echo "0";
                } ?>
                ,
                "maxValue":
                <?php if (is_numeric(get_field('maximum_salary'))) {
                    echo get_field('maximum_salary');
                } else {
                    echo "0";
                } ?>
                ,
                "unitText": "YEAR"
            }
        },
        <?php
        if (get_field('location-based_or_remote') == 'Office-based') {
            ?>
        "jobLocation": {
            "@type": "Place",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "<?=get_field('streetaddress')?>",
                "addressLocality": "<?=get_field('addressregion')?>",
                "addressRegion": "<?=get_field('addressregion')?>",
                "postalCode": "<?=get_field('postalcode')?>",
                "addressCountry": "United Kingdom"
            }
        }
            <?php
        }
        else {
            ?>
            "jobLocationType": "TELECOMMUTE",
            "joblocation":"UK",
            "applicantLocationRequirements": {
                "@type": "Country",
                "name": "UK"
            }
            <?php
        }
        ?>
    }
</script>
<?php
get_footer();
?>