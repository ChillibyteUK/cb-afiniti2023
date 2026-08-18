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
