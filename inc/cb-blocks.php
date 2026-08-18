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

	if ( null !== $choices ) {
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

		if ( '' === $line || strpos( $line, '//' ) === 0 ) {
			continue;
		}

		if ( preg_match( '/^--(col-[a-z0-9-]+)\s*:\s*(.+?);/i', $line, $match ) ) {
			$slug             = $match[1];
			$label            = ucwords( str_replace( '-', ' ', substr( $slug, 4 ) ) );
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
	if ( 'select' === $field['type'] && substr( $field['name'], -7 ) === '_colour' ) {
		$field['choices'] = cb_get_theme_colour_choices();
	}

	return $field;
}

function acf_blocks() {
	if ( function_exists( 'acf_register_block_type' ) ) {

		// INSERT NEW BLOCKS HERE.

		acf_register_block_type(
			array(
				'name'            => 'cb_nav_buttons',
				'title'           => __( 'CB Nav Buttons' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-nav-buttons.php',
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
				'name'            => 'cb_cra_hero',
				'title'           => __( 'CB CRA Hero' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/cb-cra-hero.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => false,
					'className' => false,
					'align'     => false,
					'multiple'  => false,
				),
			)
		);

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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
					'className' => true,
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array( 'mode' => false,
					'className' => true,
					'anchor'    => true,
				),
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
				'supports'        => array(
					'mode'      => false,
					'className' => true,
					'anchor'    => true,
				),
			)
		);
	}
}
add_action( 'acf/init', 'acf_blocks' );



/**
 * Applies "Additional CSS class(es)" and the HTML anchor to ACF block output.
 *
 * Every non-hero ACF block declares `className` and `anchor` support, which makes
 * the fields appear in the editor - but an ACF `render_template` block only gets
 * `$block['className']` / `$block['anchor']` handed to the template, and nothing
 * outputs them unless the template does. Around two thirds of the templates never
 * did, so the fields would have appeared and then done nothing.
 *
 * Rather than edit thirty-odd templates and their differing root elements, the
 * values are merged into the first opening tag of the rendered output here. This
 * mirrors how core blocks are already post-processed further down this file.
 *
 * Idempotent by design:
 *
 * - classes already on the element are not repeated, so the ~25 templates that do
 *   output `$block['className']` themselves keep working and gain nothing extra;
 * - an existing `id` is left alone, so a template that renders its own anchor wins.
 *
 * Hero blocks declare `className => false` / no anchor, so nothing is injected
 * into them.
 *
 * @param string $content Rendered block HTML.
 * @param array  $block   Parsed block.
 * @return string
 */
function cb_acf_block_wrapper_attributes( $content, $block ) {
	$name = $block['blockName'] ?? '';

	if ( 0 !== strpos( (string) $name, 'acf/' ) || '' === trim( (string) $content ) ) {
		return $content;
	}

	/*
	 * Gated on what the block actually declares, not just on the attribute being
	 * present. Hero blocks opt out (className => false, no anchor), and a block
	 * saved with a stale class before support was removed must not have it
	 * silently reinstated here.
	 */
	$type     = WP_Block_Type_Registry::get_instance()->get_registered( $name );
	$supports = $type ? (array) $type->supports : array();

	$attrs  = $block['attrs'] ?? array();
	$class  = empty( $supports['className'] ) ? '' : trim( (string) ( $attrs['className'] ?? '' ) );
	$anchor = empty( $supports['anchor'] ) ? '' : trim( (string) ( $attrs['anchor'] ?? '' ) );

	if ( '' === $class && '' === $anchor ) {
		return $content;
	}

	/*
	 * The first opening tag is the block's root element. Leading HTML comments -
	 * several templates start with one, e.g. <!-- text_image --> - cannot match
	 * because the pattern requires a letter straight after the "<".
	 */
	if ( ! preg_match( '/<([a-z][a-z0-9-]*)((?:"[^"]*"|\'[^\']*\'|[^>"\'])*)>/i', $content, $match, PREG_OFFSET_CAPTURE ) ) {
		return $content;
	}

	$whole    = $match[0][0];
	$offset   = (int) $match[0][1];
	$tag      = $match[1][0];
	$existing = $match[2][0];
	$updated  = $existing;

	if ( '' !== $class ) {
		$wanted = preg_split( '/\s+/', $class, -1, PREG_SPLIT_NO_EMPTY );

		if ( preg_match( '/\sclass\s*=\s*"([^"]*)"/i', $updated, $class_match ) ) {
			$present = preg_split( '/\s+/', trim( $class_match[1] ), -1, PREG_SPLIT_NO_EMPTY );
			$add     = array_diff( $wanted, $present );

			if ( $add ) {
				// The existing value is left as-is; only the addition is escaped,
				// so nothing gets double-encoded.
				$merged  = trim( $class_match[1] ) . ' ' . esc_attr( implode( ' ', $add ) );
				$updated = preg_replace(
					'/\sclass\s*=\s*"[^"]*"/i',
					' class="' . $merged . '"',
					$updated,
					1
				);
			}
		} else {
			$updated .= ' class="' . esc_attr( $class ) . '"';
		}
	}

	if ( '' !== $anchor && ! preg_match( '/\sid\s*=\s*["\']/i', $updated ) ) {
		$updated .= ' id="' . esc_attr( $anchor ) . '"';
	}

	if ( $updated === $existing ) {
		return $content;
	}

	return substr_replace( $content, '<' . $tag . $updated . '>', $offset, strlen( $whole ) );
}
add_filter( 'render_block', 'cb_acf_block_wrapper_attributes', 10, 2 );

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
