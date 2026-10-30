<?php
/**
 * Block template for CB Hero Illustration 2026.
 *
 * Same illustrations as CB Hero Illustration, positioned with an absolutely
 * positioned stage instead of negative offsets. The scenes are in
 * page-templates/anim-2026/ and styled in _cb_hero_illustration_2026.scss.
 * The original block and page-templates/anim/ are untouched.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$anim = sanitize_file_name( (string) get_field( 'illustration' ) );

if ( '' === $anim ) {
	return;
}

$scene = get_stylesheet_directory() . '/page-templates/anim-2026/' . $anim . '.php';

if ( ! file_exists( $scene ) ) {
	return;
}

require $scene;
