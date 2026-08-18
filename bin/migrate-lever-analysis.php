<?php
/**
 * Moves the CRA analysis bands from the tool page onto the lever taxonomy.
 *
 * Six {slug}_analysis repeaters on the page using the CRA Tool template become
 * one lever_analysis repeater on each matching `lever` term. The slugs already
 * line up exactly - single-cra.php built its field name as
 * strtolower( $lever ) . '_analysis', which is the term slug - so this is a
 * straight copy with no name mapping.
 *
 * Idempotent: a term that already has bands is left alone unless --force.
 * Non-destructive: the page fields are read, never deleted. cb_cra_lever_bands()
 * falls back to them, so an environment that has not run this still works, and
 * rolling back means doing nothing.
 *
 * Arguments are positional, not flags - wp eval-file parses anything starting
 * with -- as a WP-CLI option and rejects it.
 *
 * Usage:
 *   wp eval-file bin/migrate-lever-analysis.php
 *   wp eval-file bin/migrate-lever-analysis.php dry-run
 *   wp eval-file bin/migrate-lever-analysis.php force
 *   wp eval-file bin/migrate-lever-analysis.php page=590
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'cb_cra_levers' ) ) {
	echo "cb_cra_levers() not available - is the theme active?\n";
	return;
}

$argv     = isset( $args ) && is_array( $args ) ? $args : array();
$dry_run  = in_array( 'dry-run', $argv, true );
$force    = in_array( 'force', $argv, true );
$page_arg = 0;

foreach ( $argv as $arg ) {
	if ( 0 === strpos( $arg, 'page=' ) ) {
		$page_arg = (int) substr( $arg, 5 );
	}
}

/**
 * Finds the page holding the legacy analysis fields.
 *
 * cra_tool_page_id first, then any page on the CRA Tool template - that setting
 * is empty as often as not, and the template lookup is what actually matters.
 *
 * @return int
 */
function cb_cra_find_tool_page() {
	$page_id = (int) get_field( 'cra_tool_page_id', 'options' );

	if ( $page_id && get_post( $page_id ) ) {
		return $page_id;
	}

	$found = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_wp_page_template',
					'value' => 'page-templates/cra-tool.php',
				),
			),
		)
	);

	return $found ? (int) $found[0] : 0;
}

$page_id = $page_arg ? $page_arg : cb_cra_find_tool_page();

if ( ! $page_id ) {
	echo "Could not find the CRA tool page. Pass --page=<id>.\n";
	return;
}

echo 'Source page: ' . $page_id . ' (' . get_the_title( $page_id ) . ")\n";
echo $dry_run ? "DRY RUN - nothing will be written.\n\n" : "\n";

$migrated = 0;
$skipped  = 0;
$missing  = 0;

foreach ( cb_cra_levers() as $slug => $lever ) {
	$label = str_pad( $slug, 12 );

	if ( ! $lever['term_id'] ) {
		echo $label . "SKIP - no `lever` term with this slug\n";
		++$missing;
		continue;
	}

	$source = get_field( $slug . '_analysis', $page_id );

	if ( ! is_array( $source ) || ! $source ) {
		echo $label . "SKIP - no bands on the page to copy\n";
		++$skipped;
		continue;
	}

	$term_ref  = CB_CRA_LEVER_TAXONOMY . '_' . $lever['term_id'];
	$existing  = get_field( 'lever_analysis', $term_ref );
	$has_bands = is_array( $existing ) && $existing;

	if ( $has_bands && ! $force ) {
		echo $label . 'SKIP - term already has ' . count( $existing ) . " bands (use --force to overwrite)\n";
		++$skipped;
		continue;
	}

	/*
	 * Rebuilt field by field rather than passed through wholesale: the source
	 * rows carry ACF's own row keys, and only these five subfields exist on the
	 * destination repeater.
	 */
	$rows = array();

	foreach ( $source as $row ) {
		$rows[] = array(
			'low_score'       => (int) ( $row['low_score'] ?? 0 ),
			'high_score'      => (int) ( $row['high_score'] ?? 0 ),
			'summary'         => (string) ( $row['summary'] ?? '' ),
			'analysis'        => (string) ( $row['analysis'] ?? '' ),
			'recommendations' => (string) ( $row['recommendations'] ?? '' ),
		);
	}

	$ranges = array();

	foreach ( $rows as $row ) {
		$ranges[] = $row['low_score'] . '-' . $row['high_score'];
	}

	if ( $dry_run ) {
		echo $label . 'would write ' . count( $rows ) . ' bands: ' . implode( ' ', $ranges ) . "\n";
		++$migrated;
		continue;
	}

	update_field( 'lever_analysis', $rows, $term_ref );

	$written = get_field( 'lever_analysis', $term_ref );

	if ( is_array( $written ) && count( $written ) === count( $rows ) ) {
		echo $label . 'OK - ' . count( $rows ) . ' bands: ' . implode( ' ', $ranges ) . "\n";
		++$migrated;
	} else {
		echo $label . "FAILED - wrote " . count( $rows ) . ' but read back ' . ( is_array( $written ) ? count( $written ) : 0 ) . "\n";
	}
}

echo "\nmigrated: $migrated  skipped: $skipped  missing terms: $missing\n";

/*
 * Coverage check. The bands are percentages and every score maps into 0-100, so
 * a gap means some result renders with no analysis at all - which is worth
 * knowing about now rather than when a visitor hits it.
 */
if ( ! $dry_run && $migrated ) {
	echo "\nCoverage check:\n";

	foreach ( cb_cra_levers() as $slug => $lever ) {
		$bands = cb_cra_lever_bands( $slug, $page_id );

		if ( ! $bands ) {
			continue;
		}

		$gaps = array();

		for ( $percent = 0; $percent <= 100; $percent++ ) {
			if ( null === cb_cra_match_band( $bands, $percent ) ) {
				$gaps[] = $percent;
			}
		}

		echo '  ' . str_pad( $slug, 12 ) . ( $gaps ? 'GAPS at ' . implode( ',', array_slice( $gaps, 0, 12 ) ) . ( count( $gaps ) > 12 ? '...' : '' ) : 'covers 0-100' ) . "\n";
	}
}
