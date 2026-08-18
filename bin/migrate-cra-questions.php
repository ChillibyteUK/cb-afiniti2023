<?php
/**
 * Moves the CRA questions from the tool page onto the CRA Questions options page.
 *
 * The three questions_page_1/2/3 repeaters plus the single shared
 * question_header become one cra_steps repeater in options, each step carrying
 * its own header and its own nested questions.
 *
 * The legacy `lever` subfield stored a name string ("Culture"); the new one is a
 * taxonomy field storing a term id, so this maps name -> term via
 * cb_cra_levers(). A question whose lever does not resolve is reported and
 * skipped rather than written with a broken reference.
 *
 * Idempotent: refuses to run if the options page already has steps, unless
 * `force`. Non-destructive: the page repeaters are read, never deleted, and
 * cb_cra_question_steps() falls back to them, so rollback means clearing
 * cra_steps.
 *
 * Arguments are positional, not flags - wp eval-file rejects `--`.
 *
 * Usage:
 *   wp eval-file bin/migrate-cra-questions.php dry-run
 *   wp eval-file bin/migrate-cra-questions.php
 *   wp eval-file bin/migrate-cra-questions.php force
 *   wp eval-file bin/migrate-cra-questions.php page=590
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

$page_id = $page_arg;

if ( ! $page_id ) {
	$page_id = cb_cra_tool_page_id();
}

if ( ! $page_id ) {
	echo "Could not find the CRA tool page. Pass page=<id>.\n";
	return;
}

$existing = get_field( 'cra_steps', 'options' );

if ( is_array( $existing ) && $existing && ! $force ) {
	echo 'The options page already has ' . count( $existing ) . " steps. Pass `force` to overwrite.\n";
	return;
}

echo 'Source page: ' . $page_id . ' (' . get_the_title( $page_id ) . ")\n";
echo $dry_run ? "DRY RUN - nothing will be written.\n\n" : "\n";

// Term ids by lever name, and by storage key, since the legacy field stored a name.
$term_by_name = array();

foreach ( cb_cra_levers() as $lever ) {
	if ( ! $lever['term_id'] ) {
		continue;
	}

	$term_by_name[ $lever['key'] ]   = $lever['term_id'];
	$term_by_name[ $lever['label'] ] = $lever['term_id'];
}

$header  = (string) get_field( 'question_header', $page_id );
$steps   = array();
$skipped = 0;
$total   = 0;

foreach ( array( 1, 2, 3 ) as $n ) {
	$rows = get_field( 'questions_page_' . $n, $page_id );

	if ( ! is_array( $rows ) || ! $rows ) {
		echo "questions_page_$n: empty, skipping\n";
		continue;
	}

	$questions = array();

	foreach ( $rows as $row ) {
		$name    = (string) ( $row['lever'] ?? '' );
		$term_id = $term_by_name[ $name ] ?? 0;

		if ( ! $term_id ) {
			echo "  SKIP - unresolved lever \"$name\" on: " . mb_substr( (string) ( $row['question'] ?? '' ), 0, 50 ) . "\n";
			++$skipped;
			continue;
		}

		$questions[] = array(
			'question' => (string) ( $row['question'] ?? '' ),
			'lever'    => $term_id,
		);

		++$total;
	}

	$steps[] = array(
		/*
		 * All three steps were headed "Making Change Stick" in the template and
		 * shared one question_header. Carried across as-is so nothing changes
		 * visually; they are per-step fields now, so they can diverge later.
		 */
		'step_title'  => 'Making Change Stick',
		'step_header' => $header,
		'questions'   => $questions,
	);

	echo "questions_page_$n: " . count( $questions ) . " questions\n";
}

if ( ! $steps ) {
	echo "\nNothing to migrate.\n";
	return;
}

echo "\n" . count( $steps ) . " steps, $total questions, $skipped skipped\n";

if ( $dry_run ) {
	echo "\nDry run - not written.\n";
	return;
}

update_field( 'cra_steps', $steps, 'options' );

// Read back through the normaliser, which is what the template will use.
$written = cb_cra_question_steps( $page_id );
$counts  = array();

foreach ( $written as $step ) {
	$counts[] = count( $step['questions'] );
}

echo "\nWritten. cb_cra_question_steps() now reports " . count( $written ) . ' steps (' . implode( ', ', $counts ) . " questions).\n";

$maxima = cb_cra_lever_maxima( $page_id );

echo 'Derived maxima: ' . wp_json_encode( $maxima ) . "\n";

$uneven = array_unique( array_values( $maxima ) );

if ( count( $uneven ) > 1 ) {
	echo "NOTE: levers no longer carry equal weight. That is allowed, but every\n";
	echo "      result is a percentage of its own lever maximum, so check the\n";
	echo "      charts read as intended.\n";
}

if ( in_array( 0, array_values( $maxima ), true ) ) {
	echo "WARNING: at least one lever has no questions. It will always score 0%.\n";
}
