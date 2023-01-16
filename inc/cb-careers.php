<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


function cb_register_career_post_type() {

    $labels = [
        "name" => __( "Careers", "cb-digitalhome" ),
        "singular_name" => __( "Career", "cb-digitalhome" ),
    ];

    $args = [
        "label" => __( "Careers", "cb-digitalhome" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "careers", "with_front" => false ],
        "query_var" => true,
        "supports" => [ "title", "thumbnail", "editor" ],
        "show_in_graphql" => false,
        "menu_icon" => "dashicons-nametag",
    ];
    register_post_type( "careers", $args );
}
add_action( 'init', 'cb_register_career_post_type' );

function jobs_func($atts) {
    ob_start();
    $q = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'careers',
        'post_status' => 'publish'
    ));

    if ($q->have_posts()) {
        ?>
<div class="container-xl">
<div class="group joblistings">
        <?php
        while ($q->have_posts()) {
            $q->the_post();
            ?>
	<div class="joblisting my-4">
        <a href="<?php the_permalink(); ?>">
            <div class="job">
                <div class="d-flex justify-content-between flex-wrap g-4 align-items-baseline">
                    <div class="job-title"><?php the_title(); ?></div>
                    <div class="job-posted">
                        <strong>Posted:</strong> <?=get_the_date()?>
                    </div>
                </div>
                <div class="job-description">
                    <?=get_field('banner',get_the_ID())?>
                </div>
                <div class="job-salary"><strong>Salary:</strong> <?php
                    if (have_rows('base_salary', get_the_ID())) {
                        while (have_rows('base_salary', get_the_ID())) {
                            the_row();
                            if (get_sub_field('text', get_the_ID())) {
                                echo get_sub_field('text', get_the_ID());
                            } else {
                                while (have_rows('numeric_salary', get_the_ID())) {
                                    the_row();
                                    echo '£' . number_format(get_sub_field('minValue', get_the_ID()));
                                    if (get_sub_field('maxValue', get_the_ID())) {
                                        echo ' - £' . number_format(get_sub_field('maxValue', get_the_ID()));
                                    }
                                    echo ' per ' . strtolower(get_sub_field('QuantitativeValue', get_the_ID()));
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="job__link">Read more</div>
        </a>
	</div>
<?php
        }
        /* Restore original Post Data */
        wp_reset_postdata();
    }
    else {
        echo 'We do not have any positions available at this time. Check back later to see new postings.';
    }
    
    ?>
</div>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('jobs', 'jobs_func');
