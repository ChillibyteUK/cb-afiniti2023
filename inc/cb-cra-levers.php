<?php
/**
 * The CRA levers: the single source of truth for what they are and where their
 * analysis copy lives.
 *
 * There were six separate hard-codings of this list - CB_CRA_LEVERS, $levers in
 * single-cra.php, the six named subfields of the `scores` ACF group, six
 * {slug}_analysis repeaters on the tool page, the `lever` select's choices, and
 * the `lever` taxonomy. Adding or renaming one meant edits in six places.
 *
 * How it is split now:
 *
 * - CB_CRA_LEVER_MAP below owns the canonical **order** and the **storage key**.
 *   There are exactly six levers and there will only ever be six - it is the
 *   client's trademarked methodology, not a list that grows - so a fixed map is
 *   honest, and it keeps the stored score shape stable.
 * - The `lever` taxonomy owns the **label** and the **analysis copy**. Rewording
 *   a lever, or its bands, is content work with no code change.
 *
 * The storage keys are the capitalised names every existing `cra` post already
 * uses, so no historical result data moves. Slugs happen to equal
 * strtolower( key ), which is also the prefix of the old {slug}_analysis fields,
 * so the join to legacy content is exact.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomy holding the lever labels and analysis copy.
 */
const CB_CRA_LEVER_TAXONOMY = 'lever';

/**
 * Ordered slug => storage key.
 *
 * The order drives the results grid and both charts. Do not reorder without
 * checking single-cra.php, which pairs this order with the benchmark series.
 */
const CB_CRA_LEVER_MAP = array(
	'leadership' => 'Leadership',
	'drivers'    => 'Drivers',
	'culture'    => 'Culture',
	'engagement' => 'Engagement',
	'capability' => 'Capability',
	'method'     => 'Method',
);

/**
 * The lever storage keys, in canonical order.
 *
 * This is what a submitted `scores` payload is keyed by, and what
 * cb_cra_clean_scores() validates against.
 *
 * @return string[]
 */
function cb_cra_lever_keys() {
	return array_values( CB_CRA_LEVER_MAP );
}

/**
 * The levers, in canonical order, joined to their taxonomy terms.
 *
 * Falls back to the map alone when a term is missing, so a half-populated
 * taxonomy degrades to the current hard-coded behaviour rather than rendering an
 * empty results page.
 *
 * @return array[] Each: slug, key, label, term_id (0 when no term exists).
 */
function cb_cra_levers() {
	static $levers = null;

	if ( null !== $levers ) {
		return $levers;
	}

	$terms = array();

	if ( taxonomy_exists( CB_CRA_LEVER_TAXONOMY ) ) {
		$found = get_terms(
			array(
				'taxonomy'   => CB_CRA_LEVER_TAXONOMY,
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $found ) ) {
			foreach ( $found as $term ) {
				$terms[ $term->slug ] = $term;
			}
		}
	}

	$levers = array();

	foreach ( CB_CRA_LEVER_MAP as $slug => $key ) {
		$term = $terms[ $slug ] ?? null;

		$levers[ $slug ] = array(
			'slug'    => $slug,
			'key'     => $key,
			'label'   => $term ? $term->name : $key,
			'term_id' => $term ? (int) $term->term_id : 0,
		);
	}

	return $levers;
}

/**
 * The analysis bands for one lever.
 *
 * Reads term meta first, then falls back to the legacy {slug}_analysis repeater
 * on the tool page. The fallback is what makes this shippable before the
 * migration has run, and what keeps an unmigrated environment working.
 *
 * @param string $slug         Lever slug.
 * @param int    $tool_page_id Tool page, for the legacy fallback.
 * @return array[] Rows of low_score, high_score, summary, analysis, recommendations.
 */
function cb_cra_lever_bands( $slug, $tool_page_id = 0 ) {
	$levers = cb_cra_levers();
	$lever  = $levers[ $slug ] ?? null;

	if ( $lever && $lever['term_id'] ) {
		$rows = get_field( 'lever_analysis', CB_CRA_LEVER_TAXONOMY . '_' . $lever['term_id'] );

		if ( is_array( $rows ) && $rows ) {
			return $rows;
		}
	}

	if ( $tool_page_id ) {
		$rows = get_field( $slug . '_analysis', $tool_page_id );

		if ( is_array( $rows ) && $rows ) {
			return $rows;
		}
	}

	return array();
}

