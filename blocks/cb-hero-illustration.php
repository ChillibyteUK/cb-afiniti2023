<?php
/**
 * Block template for CB Hero Illustration.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$anim = get_field( 'illustration' );

if ( empty( $anim ) ) {
	return;
}

require get_stylesheet_directory() . '/page-templates/anim/' . $anim . '.php';
