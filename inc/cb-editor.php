<?php
/**
 * Block editor tweaks.
 *
 * `src/sass/custom-editor-style.scss` has existed since the theme was created and
 * `npm run css` has been compiling it to `css/custom-editor-style{,.min}.css` all
 * along - but nothing ever called add_editor_style(), so none of it has ever
 * loaded. That is fixed here, and the stylesheet now also contains top-level
 * blocks to a page-width column instead of letting them run full-bleed.
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