/**
 * The maximum score for one answer. Fixed, not a setting.
 *
 * Every stored result was scored on this scale, and the per-lever denominator
 * depends on it, so it is not something to vary per step or per question.
 */
const CB_CRA_SCALE_MAX = 10;

/**
 * The global question steps, normalised.
 *
 * Read from the CRA Questions options page. Falls back to the legacy
 * questions_page_1/2/3 repeaters on the tool page when the options page is
 * empty, on the same principle as cb_cra_lever_bands(): an unmigrated
 * environment keeps working and rollback is a no-op.
 *
 * Questions carrying an unknown lever are dropped rather than silently scored
 * against nothing - a question nobody can score is worse than a missing one.
 *
 * @param int $tool_page_id Tool page, for the legacy fallback.
 * @return array[] Each: title, header, questions[] of ( id, text, lever_slug, lever_key ).
 */
function cb_cra_question_steps( $tool_page_id = 0 ) {
	$levers = cb_cra_levers();
	$by_id  = array();

	foreach ( $levers as $slug => $lever ) {
		if ( $lever['term_id'] ) {
			$by_id[ $lever['term_id'] ] = $slug;
		}
	}

	$steps = array();
	$rows  = get_field( 'cra_steps', 'options' );

	if ( is_array( $rows ) && $rows ) {
		foreach ( $rows as $row ) {
			$questions = array();

			foreach ( (array) ( $row['questions'] ?? array() ) as $q ) {
				$slug = $by_id[ (int) ( $q['lever'] ?? 0 ) ] ?? '';

				if ( ! $slug ) {
					continue;
				}

				$questions[] = array(
					'text'       => (string) ( $q['question'] ?? '' ),
					'lever_slug' => $slug,
					'lever_key'  => $levers[ $slug ]['key'],
				);
			}

			$steps[] = array(
				'title'     => (string) ( $row['step_title'] ?? '' ),
				'header'    => (string) ( $row['step_header'] ?? '' ),
				'questions' => $questions,
			);
		}
	} elseif ( $tool_page_id ) {
		// Legacy: three fixed repeaters sharing one question_header.
		$header = (string) get_field( 'question_header', $tool_page_id );

		$keys_by_name = array();

		foreach ( $levers as $slug => $lever ) {
			$keys_by_name[ $lever['key'] ]   = $slug;
			$keys_by_name[ $lever['label'] ] = $slug;
		}

		foreach ( array( 1, 2, 3 ) as $n ) {
			$rows = get_field( 'questions_page_' . $n, $tool_page_id );

			if ( ! is_array( $rows ) || ! $rows ) {
				continue;
			}

			$questions = array();

			foreach ( $rows as $q ) {
				$slug = $keys_by_name[ (string) ( $q['lever'] ?? '' ) ] ?? '';

				if ( ! $slug ) {
					continue;
				}

				$questions[] = array(
					'text'       => (string) ( $q['question'] ?? '' ),
					'lever_slug' => $slug,
					'lever_key'  => $levers[ $slug ]['key'],
				);
			}

			$steps[] = array(
				'title'     => 'Making Change Stick',
				'header'    => $header,
				'questions' => $questions,
			);
		}
	}

	/*
	 * Question ids are assigned here, over the flattened set, so they are stable
	 * for a given configuration and independent of which step a question sits in.
	 * cra.js and the score totals both key off them.
	 */
	$index = 0;

	foreach ( $steps as $s => $step ) {
		foreach ( $step['questions'] as $q => $question ) {
			$steps[ $s ]['questions'][ $q ]['id'] = 'q' . ( ++$index );
		}
	}

	return $steps;
}

/**
 * The maximum total score per lever, derived from the live question set.
 *
 * This is what replaces the hard-coded 30 in single-cra.php. Stored with each
 * submission so a result keeps the denominator it was scored against, rather
 * than being reinterpreted every time the question set changes.
 *
 * @param int $tool_page_id Tool page, for the legacy fallback.
 * @return array<string,int> Storage key => maximum.
 */
