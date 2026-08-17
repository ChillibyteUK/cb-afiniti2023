<?php

function cb_register_post_types()
{

    $labels = [
        "name" => __("Case Studies", "cb-afiniti"),
        "singular_name" => __("Case Study", "cb-afiniti"),
    ];

    $args = [
        "label" => __("Case Study", "cb-afiniti"),
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
        "menu_icon" => "dashicons-portfolio",
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "case-studies", "with_front" => false ],
        "query_var" => true,
        "supports" => [ "title",  "thumbnail", "editor" ],
        "show_in_graphql" => false,
        "exclude_from_search" => true
    ];

    register_post_type("case-studies", $args);

    $labels = [
        "name" => __("People", "cb-afiniti"),
        "singular_name" => __("Person", "cb-afiniti"),
    ];

    $args = [
        "label" => __("Person", "cb-afiniti"),
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
        "menu_icon" => "dashicons-groups",
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "our-people", "with_front" => false ],
        "query_var" => true,
        "supports" => [ "title", "editor" ],
        "show_in_graphql" => false,
    ];

    register_post_type("people", $args);

    $labels = [
        "name" => __("Careers", "cb-afiniti"),
        "singular_name" => __("Career", "cb-afiniti"),
    ];

    $args = [
        "label" => __("Career", "cb-afiniti"),
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
        "menu_icon" => "dashicons-nametag",
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "careers", "with_front" => false ],
        "query_var" => true,
        "supports" => [ "title", "editor" ],
        "show_in_graphql" => false,
    ];

    register_post_type("careers", $args);

    $labels = [
        "name" => __("CRA Results", "cb-afiniti"),
        "singular_name" => __("CRA Result", "cb-afiniti"),
    ];

    $args = [
        "label" => __("CRA Result", "cb-afiniti"),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        /*
         * NOT exposed over REST. Each result's post_title is "Company | email"
         * (see cra.php), so an open /wp-json/wp/v2/cra would list every
         * submitter's email address. The results URL itself is protected only by
         * a random 32 character slug, which a REST index would have bypassed
         * entirely. Nothing consumes the route - "supports" is title only.
         */
        "show_in_rest" => false,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => false,
        "menu_icon" => "dashicons-portfolio",
        "delete_with_user" => false,
        // Keep results out of on-site search; they are private to the recipient.
        "exclude_from_search" => true,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "cra", "with_front" => false ],
        "query_var" => true,
        "supports" => [ "title" ],
        "show_in_graphql" => false,
    ];

    register_post_type("cra", $args);

}

add_action('init', 'cb_register_post_types');

/**
 * Keeps submitter email addresses out of the front end.
 *
 * cra.php no longer puts the email in post_title, but results created before
 * that change are titled "Company | email". post_title surfaces in more places
 * than the template controls - Yoast alone puts it in og:title and the JSON-LD
 * graph - so anything before the pipe is all that is ever exposed publicly.
 * Admin is left alone: the list screen has its own Email column.
 *
 * @param string   $title   The post title.
 * @param int|null $post_id The post ID.
 * @return string
 */
function cb_strip_cra_title_email( $title, $post_id = null ) {
    if ( is_admin() || ! $post_id || 'cra' !== get_post_type( $post_id ) ) {
        return $title;
    }

    if ( strpos( $title, '|' ) === false ) {
        return $title;
    }

    return trim( strstr( $title, '|', true ) );
}
add_filter( 'the_title', 'cb_strip_cra_title_email', 10, 2 );

/**
 * Same protection for the Yoast output, which builds its titles from the post
 * object rather than through the the_title filter.
 */
function cb_strip_cra_seo_title( $title ) {
    if ( ! is_singular( 'cra' ) ) {
        return $title;
    }

    return 'Change Readiness Assessment Results';
}
add_filter( 'wpseo_title', 'cb_strip_cra_seo_title' );
add_filter( 'wpseo_opengraph_title', 'cb_strip_cra_seo_title' );
add_filter( 'wpseo_schema_graph', 'cb_scrub_cra_schema_graph', 10 );

/**
 * Drops the schema graph entirely on results pages - it is built from the post
 * title and there is nothing on these pages worth marking up for search.
 *
 * @param array $graph The Yoast schema graph.
 * @return array
 */
function cb_scrub_cra_schema_graph( $graph ) {
    return is_singular( 'cra' ) ? array() : $graph;
}

/**
 * Results pages should never be indexed.
 *
 * Output directly rather than through the wp_robots filter: Yoast suppresses
 * core's robots tag and emits its own, and on this post type it emits none at
 * all, so filtering either one is unreliable.
 */
function cb_noindex_cra() {
    if ( is_singular( 'cra' ) ) {
        echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
    }
}
add_action( 'wp_head', 'cb_noindex_cra', 1 );

function add_new_cra_column($columns)
{
    $columns['org'] = 'Organisation';
    $columns['email'] = 'Email';
    return $columns;
}
add_filter('manage_cra_posts_columns', 'add_new_cra_column');

add_filter('manage_cra_posts_custom_column', 'add_new_cra_admin_column_show_value', 10, 2);
function add_new_cra_admin_column_show_value($column, $post_id)
{
    switch($column) {
        case 'email':
            echo get_field('data', $post_id)['contactEmail'];
            break;
        case 'org':
            echo get_field('data', $post_id)['orgName'];
            break;
    }
}

add_action('after_switch_theme', 'cb_rewrite_flush');
function cb_rewrite_flush()
{
    cb_register_post_types();
    flush_rewrite_rules();
}
