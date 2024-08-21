<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

require_once CB_THEME_DIR . '/inc/cb-posttypes.php';
require_once CB_THEME_DIR . '/inc/cb-taxonomies.php';
require_once CB_THEME_DIR . '/inc/cb-utility.php';
require_once CB_THEME_DIR . '/inc/cb-blocks.php';
require_once CB_THEME_DIR . '/inc/cb-news.php';
// require_once CB_THEME_DIR . '/inc/cb-careers.php';

// Remove unwanted SVG filter injection WP
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');


// Remove comment-reply.min.js from footer
function remove_comment_reply_header_hook()
{
    wp_deregister_script('comment-reply');
}
add_action('init', 'remove_comment_reply_header_hook');

add_action('admin_menu', 'remove_comments_menu');
function remove_comments_menu()
{
    remove_menu_page('edit-comments.php');
}

add_filter('theme_page_templates', 'child_theme_remove_page_template');
function child_theme_remove_page_template($page_templates)
{
    // unset($page_templates['page-templates/blank.php'],$page_templates['page-templates/empty.php'], $page_templates['page-templates/fullwidthpage.php'], $page_templates['page-templates/left-sidebarpage.php'], $page_templates['page-templates/right-sidebarpage.php'], $page_templates['page-templates/both-sidebarspage.php']);
    unset($page_templates['page-templates/blank.php'],$page_templates['page-templates/empty.php'], $page_templates['page-templates/left-sidebarpage.php'], $page_templates['page-templates/right-sidebarpage.php'], $page_templates['page-templates/both-sidebarspage.php']);
    return $page_templates;
}
add_action('after_setup_theme', 'remove_understrap_post_formats', 11);
function remove_understrap_post_formats()
{
    remove_theme_support('post-formats', array( 'aside', 'image', 'video' , 'quote' , 'link' ));
}

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(
        array(
            'page_title' 	=> 'Site-Wide Settings',
            'menu_title'	=> 'Site-Wide Settings',
            'menu_slug' 	=> 'theme-general-settings',
            'capability'	=> 'edit_posts',
        )
    );
}

function widgets_init()
{
    register_sidebar(
        array(
            'name'          => __('Footer Col 1', 'cb-afiniti'),
            'id'            => 'footer-1',
            'description'   => __('Footer Col 1', 'cb-afiniti'),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
        )
    );

    register_nav_menus(array(
        'primary_nav' => __('Primary Nav', 'cb-afiniti'),
    ));

    register_nav_menus(array(
        'footer_menu1' => __('Footer Menu 1', 'cb-afiniti'),
    ));
    // register_nav_menus(array(
    //     'footer_menu2' => __('Footer Menu 2', 'cb-afiniti'),
    // ));
    // register_nav_menus(array(
    //     'footer_menu3' => __('Footer Menu 3', 'cb-afiniti'),
    // ));

    unregister_sidebar('hero');
    unregister_sidebar('herocanvas');
    unregister_sidebar('statichero');
    unregister_sidebar('left-sidebar');
    unregister_sidebar('right-sidebar');
    unregister_sidebar('footerfull');
    unregister_nav_menu('primary');

    add_theme_support('disable-custom-colors');
    add_theme_support(
        'editor-color-palette',
        array(
            array(
                'name'  => 'Green',
                'slug'  => 'green-500',
                'color' => '#accf83',
            ),
            array(
                'name'  => 'Orange',
                'slug'  => 'orange-500',
                'color' => '#ed9025',
            ),
            array(
                'name'  => 'Purple',
                'slug'  => 'purple-500',
                'color' => '#575b8a',
            ),
            array(
                'name'  => 'Blue',
                'slug'  => 'blue-500',
                'color' => '#00a0df',
            ),
            array(
                'name'  => 'Red',
                'slug'  => 'red-500',
                'color' => '#8a2432',
            ),
            array(
                'name'  => 'Grey',
                'slug'  => 'grey-500',
                'color' => '#474747',
            ),
            array(
                'name'  => 'white',
                'slug'  => 'white',
                'color' => '#fff',
            ),
        )
    );
}
add_action('widgets_init', 'widgets_init', 11);


remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

