<?php
/**
 * Block editor tweaks.
 *
 * NOTE: `css/custom-editor-style.min.css` is **already** loaded into the editor,
 * and always has been - understrap's own `inc/editor.php` calls
 * `add_editor_style( 'css/custom-editor-style.min.css' )` on `admin_init`, and
 * `add_editor_style()` resolves child theme first, so the parent's call picks up
 * *our* file. Do not add it again here; it would just load twice.
 *
 * What was genuinely missing is the theme's real frontend stylesheet, which is
 * what this adds.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

/**
 * Loads the theme's stylesheets into the block editor iframe.
 *
 * Two files, and the order matters:
 *
 * 1. `child-theme.min.css` - the real frontend stylesheet, so the editor shows
 *    the actual fonts, colours and block styles rather than a hand-picked subset.
 *    It also defines the `--col-*` custom properties in `:root`, which the editor
 *    stylesheet's colour rules need resolved in the same document.
 * 2. `custom-editor-style.min.css` - editor-only, and never served on the
 *    frontend.
 *
 * `add_editor_style()` needs `editor-styles` theme support, added below. Without
 * it WordPress enqueues nothing and this is a silent no-op.
 *
 * @return void
 */
function cb_add_editor_styles() {
	add_theme_support( 'editor-styles' );
	add_editor_style(
		array(
			'css/child-theme.min.css',
			'css/custom-editor-style.min.css',
		)
	);
}
add_action( 'after_setup_theme', 'cb_add_editor_styles' );

/**
 * Puts each block's alignment into a class on its editor wrapper.
 *
 * A group set to full width stores `align: "full"` in its attributes, but this
 * install renders no marker for it in the editor DOM - no `data-align` attribute
 * and no `.alignfull` class - so the editor stylesheet has nothing to target and
 * cannot tell a full-width group from a normal one. (WordPress only emits those
 * markers when a root layout exists, which needs `settings.layout` in theme.json;
 * see CLAUDE.md for why that has not been added.)
 *
 * This adds `cb-align-full` / `cb-align-wide` to the block wrapper in the editor,
 * purely so `custom-editor-style.scss` can leave those blocks unconstrained while
 * everything else stays in a centred column. Editor only - it changes nothing
 * about the saved content or the frontend.
 *
 * @return void
 */
function cb_editor_align_classes() {
	$script = <<<'JS'
( function ( hooks, compose, element ) {
	if ( ! hooks || ! compose || ! element ) {
		return;
	}

	hooks.addFilter(
		'editor.BlockListBlock',
		'cb/editor-align-class',
		compose.createHigherOrderComponent( function ( BlockListBlock ) {
			return function ( props ) {
				var align = props.attributes && props.attributes.align;

				if ( ! align ) {
					return element.createElement( BlockListBlock, props );
				}

				var extended = Object.assign( {}, props, {
					className: ( props.className || '' ) + ' cb-align-' + align,
				} );

				return element.createElement( BlockListBlock, extended );
			};
		}, 'cbEditorAlignClass' )
	);
}( window.wp && wp.hooks, window.wp && wp.compose, window.wp && wp.element ) );
JS;

	wp_add_inline_script( 'wp-block-editor', $script );
}
add_action( 'enqueue_block_editor_assets', 'cb_editor_align_classes' );

/**
 * Stops the block editor opening in fullscreen mode.
 *
 * Ported from cb-global42026, but rewritten rather than copied. That version
 * used `jQuery( window ).load()`, removed in jQuery 3 and only still working
 * here because jQuery Migrate patches it, and `core/edit-post`'s
 * `isFeatureActive()` / `toggleFeature()`, deprecated in favour of the
 * `core/preferences` store. Both were checked against this install (jQuery
 * 3.7.1, WordPress 7.0.4) before deciding.
 *
 * More importantly it changed behaviour: it toggled fullscreen off on **every**
 * load, so an editor who deliberately turned fullscreen on found it off again
 * next time and could never make it stick. `setDefaults()` only changes the
 * default, which a persisted user preference still overrides - so the editor
 * opens windowed for anyone who has not expressed a preference, and anyone who
 * wants fullscreen keeps it.
 *
 * To force it off regardless of the user's choice, swap setDefaults() for:
 *   if ( p.get( 'core', 'fullscreenMode' ) ) { d.toggle( 'core', 'fullscreenMode' ); }
 *
 * The ACF Visual/Text focus workaround from that same file was deliberately not
 * ported: `cbp-blog-options` already fixes it, and more thoroughly - it forces
 * `delay: true` on WYSIWYG fields so TinyMCE never initialises until clicked,
 * and guards the scroll position across ACF DOM mutations. The theme version
 * monkey-patched `switchEditors.go` instead, which would be a second competing
 * fix for the same problem.
 *
 * @return void
 */
function cb_editor_windowed_by_default() {
	$script = <<<'JS'
wp.domReady( function () {
	if ( ! window.wp || ! wp.data ) {
		return;
	}

	var prefs = wp.data.dispatch( 'core/preferences' );

	if ( prefs && typeof prefs.setDefaults === 'function' ) {
		prefs.setDefaults( 'core', { fullscreenMode: false } );
		return;
	}

	// Pre-6.5 fallback. Toggles rather than defaults, so it overrides the user.
	var editPost = wp.data.select( 'core/edit-post' );
	var dispatchEditPost = wp.data.dispatch( 'core/edit-post' );

	if ( editPost && typeof editPost.isFeatureActive === 'function' && editPost.isFeatureActive( 'fullscreenMode' ) ) {
		dispatchEditPost.toggleFeature( 'fullscreenMode' );
	}
} );
JS;

	wp_add_inline_script( 'wp-blocks', $script );
}
add_action( 'enqueue_block_editor_assets', 'cb_editor_windowed_by_default' );
