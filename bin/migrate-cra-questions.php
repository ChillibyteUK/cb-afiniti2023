<?php
/**
 * wp-cli wrapper around cb_cra_migrate_questions().
 *
 * The migration itself lives in inc/cb-cra-migrate.php, because WP Engine has no
 * wp-cli and it has to be runnable from `Tools > CRA Migration` there. This is
 * the same function with a terminal in front of it - do not reimplement it here.
 *
 * Arguments are positional, not flags: wp eval-file parses anything starting with
 * -- as a WP-CLI option and rejects it.
 *
 *   wp eval-file bin/migrate-cra-questions.php dry-run
 *   wp eval-file bin/migrate-cra-questions.php
 *   wp eval-file bin/migrate-cra-questions.php force
 *   wp eval-file bin/migrate-cra-questions.php page=590
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'cb_cra_migrate_questions' ) ) {
	echo "cb_cra_migrate_questions() not available - is the theme active?\n";
	return;
}

$argv    = isset( $args ) && is_array( $args ) ? $args : array();
$page_id = 0;

foreach ( $argv as $arg ) {
	if ( 0 === strpos( $arg, 'page=' ) ) {
		$page_id = (int) substr( $arg, 5 );
	}
}

$result = cb_cra_migrate_questions(
	array(
		'dry_run' => in_array( 'dry-run', $argv, true ),
		'force'   => in_array( 'force', $argv, true ),
		'page_id' => $page_id,
	)
);

echo implode( "\n", $result['lines'] ) . "\n";