function cb_cra_lever_maxima( $tool_page_id = 0 ) {
	$maxima = array_fill_keys( cb_cra_lever_keys(), 0 );

	foreach ( cb_cra_question_steps( $tool_page_id ) as $step ) {
		foreach ( $step['questions'] as $question ) {
			$maxima[ $question['lever_key'] ] += CB_CRA_SCALE_MAX;
		}
	}

	return $maxima;
}

/**
 * The page holding the legacy question and analysis fields.
 *
 * `cra_tool_page_id` is no longer load-bearing. It is a hand-entered setting that
 * was empty as often as not, and every consumer that depended on it broke
 * quietly when it was - bails landed on the home page, and the results page
 * found no analysis bands. It is also meaningless once several pages run the
 * tool.
 *
 * So: the setting is honoured if present, but a page on the CRA Tool template is
 * found automatically otherwise. Nothing depends on the setting being filled in.
 * It is only needed at all for the legacy fallbacks, which go once production is
 * migrated - at which point this can go too.
 *
 * @return int Page id, or 0.
 */
function cb_cra_tool_page_id() {
	static $page_id = null;

	if ( null !== $page_id ) {
		return $page_id;
	}

	$configured = function_exists( 'get_field' ) ? (int) get_field( 'cra_tool_page_id', 'options' ) : 0;

	if ( $configured && get_post( $configured ) ) {
		$page_id = $configured;

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

	$page_id = $found ? (int) $found[0] : 0;

	return $page_id;
}

/**
 * Post meta key holding the per-lever maxima a result was scored against.
 */
const CB_CRA_MAXIMA_META = 'cb_cra_score_maxima';

/**
 * Post meta key holding the marketing opt-in, '1' or '0'.
 *
 * Duplicates the value inside the `data` ACF group. That group is a single
 * serialised value, so it cannot be filtered on; this is the queryable copy. See
 * cb_cra_opted_in_query_args().
 */
const CB_CRA_OPTIN_META = 'cb_cra_marketing_opt_in';

/**
 * Query args for the submissions that opted in to marketing.
 *
 * Saves rediscovering the meta key and the '1' convention at the point someone
 * needs to export a mailing list.
 *
 *   $ids = get_posts( cb_cra_opted_in_query_args() );
 *
 * @param bool $opted_in Pass false for the declines.
 * @return array
 */
function cb_cra_opted_in_query_args( $opted_in = true ) {
	return array(
		'post_type'      => 'cra',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => CB_CRA_OPTIN_META,
				'value' => $opted_in ? '1' : '0',
			),
		),
	);
}

/**
 * The maxima a stored result was actually scored against.
 *
 * Results created before the question set became editable have no stored maxima,
 * so they fall back to 30 - which is what they were scored on, three questions
 * per lever at 10 each. Without this, editing the question set would silently
 * reinterpret every historical result: a lever that used to be 21/30 (70%) would
 * be redrawn against the new maximum and mean something different.
 *
 * @param int $post_id A `cra` post.
 * @return array<string,int> Storage key => maximum.
 */
function cb_cra_result_maxima( $post_id ) {
	$stored = get_post_meta( $post_id, CB_CRA_MAXIMA_META, true );
	$maxima = array();

	foreach ( cb_cra_lever_keys() as $key ) {
		$value = is_array( $stored ) ? (int) ( $stored[ $key ] ?? 0 ) : 0;

		// A zero would divide by nothing, so treat it as unrecorded.
		$maxima[ $key ] = $value > 0 ? $value : CB_CRA_MAX_LEVER_SCORE;
	}

	return $maxima;
}

/**
 * The band matching a percentage, or null.
 *
 * Bands are inclusive at both ends. The first match wins, so overlapping bands
 * resolve by repeater order rather than silently picking the last one - which is
 * what single-cra.php used to do.
 *
 * @param array[] $bands   Rows from cb_cra_lever_bands().
 * @param int     $percent 0-100.
 * @return array|null
 */
function cb_cra_match_band( $bands, $percent ) {
	foreach ( $bands as $band ) {
		$low  = (int) ( $band['low_score'] ?? 0 );
		$high = (int) ( $band['high_score'] ?? 0 );

		if ( $percent >= $low && $percent <= $high ) {
			return $band;
		}
	}

	return null;
}
