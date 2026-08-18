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
