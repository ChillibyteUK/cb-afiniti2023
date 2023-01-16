<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

require_once CB_THEME_DIR . '/inc/cb-posttypes.php';
require_once CB_THEME_DIR . '/inc/cb-taxonomies.php';
require_once CB_THEME_DIR . '/inc/cb-utility.php';
require_once CB_THEME_DIR . '/inc/cb-blocks.php';
require_once CB_THEME_DIR . '/inc/cb-news.php';
require_once CB_THEME_DIR . '/inc/cb-careers.php';


// Remove unwanted SVG filter injection WP
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );


// Remove comment-reply.min.js from footer
function remove_comment_reply_header_hook(){
	wp_deregister_script( 'comment-reply' );
}
add_action('init','remove_comment_reply_header_hook');

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
    register_nav_menus(array(
        'footer_menu2' => __('Footer Menu 2', 'cb-afiniti'),
    ));
    register_nav_menus(array(
        'footer_menu3' => __('Footer Menu 3', 'cb-afiniti'),
    ));

    unregister_sidebar('hero');
    unregister_sidebar('herocanvas');
    unregister_sidebar('statichero');
    unregister_sidebar('left-sidebar');
    unregister_sidebar('right-sidebar');
    unregister_sidebar('footerfull');
    unregister_nav_menu('primary');

    add_theme_support( 'disable-custom-colors' );
    add_theme_support(
        'editor-color-palette',
        array(
            array(
                'name'  => 'Blue',
                'slug'  => 'default',
                'color' => '#254690',
            ),
            array(
                'name'  => 'Purple',
                'slug'  => 'donate',
                'color' => '#640160',
            ),
            array(
                'name'  => 'Green',
                'slug'  => 'support',
                'color' => '#44946C',
            ),
            array(
                'name'  => 'Grey',
                'slug'  => 'grey',
                'color' => '#575757',
            ),
        )
    );

}
add_action('widgets_init', 'widgets_init', 11);


remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

//Custom Dashboard Widget
add_action( 'wp_dashboard_setup', 'register_cb_dashboard_widget' );
function register_cb_dashboard_widget() {
	wp_add_dashboard_widget(
		'cb_dashboard_widget',
		'Chillibyte',
		'cb_dashboard_widget_display'
	);

}

function cb_dashboard_widget_display() {
   ?>
    <div style="display: flex; align-items: center; justify-content: space-around;">
        <img style="width: 50%;" src="<?= get_stylesheet_directory_uri().'/img/cb-full.jpg'; ?>">
        <a class="button button-primary" target="_blank" rel="noopener nofollow noreferrer" href="mailto:hello@www.chillibyte.co.uk/">Contact</a>
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
function cc_gutenberg_register_files() {
    // script file
    wp_register_script(
        'cc-block-script',
        get_stylesheet_directory_uri() .'/js/block-script.js', // adjust the path to the JS file
        array( 'wp-blocks', 'wp-edit-post' )
    );
    // register block editor script
    register_block_type( 'cc/ma-block-files', array(
        'editor_script' => 'cc-block-script'
    ) );

}
add_action( 'init', 'cc_gutenberg_register_files' );

function understrap_all_excerpts_get_more_link( $post_excerpt ) {
    if ( is_admin() || ! get_the_ID() ) {
        return $post_excerpt;
    }
    return $post_excerpt;
}

//* Remove Yoast SEO breadcrumbs from Revelanssi's search results
add_filter( 'the_content', 'wpdocs_remove_shortcode_from_index' );
function wpdocs_remove_shortcode_from_index( $content ) {
    if ( is_search() ) {
        $content = strip_shortcodes( $content );
    }
    return $content;
}



// GF really is pants.
/**
 * Change submit from input to button
 *
 * Do not use example provided by Gravity Forms as it strips out the button attributes including onClick
 */
function wd_gf_update_submit_button( $button_input, $form ) {

    //save attribute string to $button_match[1]
    preg_match( "/<input([^\/>]*)(\s\/)*>/", $button_input, $button_match );

    //remove value attribute (since we aren't using an input)
    $button_atts = str_replace( "value='" . $form['button']['text'] . "' ", "", $button_match[1] );

    // create the button element with the button text inside the button element instead of set as the value
    return '<button ' . $button_atts . '><span>' . $form['button']['text'] . '</span></button>';

}
add_filter('gform_submit_button', 'wd_gf_update_submit_button', 10, 2);


function cb_theme_enqueue()
{
    $the_theme = wp_get_theme();
    wp_enqueue_style('lightbox-stylesheet', get_stylesheet_directory_uri() . '/css/lightbox.min.css', array(), $the_theme->get('Version'));
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox-plus-jquery.min.js', array(), $the_theme->get('Version'), true);
    wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox.min.js', array(), $the_theme->get('Version'), true);
}
add_action('wp_enqueue_scripts', 'cb_theme_enqueue');


// change breadcrumbs based on referrer context
add_filter('wpseo_breadcrumb_links', function( $links ) {
    global $post;
    if ( is_singular( 'post' ) ) {
        $t = get_the_category($post->ID);

        if ($t[0]->slug == 'event') {

            $breadcrumb[] = array(
                'url' => '/events/',
                'text' => 'Events',
            );

            array_splice( $links, 0, 1, $breadcrumb );
        }
    }
    return $links;
}
);


// make phone OR email required
// NOTE: replace 4 with the form ID
add_filter( 'gform_validation_1', 'custom_validation' );
function custom_validation( $validation_result ) {
    $form = $validation_result['form'];

    // if one of the required fields are required
    // NOTE: replace 1, 2, 3 with the fields you would like to validate
    if ( empty( rgpost( 'input_4' ) ) && empty( rgpost( 'input_5' ) ) ) {

        //finding Field with ID of 1 and marking it as failed validation
        foreach( $form['fields'] as &$field ) {

            // NOTE: replace 1, 2, 3 with the fields you would like to validate
            if ( $field->id == '4' || $field->id == '5' ) {
                $field->failed_validation = true;
                $field->validation_message = 'Please provide phone number or email address.';
                $validation_result['is_valid'] = false;
            }
        }
    }
    // Assign modified $form object back to the validation result
    $validation_result['form'] = $form;
    return $validation_result;
}


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