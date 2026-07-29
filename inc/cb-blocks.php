<?php

/**
 * Register ACF Blocks
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

/**
 * Parses the --col-* custom properties out of the :root block in
 * _props.scss so they can be offered as ACF select choices, instead
 * of keeping a hand-maintained list in sync with the theme's colours.
 */
function cb_get_theme_colour_choices() {
    static $choices = null;

    if ( $choices !== null ) {
        return $choices;
    }

    $choices   = array();
    $scss_path = get_stylesheet_directory() . '/src/sass/theme/_props.scss';

    if ( ! file_exists( $scss_path ) ) {
        return $choices;
    }

    $scss = file_get_contents( $scss_path );

    if ( ! preg_match( '/:root\s*\{(.*?)\}/s', $scss, $root_match ) ) {
        return $choices;
    }

    foreach ( explode( "\n", $root_match[1] ) as $line ) {
        $line = trim( $line );

        if ( $line === '' || strpos( $line, '//' ) === 0 ) {
            continue;
        }

        if ( preg_match( '/^--(col-[a-z0-9-]+)\s*:\s*(.+?);/i', $line, $match ) ) {
            $slug           = $match[1];
            $label          = ucwords( str_replace( '-', ' ', substr( $slug, 4 ) ) );
            $choices[ $slug ] = $label;
        }
    }

    return $choices;
}

/**
 * Populates any ACF select field named "*_colour" with the theme's
 * --col-* custom properties, so new colours only need adding in one
 * place (_props.scss) to become pickable in the block editor.
 */
add_filter( 'acf/load_field', 'cb_load_colour_select_choices' );
function cb_load_colour_select_choices( $field ) {
    if ( $field['type'] === 'select' && substr( $field['name'], -7 ) === '_colour' ) {
        $field['choices'] = cb_get_theme_colour_choices();
    }

    return $field;
}

function acf_blocks() {
	if ( function_exists( 'acf_register_block_type' ) ) {

		// INSERT NEW BLOCKS HERE.

		acf_register_block_type(
			array(
				'name'            => 'cb_related_posts',
				'title'           => __( 'CB Related Posts' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-related-posts.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_val_prop',
				'title'           => __( 'CB Val Prop' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-val-prop.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_hover_q_a',
				'title'           => __( 'CB Hover Q&A' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-hover-q-a.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => true,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_stat_spinner',
				'title'           => __( 'CB Stat Spinner' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-stat-spinner.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
					'color'     => array(
						'background' => false,
						'text'       => true,
					),
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_vimeo_video_feature',
				'title'           => __( 'CB Vimeo Video Feature' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-vimeo-video-feature.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);

		acf_register_block_type(
			array(
				'name'            => 'cb_hero',
				'title'           => __( 'CB Hero' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-hero.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_hero_illustration',
				'title'           => __( 'CB Hero Illustration' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-hero-illustration.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_intro_block',
				'title'           => __( 'CB Intro Block' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-intro-block.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_page_intro_block',
				'title'           => __( 'CB Page Intro Block' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-page-intro-block.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_vimeo',
				'title'           => __( 'CB Vimeo' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-vimeo.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_pillar_nav',
				'title'           => __( 'CB Pillar Navigation' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-pillar-nav.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_pillar_nav_short',
				'title'           => __( 'CB Pillar Navigation (Short)' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-pillar-nav-short.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
		)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_pillar_nav_editable',
				'title'           => __( 'CB Pillar Navigation (Editable)' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-pillar-nav-editable.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
				),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_accreditation_carousel',
				'title'           => __( 'CB Accreditation Carousel' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-accreditation-carousel.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_text_image',
				'title'           => __( 'CB Text Image' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-text-image.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_quote',
				'title'           => __( 'CB Quote' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-quote.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_divider',
				'title'           => __( 'CB Divider' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-divider.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_image_divider',
				'title'           => __( 'CB Image Divider' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-image-divider.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_tab_divider',
				'title'           => __( 'CB Tab Divider' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-tab-divider.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_business_change_tabs',
				'title'           => __( 'CB Business Change Tabs' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-business-change-tabs.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_two_col_img_text',
				'title'           => __( 'CB 2-Col Image/Text' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-two-col-img-text.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_three_col_text_icon',
				'title'           => __( 'CB 3-Col Icon/Text' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-three-col-text-icon.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_latest_insights',
				'title'           => __( 'CB Latest Insights' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-latest-insights.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_team_insights',
				'title'           => __( 'CB Team Insights' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-team-insights.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_all_case_studies',
				'title'           => __( 'CB All Case Studies' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-all-case-studies.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_featured_experience',
				'title'           => __( 'CB Featured and Experience' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-featured-experience.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_qa_tool',
				'title'           => __( 'CB QA Tool' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-qa-tool.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_related_case_studies',
				'title'           => __( 'CB Related Case Studies' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-related-case-studies.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_related_insights',
				'title'           => __( 'CB Related Insights' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-related-insights.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_tabs',
				'title'           => __( 'CB Tabs' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-tabs.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_popup',
				'title'           => __( 'CB Popup' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-popup.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_faq',
				'title'           => __( 'CB FAQs' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-faq.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'   => false,
					'anchor' => true,
				),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_five_steps',
				'title'           => __( 'CB Five Steps' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-five-steps.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_hub_insights',
				'title'           => __( 'CB Hub Insights' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-hub-insights.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_people_cta',
				'title'           => __( 'CB People CTA' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-people-cta.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_dt_diagram',
				'title'           => __( 'CB Digital Transformation' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-dt-diagram.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_video_feature',
				'title'           => __( 'CB Video Feature' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-video-feature.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_video_carousel',
				'title'           => __( 'CB Video Carousel' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-video-carousel.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
		acf_register_block_type(
			array(
				'name'            => 'cb_simple_cta',
				'title'           => __( 'CB Simple CTA' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-simple-cta.php',
				'mode'            => 'edit',
				'supports'        => array( 'mode' => false ),
			)
		);
	}
}
add_action( 'acf/init', 'acf_blocks' );



// Gutenburg core modifications
add_filter( 'register_block_type_args', 'core_image_block_type_args', 10, 3 );
function core_image_block_type_args( $args, $name ) {
	if ( 'core/paragraph' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	if ( 'core/list' === $name ) {
		$args['render_callback'] = 'modify_core_list';
	}
	if ( 'core/columns' === $name ) {
		$args['render_callback'] = 'modify_core_add_container';
	}
	if ( 'core/heading' === $name ) {
		$args['render_callback'] = 'modify_core_heading';
	}
	return $args;
}

function modify_core_add_container( $attributes, $content ) {
	ob_start();
	?>
<div class="container-xl">
	<?= $content; ?>
</div>
	<?php
	$content = ob_get_clean();
	return $content;
}

function modify_core_heading( $attributes, $content ) {
	ob_start();
	$id = strtolower( wp_strip_all_tags( $content ) );
	$id = cbslugify( $id );
	?>
<div class="container-xl" id="<?= $id; ?>">
	<?= $content; ?>
</div>
	<?php
	$content = ob_get_clean();
	return $content;
}

function modify_core_list( $attributes, $content ) {
	ob_start();
	?>
<div class="container-xl ps-5">
	<?= $content; ?>
</div>
	<?php
	$content = ob_get_clean();
	return $content;
}