//Custom Dashboard Widget
add_action('wp_dashboard_setup', 'register_cb_dashboard_widget');
function register_cb_dashboard_widget()
{
    wp_add_dashboard_widget(
        'cb_dashboard_widget',
        'Chillibyte',
        'cb_dashboard_widget_display'
    );
}

function cb_dashboard_widget_display()
{
    ?>
<div style="display: flex; align-items: center; justify-content: space-around;">
    <img style="width: 50%;"
        src="<?= get_stylesheet_directory_uri().'/img/cb-full.jpg'; ?>">
    <a class="button button-primary" target="_blank" rel="noopener nofollow noreferrer"
        href="mailto:hello@chillibyte.co.uk/">Contact</a>
</div>
<div>
    <p><strong>Thanks for choosing Chillibyte!</strong></p>
    <hr>
    <p>Got a problem with your site, or want to make some changes & need us to take a look for you?</p>
    <p>Use the link above to get in touch and we'll get back to you ASAP.</p>
</div>
<?php
}



// remove discussion metabox
function cc_gutenberg_register_files()
{
    // script file
    wp_register_script(
        'cc-block-script',
        get_stylesheet_directory_uri() .'/js/block-script.js', // adjust the path to the JS file
        array( 'wp-blocks', 'wp-edit-post' )
    );
    // register block editor script
    register_block_type('cc/ma-block-files', array(
        'editor_script' => 'cc-block-script'
    ));
}
add_action('init', 'cc_gutenberg_register_files');

function understrap_all_excerpts_get_more_link($post_excerpt)
{
    if (is_admin() || ! get_the_ID()) {
        return $post_excerpt;
    }
    return $post_excerpt;
}

//* Remove Yoast SEO breadcrumbs from Revelanssi's search results
add_filter('the_content', 'wpdocs_remove_shortcode_from_index');
function wpdocs_remove_shortcode_from_index($content)
{
    if (is_search()) {
        $content = strip_shortcodes($content);
    }
    return $content;
}



// GF really is pants.
/**
 * Change submit from input to button
 *
 * Do not use example provided by Gravity Forms as it strips out the button attributes including onClick
 */
function wd_gf_update_submit_button($button_input, $form)
{
    //save attribute string to $button_match[1]
    preg_match("/<input([^\/>]*)(\s\/)*>/", $button_input, $button_match);

    //remove value attribute (since we aren't using an input)
    $button_atts = str_replace("value='" . $form['button']['text'] . "' ", "", $button_match[1]);

    // create the button element with the button text inside the button element instead of set as the value
    return '<button ' . $button_atts . '><span>' . $form['button']['text'] . '</span></button>';
}
add_filter('gform_submit_button', 'wd_gf_update_submit_button', 10, 2);


function cb_theme_enqueue()
{
    $the_theme = wp_get_theme();
    // wp_enqueue_style('lightbox-stylesheet', get_stylesheet_directory_uri() . '/css/lightbox.min.css', array(), $the_theme->get('Version'));
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox-plus-jquery.min.js', array(), $the_theme->get('Version'), true);
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox.min.js', array(), $the_theme->get('Version'), true);
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.3.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'cb_theme_enqueue');


// black thumbnails - fix alpha channel
/**
 * Patch to prevent black PDF backgrounds.
 *
 * https://core.trac.wordpress.org/ticket/45982
 */
