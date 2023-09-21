<?php

function cb_register_taxes()
{
    $args = [
        "labels" => [
            "name" => __("Transformations", "cb-afiniti"),
            "singular_name" => __("Transformation", "cb-afiniti"),
        ],
        "public" => true,
        "publicly_queryable" => false,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => false,
        "show_admin_column" => true,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "show_in_quick_edit" => true,
    ];
    register_taxonomy("transformation", [ "case-studies" ], $args);

    $args = [
        "labels" => [
            "name" => __("Sectors", "cb-afiniti"),
            "singular_name" => __("Sector", "cb-afiniti"),
        ],
        "public" => true,
        "publicly_queryable" => false,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => false,
        "show_admin_column" => true,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "show_in_quick_edit" => true,
    ];
    register_taxonomy("sectors", [ "case-studies" ], $args);

    $args = [
        "labels" => [
            "name" => __("Insight Types", "cb-afiniti"),
            "singular_name" => __("Insight Type", "cb-afiniti"),
        ],
        "default_term" => [ 'name' => 'Article', 'slug' => 'article' ],
        "public" => true,
        "publicly_queryable" => false,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => false,
        "show_admin_column" => true,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "show_in_quick_edit" => true,
    ];
    register_taxonomy("insight-type", [ "post" ], $args);

    $args = [
        "labels" => [
            "name" => __("Levers", "cb-afiniti"),
            "singular_name" => __("Lever", "cb-afiniti"),
        ],
        "public" => true,
        "publicly_queryable" => false,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => false,
        "show_admin_column" => true,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "show_in_quick_edit" => true,
    ];
    register_taxonomy("lever", [ "post" ], $args);
}
add_action('init', 'cb_register_taxes');
