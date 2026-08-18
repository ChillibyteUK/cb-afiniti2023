<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Chillibyte Theme Functions
 *
 * @package cb-afiniti2023
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

require_once CB_THEME_DIR . '/inc/cb-posttypes.php';
require_once CB_THEME_DIR . '/inc/cb-taxonomies.php';
require_once CB_THEME_DIR . '/inc/cb-utility.php';
require_once CB_THEME_DIR . '/inc/cb-blocks.php';
require_once CB_THEME_DIR . '/inc/cb-news.php';
require_once CB_THEME_DIR . '/inc/cb-editor.php';
require_once CB_THEME_DIR . '/inc/cb-cra-levers.php';
require_once CB_THEME_DIR . '/inc/cb-cra-migrate.php';
require_once CB_THEME_DIR . '/inc/cb-cra-submit.php';
require_once CB_THEME_DIR . '/inc/cb-block-usage.php';
// require_once CB_THEME_DIR . '/inc/cb-careers.php';
require_once CB_THEME_DIR . '/inc/cb-people-contact.php';

/*
 * Remove unwanted SVG filter injection WP.
 *
 * NB: the first line does not actually stop global styles being output - WP
 * registers wp_enqueue_global_styles on both wp_enqueue_scripts and wp_footer,
 * and only the former is removed, so global styles still print. Do not "finish
 * the job": the --col-* custom properties in _props.scss alias
 * --wp--preset--color--*, which global styles define. Removing them would
 * flatten every theme colour. (Repeated verbatim further down this file.)
 */
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');


// Comment handling now lives in the cbp-blog-options plugin. comment-reply.js
// no longer needs deregistering either: functions.php only enqueues it when
// comments_open() is true, and the plugin forces that false.