require_once ABSPATH . 'wp-includes/class-wp-image-editor.php';
require_once ABSPATH . 'wp-includes/class-wp-image-editor-imagick.php';

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
final class ExtendedWpImageEditorImagick extends WP_Image_Editor_Imagick
{
    /**
     * Add properties to the image produced by Ghostscript to prevent black PDF backgrounds.
     *
     * @return true|WP_error
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function pdf_load_source()
    {
        $loaded = parent::pdf_load_source();

        try {
            $this->image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $this->image->setBackgroundColor('#ffffff');
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }

        return $loaded;
    }
}

/**
 * Filters the list of image editing library classes to prevent black PDF backgrounds.
 *
 * @param array $editors
 * @return array
 */
add_filter('wp_image_editors', function (array $editors): array {
    array_unshift($editors, ExtendedWpImageEditorImagick::class);

    return $editors;
});



add_shortcode('cb_all_people', function () {
    ob_start();

    $args = array(
        'post_type' => 'people',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    $people = new WP_Query($args);

    ?>
<div class="container-xl">
    <div class="row g-4">
        <?php
    while ($people->have_posts()) {
        $people->the_post();
        ?>
        <div class="col-md-6 col-lg-4 pb-4" itemscope="itemscope" itemtype="https://schema.org/Person">
            <a href="<?=get_the_permalink()?>">
                <div class="person-photo-container mb-2">
                    <div class="person-photo"
                        style="background-image:url(<?=wp_get_attachment_image_url(get_field('photo'), 'large')?>"
                        itemprop="image"></div>
                </div>
            </a>
            <div class="fs-5 text--green fw-bold" itemprop="name">
                <?=get_the_title()?>
            </div>
            <div class="pb-2" itemprop="jobTitle">
                <?=get_field('job_title')?>
            </div>
            <div class="pb-2 person-description" itemprop="description">
                <?=wp_trim_words(get_the_content(), 18)?>
            </div>
            <div class="person-links">
                <a class="more-circle"
                    href="<?=get_the_permalink()?>"><span
                        class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i
                            class="fa-solid fa-plus fa-stack-1x fa-inverse"></i></span></a>
                <a class="linkedin-circle"
                    href="<?=get_field('linkedin_url')?>"
                    target="_blank" itemprop="url"><span class="fa-stack fa-2x"><i
                            class="fa fa-circle fa-stack-2x"></i><i
                            class="fa-brands fa-linkedin-in fa-stack-1x fa-inverse"></i></span></a>
            </div>
        </div>
        <?php
    }

    ?>
    </div>
</div>
<?php

    return ob_get_clean();
});

// add role to people index
function add_acf_columns($columns)
{
    return array_merge($columns, array(
      'job_title' => __('Job Title')
    ));
}
add_filter('manage_people_posts_columns', 'add_acf_columns');

function people_custom_column($column, $post_id)
{
    switch ($column) {
        case 'job_title':
            echo get_post_meta($post_id, 'job_title', true);
            break;
    }
}
add_action('manage_people_posts_custom_column', 'people_custom_column', 10, 2);

add_filter('manage_people_posts_columns', 'column_order');
function column_order($columns)
{
    $n_columns = array();
    $move = 'job_title'; // what to move
    $before = 'date'; // move before this
    foreach ($columns as $key => $value) {
        if ($key==$before) {
            $n_columns[$move] = $move;
        }
        $n_columns[$key] = $value;
    }
    return $n_columns;
}


// careers functions
add_shortcode('cb_current_opportunities', function () {
    ob_start();
    $args = array(
        'post_type' => 'careers',
        'post_status' => 'publish',
        'posts_per_page' => -1
        );
    $careers = new WP_Query($args);

    ?>
<section class="current_opportunities bg--green-500 py-5 mb-5">
    <div class="container-xl">
        <h2 class="mb-4">Current Opportunities</h2>
        <?php
                if ($careers->have_posts()) {
                    if ($careers->found_posts == 1) {
                        while ($careers->have_posts()) {
                            $careers->the_post();
                            ?>
        <div class="row g-4">
            <div class="col-md-8">
                <a href="<?=get_the_permalink()?>"
                    class="text-white">
                    <p class="fs-5 fw-bold"><?=get_the_title()?></p>
                    <p><?=get_field('description', get_the_ID())?>
                    </p>
                    <div class="fw-bold text-white anim-arrow--slide noline"
                        href="<?=get_the_permalink()?>">Read more
                        <span class="arrow"></span>
                    </div>
                </a>
            </div>
            <div class="col-md-4 justify-content-center">
                <img src="<?=get_stylesheet_directory_uri()?>/img/illustrations/Ship-Anchored.png"
                    alt="">
            </div>
        </div>
        <?php

                        }
                    } elseif ($careers->found_posts == 2) {
                        ?>
        <div class="row g-4 mx-0">
            <div class="order-1 order-lg-3 col-lg-4 text-center">
                <img src="<?=get_stylesheet_directory_uri()?>/img/illustrations/Ship-Anchored.png"
                    class="w-75 w-md-50 w-lg-100">
            </div>
            <?php
                        while ($careers->have_posts()) {
                            $careers->the_post();
                            ?>
            <div class="col-lg-4 order-2 order-lg-1">
                <a href="<?=get_the_permalink()?>"
                    class="text-white">
                    <p class="fs-5 fw-bold"><?=get_the_title()?></p>
                    <p><?=get_field('description', get_the_ID())?>
                    </p>
                    <div class="fw-bold text-white anim-arrow--slide noline"
                        href="<?=get_the_permalink()?>">Read more
                        <span class="arrow"></span>
                    </div>
                </a>
            </div>
            <?php

                        }
                        ?>
        </div>
        <?php
                    } elseif ($careers->found_posts == 3) {
                        ?>
        <div class="row g-4 mx-0">
            <?php
                        while ($careers->have_posts()) {
                            $careers->the_post();
                            ?>
            <div class="col-lg-4">
                <a href="<?=get_the_permalink()?>"
                    class="text-white">
                    <p class="fs-5 fw-bold"><?=get_the_title()?></p>
                    <p><?=get_field('description', get_the_ID())?>
                    </p>
                    <div class="fw-bold text-white anim-arrow--slide noline"
                        href="<?=get_the_permalink()?>">Read more
                        <span class="arrow"></span>
                    </div>
                </a>
            </div>
            <?php

                        }
                        ?>
        </div>
        <?php
                    } else {
                        echo 'TODO: CAROUSEL';
                    }
                } else {
                    ?>
        <div class="row g-4">
            <div class="col-md-8">
                <p class="fw-bold">We don't currently have any vacancies within the Afiniti team.</p>
                <p>Please check again at a later date or submit your CV below to be informed of suitable roles.</p>
            </div>
            <div class="col-md-4 justify-content-center">
                <img src="<?=get_stylesheet_directory_uri()?>/img/illustrations/Ship-Anchored.png"
                    alt="">
            </div>
        </div>
        <?php
                }
    ?>
    </div>
</section>
<?php
    return ob_get_clean();
});


add_filter('wpseo_breadcrumb_links', 'override_yoast_breadcrumb_trail_stories');

function override_yoast_breadcrumb_trail_stories($links)
{
    global $post;

    if (is_singular('case-studies')) {
        $breadcrumb[] = array(
            'url' => '/case-studies/',
            'text' => 'Case Studies',
        );
        array_splice($links, 1, -2, $breadcrumb);
    }
    if (is_singular('careers')) {
        $breadcrumb[] = array(
            'url' => '/careers/',
            'text' => 'Careers',
        );
        array_splice($links, 1, -2, $breadcrumb);
    }

    return $links;
}

add_filter('relevanssi_modify_wp_query', function ($query) {
    if (empty($query->query_vars['sentence'])) {
        unset($query->query_vars['sentence']);
    }
    return $query;
});


// disable canonicals (for WPE)
add_filter('wpseo_canonical', '__return_false');

// CSV CRA Download
function cb_register_cra_menu_page()
{
    add_submenu_page(
        'edit.php?post_type=cra',
        'Download Results',
        'Download Results',
        'manage_options',
        'download-cra',
        'cra_download_callback',
        6
    );
}
add_action('admin_menu', 'cb_register_cra_menu_page');

function cra_download_callback()
{
    ?>
<style>
    .form {
        display: grid;
        gap: 1rem;
        width: min-content;
    }

    .form div {
        display: flex;
        gap: 1rem;
    }

    .form label {
        min-width: 100px;
    }
</style>
<div class="wrap">
    <h1>CRA Tool Results Download</h1>
    <p>
    <form
        action="<?=get_stylesheet_directory_uri()?>/cra-results.php"
        method="POST" class="form">
        <div><label for="start">Start Date</label><input type="date" name="start" id="start"
                value="<?=date("Y-m-01")?>">
        </div>
        <div><label for="end">End Date</label><input type="date" name="end" id="end"
                value="<?=date("Y-m-d")?>"></div>
        <input type="submit" value="Get Results">
    </form>
    </p>
</div>
<?php
}

add_shortcode('org_name', function () {
    global $data;
    return $data['orgName'];
});

?>