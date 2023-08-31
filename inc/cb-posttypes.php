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
        "rewrite" => [ "slug" => "cra", "with_front" => false ],
        "query_var" => true,
        "supports" => [ "title" ],
        "show_in_graphql" => false,
        "exclude_from_search" => true
    ];

    register_post_type("cra", $args);

}

add_action('init', 'cb_register_post_types');

add_action('init', 'cb_register_post_types');

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