add_filter('theme_page_templates', 'child_theme_remove_page_template');
function child_theme_remove_page_template( $page_templates ) {
	// unset($page_templates['page-templates/blank.php'],$page_templates['page-templates/empty.php'], $page_templates['page-templates/fullwidthpage.php'], $page_templates['page-templates/left-sidebarpage.php'], $page_templates['page-templates/right-sidebarpage.php'], $page_templates['page-templates/both-sidebarspage.php']);
	unset($page_templates['page-templates/blank.php'], $page_templates['page-templates/empty.php'], $page_templates['page-templates/left-sidebarpage.php'], $page_templates['page-templates/right-sidebarpage.php'], $page_templates['page-templates/both-sidebarspage.php']);
	return $page_templates;
}
add_action('after_setup_theme', 'remove_understrap_post_formats', 11);
function remove_understrap_post_formats() {
	remove_theme_support('post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ));
}

if ( function_exists('acf_add_options_page') ) {
	acf_add_options_page(
		array(
			'page_title' => 'Site-Wide Settings',
			'menu_title' => 'Site-Wide Settings',
			'menu_slug'  => 'theme-general-settings',
			'capability' => 'edit_posts',
		)
	);

	/*
	 * The CRA questions are global: every page running the CRA Tool template
	 * presents the same assessment, and only slide 1 differs. They used to live
	 * on the tool page itself in three questions_page_N repeaters, bound to
	 * page_template == cra-tool.php - so losing that template assignment hid
	 * every question from the editor while the values sat orphaned in postmeta.
	 *
	 * Registered as its own top-level page, NOT as a sub page of Site-Wide
	 * Settings. Passing parent_slug plus an explicit menu_slug to
	 * acf_add_options_sub_page() overwrote the *parent's* menu_slug with this
	 * one, so the Site-Wide Settings menu item opened this page instead - which
	 * looks exactly like the original settings having been wiped. They were
	 * never touched; only the menu target was wrong. Do not nest this.
	 */
	acf_add_options_page(
		array(
			'page_title' => 'CRA Questions',
			'menu_title' => 'CRA Questions',
			'menu_slug'  => 'cra-questions',
			'capability' => 'edit_posts',
			'icon_url'   => 'dashicons-forms',
			'position'   => 59,
		)
	);
}

function widgets_init() {
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
	// 'footer_menu2' => __('Footer Menu 2', 'cb-afiniti'),
	// ));
	// register_nav_menus(array(
	// 'footer_menu3' => __('Footer Menu 3', 'cb-afiniti'),
	// ));

	unregister_sidebar('hero');
	unregister_sidebar('herocanvas');
	unregister_sidebar('statichero');
	unregister_sidebar('left-sidebar');
	unregister_sidebar('right-sidebar');
	unregister_sidebar('footerfull');
	unregister_nav_menu('primary');

	/*
	 * The editor colour palette and disable-custom-colors both live in
	 * theme.json now. They used to be declared here with hex values that had
	 * drifted from the --col-* custom properties in _props.scss, so the same
	 * slug rendered two different colours depending on whether a core class or
	 * a theme class was used. theme.json is the single source; _props.scss
	 * aliases --col-* to the generated --wp--preset--color--* values.
	 */
}

/**
 * Cancels the off-site icon on new-tab links that point back at this site.
 *
 * _child_theme.scss puts a Font Awesome external-link glyph after any
 * target="_blank" link. Relative hrefs are excluded there, but an absolute link
 * to our own domain can't be recognised in CSS, which has no notion of the
 * current host. So the host is injected here instead, derived from home_url() so
 * it follows the environment rather than being hardcoded.
 *
 * Uses ^= rather than *= so a third-party URL merely containing our domain (in a
 * query string, say) still counts as off-site. The prefix must include the
 * trailing slash, or a lookalike host like afiniti.co.uk.example.com would match
 * too - hence the separate exact-match selector for a bare domain with no path.
 * !important is needed because the SCSS selector is more specific than anything
 * reasonable to write here.
 */
add_action( 'wp_head', 'cb_internal_link_icon_style', 20 );
function cb_internal_link_icon_style() {
	$home = wp_parse_url( home_url() );
	$host = $home['host'] ?? '';

	if ( ! $host ) {
		return;
	}

	if ( ! empty( $home['port'] ) ) {
		$host .= ':' . $home['port'];
	}

	// Treat www and bare forms of the domain as the same site.
	$hosts = array( $host );
	$hosts[] = str_starts_with( $host, 'www.' ) ? substr( $host, 4 ) : 'www.' . $host;

	$selectors = array();

	foreach ( $hosts as $candidate ) {
		$candidate = preg_replace( '/[^a-z0-9.\-:]/i', '', $candidate );

		if ( ! $candidate ) {
			continue;
		}

		foreach ( array( '//', 'http://', 'https://' ) as $scheme ) {
			$selectors[] = 'a[target="_blank"][href^="' . $scheme . $candidate . '/"]::after';
			$selectors[] = 'a[target="_blank"][href="' . $scheme . $candidate . '"]::after';
		}
	}

	if ( ! $selectors ) {
		return;
	}

	echo '<style id="cb-internal-link-icon">' . implode( ',', $selectors )
		. '{content:none !important;margin-left:0 !important;}</style>' . "\n";
}

/**
 * Mirrors the theme.json colour palette into add_theme_support().
 *
 * theme.json is the single source of truth for the palette, but some code reads
 * get_theme_support( 'editor-color-palette' ) directly instead of going through
 * wp_get_global_settings() - the ACF Editor Palette field, used by 18 block
 * fields, is one such. Two things go wrong without this shim:
 *
 * 1. understrap registers its own Bootstrap palette on after_setup_theme, which
 *    would resurface and offer 13 extra colours in those ACF fields.
 * 2. WP strips theme.json palette entries whose slug collides with one of its
 *    own defaults, so "white" never reaches wp_get_global_settings().
 *
 * Reading theme.json from disk sidesteps both while keeping one set of values.
 * theme.json still takes precedence for the editor and the generated CSS, so
 * this only ever affects get_theme_support() callers.
 */
add_action( 'after_setup_theme', 'cb_mirror_theme_json_palette', 20 );
function cb_mirror_theme_json_palette() {
	$theme_json = wp_json_file_decode( get_stylesheet_directory() . '/theme.json', array( 'associative' => true ) );
	$palette    = $theme_json['settings']['color']['palette'] ?? array();

	if ( $palette ) {
		add_theme_support( 'editor-color-palette', $palette );
	}
}
add_action('widgets_init', 'widgets_init', 11);


remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

// The Chillibyte dashboard widget is registered by the cbp-blog-options plugin,
// unconditionally and under the same cb_dashboard_widget id, so the copy that
// used to live here was being overwritten anyway.



// remove discussion metabox
function cc_gutenberg_register_files() {
	// script file
	wp_register_script(
		'cc-block-script',
		get_stylesheet_directory_uri() . '/js/block-script.js', // adjust the path to the JS file
		array( 'wp-blocks', 'wp-edit-post' )
	);
	// register block editor script
	register_block_type('cc/ma-block-files', array(
		'editor_script' => 'cc-block-script',
	));
}
add_action('init', 'cc_gutenberg_register_files');

function understrap_all_excerpts_get_more_link( $post_excerpt ) {
	if ( is_admin() || ! get_the_ID() ) {
		return $post_excerpt;
	}
	return $post_excerpt;
}

// * Remove Yoast SEO breadcrumbs from Revelanssi's search results
add_filter('the_content', 'wpdocs_remove_shortcode_from_index');
function wpdocs_remove_shortcode_from_index( $content ) {
	if ( is_search() ) {
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
function wd_gf_update_submit_button( $button_input, $form ) {
	// save attribute string to $button_match[1]
	preg_match('/<input([^\/>]*)(\s\/)*>/', $button_input, $button_match);

	// remove value attribute (since we aren't using an input)
	$button_atts = str_replace("value='" . $form['button']['text'] . "' ", '', $button_match[1]);

	// create the button element with the button text inside the button element instead of set as the value
	return '<button ' . $button_atts . '><span>' . $form['button']['text'] . '</span></button>';
}
add_filter('gform_submit_button', 'wd_gf_update_submit_button', 10, 2);


function cb_theme_enqueue() {
	$the_theme = wp_get_theme();
	// wp_enqueue_style('lightbox-stylesheet', get_stylesheet_directory_uri() . '/css/lightbox.min.css', array(), $the_theme->get('Version'));
	// wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox-plus-jquery.min.js', array(), $the_theme->get('Version'), true);
	// wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox.min.js', array(), $the_theme->get('Version'), true);
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.3.min.js', array(), null, true);

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$has_swiper_block = has_block( 'acf/cb-text-image', $post_id ) || has_block( 'acf/cb-people-cta', $post_id );

		if ( $has_swiper_block ) {
			wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.2.10' );
			wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.2.10', false );
		}
	}
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
final class ExtendedWpImageEditorImagick extends WP_Image_Editor_Imagick {

	/**
	 * Add properties to the image produced by Ghostscript to prevent black PDF backgrounds.
	 *
	 * @return true|WP_error
	 */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	protected function pdf_load_source() {
		$loaded = parent::pdf_load_source();

		try {
			$this->image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
			$this->image->setBackgroundColor('#ffffff');
		} catch ( Exception $exception ) {
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
add_filter('wp_image_editors', function ( array $editors ): array {
	array_unshift($editors, ExtendedWpImageEditorImagick::class);

	return $editors;
});



add_shortcode('cb_all_people', function () {
	ob_start();

	$args = array(
		'post_type'      => 'people',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	$people = new WP_Query($args);

	$contact_form_id    = cb_people_get_contact_form_id();
	$fields             = $contact_form_id ? cb_people_resolve_form_fields( $contact_form_id ) : null;
	$recipient_field_id = ( $fields && ! empty( $fields['recipient'] ) ) ? (int) $fields['recipient'] : 0;

	?>
<div class="container-xl">
	<div class="row g-4">
		<?php
		while ( $people->have_posts() ) {
			$people->the_post();
			$full_name  = get_the_title();
			$name_parts = explode( ' ', $full_name, 2 );
			$first_name = $name_parts[0];
			?>
		<div class="col-md-6 col-lg-4 pb-4" itemscope="itemscope" itemtype="https://schema.org/Person">
			<a href="<?= get_the_permalink(); ?>">
				<div class="person-photo-container mb-2">
					<div class="person-photo"
						style="background-image:url(<?= wp_get_attachment_image_url(get_field('photo'), 'large'); ?>"
						itemprop="image"></div>
				</div>
			</a>
			<div class="fs-5 text--green fw-bold" itemprop="name">
				<?= get_the_title(); ?>
			</div>
			<div class="pb-2" itemprop="jobTitle">
				<?= get_field('job_title'); ?>
			</div>
			<div class="pb-2 person-description" itemprop="description">
				<?= wp_trim_words(get_the_content(), 18); ?>
			</div>
			<div class="person-links">
				<a class="more-circle"
					href="<?= get_the_permalink(); ?>"><span
						class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i
							class="fa-solid fa-plus fa-stack-1x fa-inverse"></i></span></a>
				<?php if ( $contact_form_id ) { ?>
				<a href="#modal-contact-person"
					class="cb-people__contact-link cb-people__contact-link--contact"
					data-bs-toggle="modal"
					data-bs-target="#modal-contact-person"
					data-person-id="<?= esc_attr( get_the_ID() ); ?>"
					data-person-firstname="<?= esc_attr( $first_name ); ?>"
					data-person-fullname="<?= esc_attr( $full_name ); ?>"
					aria-label="Contact <?= esc_attr( $first_name ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
				</a>
				<?php } ?>
			</div>
		</div>
			<?php
		}

		?>
	</div>
</div>
	<?php

	// Render the shared contact modal once per page.
	if ( $contact_form_id ) {
		cb_people_render_contact_modal( $contact_form_id, $recipient_field_id );
	}

	return ob_get_clean();
});

// add role to people index
function add_acf_columns( $columns ) {
	return array_merge($columns, array(
		'job_title' => __('Job Title'),
	));
}
add_filter('manage_people_posts_columns', 'add_acf_columns');

function people_custom_column( $column, $post_id ) {
	switch ( $column ) {
		case 'job_title':
			echo get_post_meta($post_id, 'job_title', true);
			break;
	}
}
add_action('manage_people_posts_custom_column', 'people_custom_column', 10, 2);

add_filter('manage_people_posts_columns', 'column_order');
function column_order( $columns ) {
	$n_columns = array();
	$move      = 'job_title'; // what to move
	$before    = 'date'; // move before this
	foreach ( $columns as $key => $value ) {
		if ( $key == $before ) {
			$n_columns[ $move ] = $move;
		}
		$n_columns[ $key ] = $value;
	}
	return $n_columns;
}


// careers functions
add_shortcode('cb_current_opportunities', function () {
	ob_start();
	$args    = array(
		'post_type'      => 'careers',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);
	$careers = new WP_Query($args);

	?>
<section class="current_opportunities bg--green-500 py-5 mb-5">
	<div class="container-xl">
		<h2 class="mb-4">Current Opportunities</h2>
		<?php
		if ( $careers->have_posts() ) {
			if ( $careers->found_posts == 1 ) {
				while ( $careers->have_posts() ) {
					$careers->the_post();
					?>
		<div class="row g-4">
			<div class="col-md-8">
				<a href="<?= get_the_permalink(); ?>"
					class="text-white">
					<p class="fs-5 fw-bold"><?= get_the_title(); ?></p>
					<p><?= get_field('description', get_the_ID()); ?>
					</p>
					<div class="fw-bold text-white anim-arrow--slide noline"
						href="<?= get_the_permalink(); ?>">Read more
						<span class="arrow"></span>
					</div>
				</a>
			</div>
			<div class="col-md-4 justify-content-center">
				<img src="<?= get_stylesheet_directory_uri(); ?>/img/illustrations/Ship-Anchored.png"
					alt="">
			</div>
		</div>
					<?php

				}
			} elseif ( $careers->found_posts == 2 ) {
				?>
		<div class="row g-4 mx-0">
			<div class="order-1 order-lg-3 col-lg-4 text-center">
				<img src="<?= get_stylesheet_directory_uri(); ?>/img/illustrations/Ship-Anchored.png"
					class="w-75 w-md-50 w-lg-100">
			</div>
				<?php
				while ( $careers->have_posts() ) {
					$careers->the_post();
					?>
			<div class="col-lg-4 order-2 order-lg-1">
				<a href="<?= get_the_permalink(); ?>"
					class="text-white">
					<p class="fs-5 fw-bold"><?= get_the_title(); ?></p>
					<p><?= get_field('description', get_the_ID()); ?>
					</p>
					<div class="fw-bold text-white anim-arrow--slide noline"
						href="<?= get_the_permalink(); ?>">Read more
						<span class="arrow"></span>
					</div>
				</a>
			</div>
					<?php

				}
				?>
		</div>
				<?php
			} elseif ( $careers->found_posts == 3 ) {
				?>
		<div class="row g-4 mx-0">
				<?php
				while ( $careers->have_posts() ) {
					$careers->the_post();
					?>
			<div class="col-lg-4">
				<a href="<?= get_the_permalink(); ?>"
					class="text-white">
					<p class="fs-5 fw-bold"><?= get_the_title(); ?></p>
					<p><?= get_field('description', get_the_ID()); ?>
					</p>
					<div class="fw-bold text-white anim-arrow--slide noline"
						href="<?= get_the_permalink(); ?>">Read more
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
				<img src="<?= get_stylesheet_directory_uri(); ?>/img/illustrations/Ship-Anchored.png"
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

function override_yoast_breadcrumb_trail_stories( $links ) {
	global $post;

	if ( is_singular('case-studies') ) {
		$breadcrumb[] = array(
			'url'  => '/case-studies/',
			'text' => 'Case Studies',
		);
		array_splice($links, 1, -2, $breadcrumb);
	}
	if ( is_singular('careers') ) {
		$breadcrumb[] = array(
			'url'  => '/careers/',
			'text' => 'Careers',
		);
		array_splice($links, 1, -2, $breadcrumb);
	}

	return $links;
}

add_filter('relevanssi_modify_wp_query', function ( $query ) {
	if ( empty($query->query_vars['sentence']) ) {
		unset($query->query_vars['sentence']);
	}
	return $query;
});


// disable canonicals (for WPE)
add_filter('wpseo_canonical', '__return_false');

// CSV CRA Download
function cb_register_cra_menu_page() {
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

function cra_download_callback() {
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
		action="<?= get_stylesheet_directory_uri(); ?>/cra-results.php"
		method="POST" class="form">
		<div><label for="start">Start Date</label><input type="date" name="start" id="start"
				value="<?= date('Y-m-01'); ?>">
		</div>
		<div><label for="end">End Date</label><input type="date" name="end" id="end"
				value="<?= date('Y-m-d'); ?>"></div>
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

add_filter('acf/update_value', 'cb_acf_save_revisions', 10, 3);
function cb_acf_save_revisions( $value, $post_id, $field ) {
	// Trigger a revision save.
	if ( get_post_type($post_id) !== 'acf-field-group' ) {
		wp_save_post_revision($post_id);
	}
	return $value;
}


// Honeytrap
add_filter('gform_validation_5', function ( $validation_result ) {
	$form = $validation_result['form'];

	foreach ( $form['fields'] as &$field ) {
		if ( $field->label === 'Referral Code' ) {
			$value = rgpost("input_{$field->id}");

			// If the field has a value, mark as spam
			if ( ! empty($value) ) {
				$field->failed_validation      = true;
				$field->validation_message     = 'Spam detected.';
				$validation_result['is_valid'] = false;
			}
		}
	}

	$validation_result['form'] = $form;
	return $validation_result;
});
