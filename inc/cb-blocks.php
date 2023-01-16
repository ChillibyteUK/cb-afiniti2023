<?php
function acf_blocks() {
	if( function_exists('acf_register_block_type') ) {
		acf_register_block_type(array(
			'name'				=> 'cb_hero',
			'title'				=> __('CB Hero'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_hero.php',
			'keywords'			=> array( 'hero' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_page_title',
			'title'				=> __('CB Page Title'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_page_title.php',
			'keywords'			=> array( 'page', 'title' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_bg_panel',
			'title'				=> __('CB BG Panel'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_bg_panel.php',
			'keywords'			=> array( 'panel' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_story_slider',
			'title'				=> __('CB Story Slider'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_story_slider.php',
			'keywords'			=> array( 'panel' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => true, 'anchor' => true),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_nav_cards',
			'title'				=> __('CB Nav Cards'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_nav_cards.php',
			'keywords'			=> array( 'panel' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_latest_news',
			'title'				=> __('CB Latest News'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_latest_news.php',
			'keywords'			=> array( 'panel' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
		));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_page_children',
		// 	'title'				=> __('CB Page Children'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_children.php',
		// 	'keywords'			=> array( 'page', 'children' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
		acf_register_block_type(array(
			'name'				=> 'cb_newsletter',
			'title'				=> __('CB Newsletter'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_newsletter.php',
			'keywords'			=> array( 'newsletter' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_quick_links',
			'title'				=> __('CB Quick Links'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_quick_links.php',
			'keywords'			=> array( 'newsletter' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_people_cards',
			'title'				=> __('CB People cards'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_people_cards.php',
			'keywords'			=> array( 'people', 'cards' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_cards',
			'title'				=> __('CB Cards'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_cards.php',
			'keywords'			=> array( 'cards' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_fancy_cards',
			'title'				=> __('CB Fancy Cards'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_fancy_cards.php',
			'keywords'			=> array( 'fancy', 'cards' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_all_stories',
			'title'				=> __('CB All Stories'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_all_stories.php',
			'keywords'			=> array( 'all', 'stories' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_downloads',
			'title'				=> __('CB Downloads'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_downloads.php',
			'keywords'			=> array( 'downloads' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_posts_by_tag',
			'title'				=> __('CB Posts by Tag'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_posts_by_tag.php',
			'keywords'			=> array( 'posts', 'by', 'tag', 'related' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_partner_list',
			'title'				=> __('CB Partner List'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_partner_list.php',
			'keywords'			=> array( 'partner', 'list' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_partner_wall',
			'title'				=> __('CB Partner Wall'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_partner_wall.php',
			'keywords'			=> array( 'partner', 'wall' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_links',
			'title'				=> __('CB Links'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_links.php',
			'keywords'			=> array( 'links' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_breadcrumbs',
			'title'				=> __('CB Breadcrumbs'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_breadcrumbs.php',
			'keywords'			=> array( 'breadcrumbs' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_faq_block',
			'title'				=> __('CB FAQ Block'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_faq_block.php',
			'keywords'			=> array( 'faq', 'block' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_dynamic_form',
			'title'				=> __('CB Dynamic Contact Form'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_dynamic_form.php',
			'keywords'			=> array( 'dynamic', 'form' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_gallery',
			'title'				=> __('CB Gallery'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_gallery.php',
			'keywords'			=> array( 'gallery' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_banner',
			'title'				=> __('CB Banner'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_banner.php',
			'keywords'			=> array( 'banner' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_timeline',
			'title'				=> __('CB Timeline'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_timeline.php',
			'keywords'			=> array( 'timeline' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => true),
		));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_icon_cards',
		// 	'title'				=> __('CB Icon Cards'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_icon_cards.php',
		// 	'keywords'			=> array( 'icon', 'cards' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_text_icon_block',
		// 	'title'				=> __('CB Text Icon Block'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_text_icon_block.php',
		// 	'keywords'			=> array( 'text', 'icon', 'block' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false, 'anchor' => true),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_quote_text_block',
		// 	'title'				=> __('CB Quote Text Block'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_quote_text_block.php',
		// 	'keywords'			=> array( 'text', 'quote', 'block' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_partners',
		// 	'title'				=> __('CB Partners Grid'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_partners.php',
		// 	'keywords'			=> array( 'partners', 'grid' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_partners_slider',
		// 	'title'				=> __('CB Partners Slider'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_partners_slider.php',
		// 	'keywords'			=> array( 'partners', 'slider' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_people',
		// 	'title'				=> __('CB Team Grid'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_people.php',
		// 	'keywords'			=> array( 'team', 'people', 'grid' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_anim_heading',
		// 	'title'				=> __('CB Animated Heading'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_anim_heading.php',
		// 	'keywords'			=> array( 'anim', 'heading' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false, 'anchor' => true),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_anim_image',
		// 	'title'				=> __('CB Animated Image'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_anim_image.php',
		// 	'keywords'			=> array( 'anim', 'image' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false, 'anchor' => true),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_address_map',
		// 	'title'				=> __('CB Address Map'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_address_map.php',
		// 	'keywords'			=> array( 'address', 'map' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_faq_block',
		// 	'title'				=> __('CB FAQ Block'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_faq_block.php',
		// 	'keywords'			=> array( 'faq', 'block' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
		acf_register_block_type(array(
			'name'				=> 'cb_button_bar',
			'title'				=> __('CB Button Bar'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_button_bar.php',
			'keywords'			=> array( 'button', 'bar' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		acf_register_block_type(array(
			'name'				=> 'cb_posts_by_category',
			'title'				=> __('CB Posts by Category'),
			'category'			=> 'layout',
			'icon'				=> 'cover-image',
			'render_template'	=> 'page-templates/blocks/cb_posts_by_category.php',
			'keywords'			=> array( 'posts', 'category' ),
			'mode'	=> 'edit',
            'supports' => array('mode' => false),
		));
		// acf_register_block_type(array(
		// 	'name'				=> 'cb_icon_accordion',
		// 	'title'				=> __('CB Icon Accordion'),
		// 	'category'			=> 'layout',
		// 	'icon'				=> 'cover-image',
		// 	'render_template'	=> 'page-templates/blocks/cb_icon_accordion.php',
		// 	'keywords'			=> array( 'icon', 'accordion' ),
		// 	'mode'	=> 'edit',
        //     'supports' => array('mode' => false),
		// ));
    }
}
add_action('acf/init', 'acf_blocks');

// Gutenburg core modifications
add_filter( 'register_block_type_args', 'core_image_block_type_args', 10, 3 );
function core_image_block_type_args( $args, $name ) {
    if ( $name == 'core/paragraph' ) {
        $args['render_callback'] = 'modify_core_add_container';
    }
    if ( $name == 'core/list' ) {
        $args['render_callback'] = 'modify_core_add_container';
    }
    if ( $name == 'core/columns' ) {
        // $args['render_callback'] = 'modify_core_add_container';
    }
    if ( $name == 'core/heading' ) {
        $args['render_callback'] = 'modify_core_heading';
    }
    if ( $name == 'core/button' ) {
        $args['render_callback'] = 'modify_core_buttons';
    }
    if ( $name == 'core/quote' ) {
        $args['render_callback'] = 'modify_core_quote';
    }

	// echo '<pre>' . $name . '</pre>';

    return $args;
}

function modify_core_add_container($attributes, $content) {
    ob_start();
	// $class = $block['className'];
    ?>
    <div class="container-xl">
		<?=$content?>
    </div>
    <?php
    $content = ob_get_clean();
	return $content;
}

function modify_core_heading($attributes, $content) {
    ob_start();
    $id = strtolower( wp_strip_all_tags( $content ) );
    $id = cbslugify($id);
    ?>
    <div class="container-xl" id="<?=$id?>">
        <?=$content?>
    </div>
    <?php
    $content = ob_get_clean();
	return $content;
}

function modify_core_buttons($attributes, $content) {
	ob_start();

	$btn = $content;

	preg_match('/class="wp-block-button (.*?)"/', $btn, $class);
	
	preg_match('/href="(.*?)"/', $btn, $link);

	preg_match('/target="(.*?)" rel="(.*?)"/', $btn, $target);

	preg_match('/<a.*?>(.*?)<\/a>/', $btn, $label);

	?>
	<!-- core/buttons -->
	<div class="container-xl clearfix mb-4"><a class="btn <?=$class[1]?>" href="<?=$link[1]?>" target="<?=$target[1]?>" rel="<?=$target[2]?>"><?=$label[1]?></a></div>
	<?php
	$content = ob_get_clean();
	return $content;
}

function modify_core_quote($attributes, $content) {
	ob_start();

    ?>
	<!-- wp_quote -->
    <div class="container-xl">
		<div class="wp-block-quote--cb my-4 w-md-75 mx-auto">
			<div class="overlay"></div>
			<?=$content?>
		</div>
    </div>
    <?php

	$content = ob_get_clean();
	return $content;
}