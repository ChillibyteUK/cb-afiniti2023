<?php
/**
 * CRA migrations, and the admin page that runs them.
 *
 * The restructure moved two things off the CRA Tool page: the questions, to the
 * CRA Questions options page, and the analysis bands, to the `lever` taxonomy.
 * Both need a one-off migration on each environment.
 *
 * WP Engine gives no wp-cli access, so the migrations cannot be shell scripts.
 * The logic lives here as functions; `Tools > CRA Migration` runs them, and the
 * `bin/migrate-*.php` scripts are thin wrappers over the same functions for
 * environments where wp-cli does exist. One implementation, two front ends.
 *
 * Both migrations are:
 *
 * - **idempotent** - they refuse to overwrite existing data unless forced;
 * - **non-destructive** - the source fields on the page are read, never deleted,
 *   and `cb_cra_question_steps()` / `cb_cra_lever_bands()` still fall back to
 *   them, so rollback is undoing the destination rather than restoring a backup.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

/**
 * Capability required to see or run the migrations.
 */
const CB_CRA_MIGRATE_CAP = 'manage_options';

/**
 * Every page using the CRA Tool template.
 *
 * Production has several. That matters a great deal here: the migration reads
 * from **one** page but writes to **global** storage, so afterwards every one of
 * these pages renders the same questions. If they currently differ, one set wins
 * and the rest stop being used - which is the intent of making questions global,
 * but not something to discover after the fact. Hence the fingerprints below and
 * the explicit source picker on the admin page.
 *
 * @return int[] Page ids, the configured one first.
 */
function cb_cra_tool_pages() {
	$found = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_wp_page_template',
					'value' => 'page-templates/cra-tool.php',
				),
			),
		)
	);

	$pages      = array_map( 'intval', (array) $found );
	$configured = cb_cra_tool_page_id();

	if ( $configured && ! in_array( $configured, $pages, true ) ) {
		$pages[] = $configured;
	}

	// The resolver's choice first, since that is what an unpicked migration uses.
	if ( $configured ) {
		$pages = array_merge( array( $configured ), array_diff( $pages, array( $configured ) ) );
	}

	return array_values( $pages );
}

/**
 * A fingerprint of one page's legacy CRA content.
 *
 * Two pages with the same fingerprint hold the same questions and bands, so
 * choosing between them as the migration source does not matter. Different
 * fingerprints mean a real editorial decision.
 *
 * @param int $page_id Page.
 * @return array questions, bands, q_hash, band_hash, header.
 */
function cb_cra_page_fingerprint( $page_id ) {
	$questions = array();

	foreach ( array( 1, 2, 3 ) as $n ) {
		$rows = get_field( 'questions_page_' . $n, $page_id );

		foreach ( (array) $rows as $row ) {
			$questions[] = trim( (string) ( $row['question'] ?? '' ) ) . '|' . trim( (string) ( $row['lever'] ?? '' ) );
		}
	}

	$bands = array();

	foreach ( cb_cra_levers() as $slug => $lever ) {
		$rows = get_field( $slug . '_analysis', $page_id );

		foreach ( (array) $rows as $row ) {
			$bands[] = $slug . ':' . (int) ( $row['low_score'] ?? 0 ) . '-' . (int) ( $row['high_score'] ?? 0 ) . '|'
				. md5( (string) ( $row['summary'] ?? '' ) . (string) ( $row['analysis'] ?? '' ) . (string) ( $row['recommendations'] ?? '' ) );
		}
	}

	$header = trim( (string) get_field( 'question_header', $page_id ) );

	return array(
		'questions' => count( $questions ),
		'bands'     => count( $bands ),
		'q_hash'    => $questions ? substr( md5( implode( "\n", $questions ) . "\n" . $header ), 0, 8 ) : '',
		'band_hash' => $bands ? substr( md5( implode( "\n", $bands ) ), 0, 8 ) : '',
		'header'    => '' !== $header,
	);
}

/**
 * Copies the {slug}_analysis repeaters from the tool page onto the lever terms.
 *
 * The slugs already line up: single-cra.php built its field name as
 * strtolower( $lever ) . '_analysis', which is the term slug, so this is a
 * straight copy with no name mapping.
 *
 * @param array $opts dry_run, force, page_id.
 * @return array lines, migrated, skipped, missing, ok.
 */
function cb_cra_migrate_analysis( $opts = array() ) {
	$dry_run = ! empty( $opts['dry_run'] );
	$force   = ! empty( $opts['force'] );
	$page_id = (int) ( $opts['page_id'] ?? 0 );

	if ( ! $page_id ) {
		$page_id = cb_cra_tool_page_id();
	}

	$out = array(
		'lines'    => array(),
		'migrated' => 0,
		'skipped'  => 0,
		'missing'  => 0,
		'ok'       => false,
	);

	if ( ! $page_id ) {
		$out['lines'][] = 'Could not find the CRA Tool page. Assign the CRA Tool template to a page, or set the CRA Tool Page ID.';

		return $out;
	}

	$out['lines'][] = 'Source page: ' . $page_id . ' (' . get_the_title( $page_id ) . ')';

	if ( $dry_run ) {
		$out['lines'][] = 'DRY RUN - nothing will be written.';
	}

	$out['lines'][] = '';

	foreach ( cb_cra_levers() as $slug => $lever ) {
		$label = str_pad( $slug, 12 );

		if ( ! $lever['term_id'] ) {
			$out['lines'][] = $label . 'SKIP - no `lever` term with this slug';
			++$out['missing'];
			continue;
		}

		$source = get_field( $slug . '_analysis', $page_id );

		if ( ! is_array( $source ) || ! $source ) {
			$out['lines'][] = $label . 'SKIP - no bands on the page to copy';
			++$out['skipped'];
			continue;
		}

		$term_ref = CB_CRA_LEVER_TAXONOMY . '_' . $lever['term_id'];
		$existing = get_field( 'lever_analysis', $term_ref );

		if ( is_array( $existing ) && $existing && ! $force ) {
			$out['lines'][] = $label . 'SKIP - term already has ' . count( $existing ) . ' bands (tick Overwrite to replace)';
			++$out['skipped'];
			continue;
		}

		/*
		 * Rebuilt field by field rather than passed through wholesale: the source
		 * rows carry ACF's own row keys, and only these five subfields exist on
		 * the destination repeater.
		 */
		$rows   = array();
		$ranges = array();

		foreach ( $source as $row ) {
			$rows[]   = array(
				'low_score'       => (int) ( $row['low_score'] ?? 0 ),
				'high_score'      => (int) ( $row['high_score'] ?? 0 ),
				'summary'         => (string) ( $row['summary'] ?? '' ),
				'analysis'        => (string) ( $row['analysis'] ?? '' ),
				'recommendations' => (string) ( $row['recommendations'] ?? '' ),
			);
			$ranges[] = (int) ( $row['low_score'] ?? 0 ) . '-' . (int) ( $row['high_score'] ?? 0 );
		}

		if ( $dry_run ) {
			$out['lines'][] = $label . 'would write ' . count( $rows ) . ' bands: ' . implode( ' ', $ranges );
			++$out['migrated'];
			continue;
		}

		update_field( 'lever_analysis', $rows, $term_ref );

		$written = get_field( 'lever_analysis', $term_ref );

		if ( is_array( $written ) && count( $written ) === count( $rows ) ) {
			$out['lines'][] = $label . 'OK - ' . count( $rows ) . ' bands: ' . implode( ' ', $ranges );
			++$out['migrated'];
		} else {
			$out['lines'][] = $label . 'FAILED - wrote ' . count( $rows ) . ' but read back ' . ( is_array( $written ) ? count( $written ) : 0 );
		}
	}

	$out['lines'][] = '';
	$out['lines'][] = sprintf( 'migrated: %d  skipped: %d  missing terms: %d', $out['migrated'], $out['skipped'], $out['missing'] );

	/*
	 * Coverage check. Bands are percentages and every score maps into 0-100, so a
	 * gap means some result renders with no analysis at all - worth knowing now
	 * rather than when a visitor hits it.
	 */
	if ( ! $dry_run && $out['migrated'] ) {
		$out['lines'][] = '';
		$out['lines'][] = 'Coverage check:';

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

			$out['lines'][] = '  ' . str_pad( $slug, 12 ) . ( $gaps
				? 'GAPS at ' . implode( ',', array_slice( $gaps, 0, 12 ) ) . ( count( $gaps ) > 12 ? '...' : '' )
				: 'covers 0-100' );
		}
	}

	$out['ok'] = true;

	return $out;
}

/**
 * Copies questions_page_1/2/3 onto the CRA Questions options page.
 *
 * The legacy `lever` subfield stored a name string ("Culture"); the new one is a
 * taxonomy field storing a term id. A question whose lever does not resolve is
 * reported and skipped rather than written with a broken reference.
 *
 * @param array $opts dry_run, force, page_id.
 * @return array lines, steps, questions, skipped, ok.
 */
function cb_cra_migrate_questions( $opts = array() ) {
	$dry_run = ! empty( $opts['dry_run'] );
	$force   = ! empty( $opts['force'] );
	$page_id = (int) ( $opts['page_id'] ?? 0 );

	if ( ! $page_id ) {
		$page_id = cb_cra_tool_page_id();
	}

	$out = array(
		'lines'     => array(),
		'steps'     => 0,
		'questions' => 0,
		'skipped'   => 0,
		'ok'        => false,
	);

	if ( ! $page_id ) {
		$out['lines'][] = 'Could not find the CRA Tool page. Assign the CRA Tool template to a page, or set the CRA Tool Page ID.';

		return $out;
	}

	$existing = get_field( 'cra_steps', 'options' );

	if ( is_array( $existing ) && $existing && ! $force ) {
		$out['lines'][] = 'The CRA Questions page already has ' . count( $existing ) . ' steps. Tick Overwrite to replace them.';

		return $out;
	}

	$out['lines'][] = 'Source page: ' . $page_id . ' (' . get_the_title( $page_id ) . ')';

	if ( $dry_run ) {
		$out['lines'][] = 'DRY RUN - nothing will be written.';
	}

	$out['lines'][] = '';

	// Term ids by lever name and by storage key, since the legacy field held a name.
	$term_by_name = array();

	foreach ( cb_cra_levers() as $lever ) {
		if ( ! $lever['term_id'] ) {
			continue;
		}

		$term_by_name[ $lever['key'] ]   = $lever['term_id'];
		$term_by_name[ $lever['label'] ] = $lever['term_id'];
	}

	$header = (string) get_field( 'question_header', $page_id );
	$steps  = array();

	foreach ( array( 1, 2, 3 ) as $n ) {
		$rows = get_field( 'questions_page_' . $n, $page_id );

		if ( ! is_array( $rows ) || ! $rows ) {
			$out['lines'][] = "questions_page_$n: empty, skipping";
			continue;
		}

		$questions = array();

		foreach ( $rows as $row ) {
			$name    = (string) ( $row['lever'] ?? '' );
			$term_id = $term_by_name[ $name ] ?? 0;

			if ( ! $term_id ) {
				$out['lines'][] = '  SKIP - unresolved lever "' . $name . '" on: ' . mb_substr( (string) ( $row['question'] ?? '' ), 0, 50 );
				++$out['skipped'];
				continue;
			}

			$questions[] = array(
				'question' => (string) ( $row['question'] ?? '' ),
				'lever'    => $term_id,
			);

			++$out['questions'];
		}

		$steps[] = array(
			/*
			 * All three steps were headed "Making Change Stick" in the template
			 * and shared one question_header. Carried across as-is so nothing
			 * changes visually; they are per-step fields now, so they can diverge.
			 */
			'step_title'  => 'Making Change Stick',
			'step_header' => $header,
			'questions'   => $questions,
		);

		$out['lines'][] = "questions_page_$n: " . count( $questions ) . ' questions';
	}

	if ( ! $steps ) {
		$out['lines'][] = '';
		$out['lines'][] = 'Nothing to migrate - no questions found on the page.';

		return $out;
	}

	$out['steps']    = count( $steps );
	$out['lines'][]  = '';
	$out['lines'][]  = sprintf( '%d steps, %d questions, %d skipped', $out['steps'], $out['questions'], $out['skipped'] );

	if ( $dry_run ) {
		$out['ok'] = true;

		return $out;
	}

	update_field( 'cra_steps', $steps, 'options' );

	// Read back through the normaliser, which is what the template uses.
	$written = cb_cra_question_steps( $page_id );
	$counts  = array();

	foreach ( $written as $step ) {
		$counts[] = count( $step['questions'] );
	}

	$out['lines'][] = '';
	$out['lines'][] = 'Written. The tool now reports ' . count( $written ) . ' steps (' . implode( ', ', $counts ) . ' questions).';

	$maxima         = cb_cra_lever_maxima( $page_id );
	$out['lines'][] = 'Derived maxima: ' . wp_json_encode( $maxima );

	if ( count( array_unique( array_values( $maxima ) ) ) > 1 ) {
		$out['lines'][] = 'NOTE: levers no longer carry equal weight. That is allowed - every result is a';
		$out['lines'][] = '      percentage of its own lever maximum - but check the charts read as intended.';
	}

	if ( in_array( 0, array_values( $maxima ), true ) ) {
		$out['lines'][] = 'WARNING: at least one lever has no questions. It will always score 0%.';
	}

	$out['ok'] = true;

	return $out;
}

/**
 * A snapshot of what has and has not been migrated on this environment.
 *
 * @return array
 */
function cb_cra_migration_status() {
	$page_id = cb_cra_tool_page_id();

	$status = array(
		'page_id'         => $page_id,
		'page_title'      => $page_id ? get_the_title( $page_id ) : '',
		'legacy_rows'     => 0,
		'legacy_header'   => false,
		'options_steps'   => 0,
		'options_qs'      => 0,
		'levers'          => array(),
		'maxima'          => array(),
		'terms_missing'   => 0,
		'questions_ready' => false,
		'analysis_ready'  => false,
	);

	if ( $page_id ) {
		foreach ( array( 1, 2, 3 ) as $n ) {
			$rows = get_field( 'questions_page_' . $n, $page_id );

			if ( is_array( $rows ) ) {
				$status['legacy_rows'] += count( $rows );
			}
		}

		$status['legacy_header'] = '' !== trim( (string) get_field( 'question_header', $page_id ) );
	}

	$steps = get_field( 'cra_steps', 'options' );

	if ( is_array( $steps ) ) {
		$status['options_steps'] = count( $steps );

		foreach ( $steps as $step ) {
			$status['options_qs'] += is_array( $step['questions'] ?? null ) ? count( $step['questions'] ) : 0;
		}
	}

	foreach ( cb_cra_levers() as $slug => $lever ) {
		$term_bands = array();
		$page_bands = array();

		if ( $lever['term_id'] ) {
			$rows       = get_field( 'lever_analysis', CB_CRA_LEVER_TAXONOMY . '_' . $lever['term_id'] );
			$term_bands = is_array( $rows ) ? $rows : array();
		} else {
			++$status['terms_missing'];
		}

		if ( $page_id ) {
			$rows       = get_field( $slug . '_analysis', $page_id );
			$page_bands = is_array( $rows ) ? $rows : array();
		}

		$gaps = 0;

		if ( $term_bands ) {
			for ( $percent = 0; $percent <= 100; $percent++ ) {
				if ( null === cb_cra_match_band( $term_bands, $percent ) ) {
					++$gaps;
				}
			}
		}

		$status['levers'][ $slug ] = array(
			'label'      => $lever['label'],
			'term_id'    => $lever['term_id'],
			'term_bands' => count( $term_bands ),
			'page_bands' => count( $page_bands ),
			'gaps'       => $gaps,
		);
	}

	$status['maxima']          = cb_cra_lever_maxima( $page_id );
	$status['questions_ready'] = $status['options_steps'] > 0;

	$ready = 0;

	foreach ( $status['levers'] as $lever ) {
		if ( $lever['term_bands'] > 0 ) {
			++$ready;
		}
	}

	$status['analysis_ready'] = $ready === count( $status['levers'] ) && $ready > 0;

	return $status;
}

add_action( 'admin_menu', 'cb_cra_migrate_menu' );

/**
 * Registers Tools > CRA Migration.
 *
 * @return void
 */
function cb_cra_migrate_menu() {
	add_management_page(
		'CRA Migration',
		'CRA Migration',
		CB_CRA_MIGRATE_CAP,
		'cb-cra-migrate',
		'cb_cra_migrate_page'
	);
}

/**
 * Renders the migration page, and runs a migration when one is submitted.
 *
 * @return void
 */
function cb_cra_migrate_page() {
	if ( ! current_user_can( CB_CRA_MIGRATE_CAP ) ) {
		wp_die( esc_html__( 'You do not have permission to run migrations.', 'cb-afiniti' ) );
	}

	$result = null;
	$ran    = '';

	if ( isset( $_POST['cb_cra_migrate_action'] ) ) {
		check_admin_referer( 'cb_cra_migrate' );

		$action = sanitize_key( wp_unslash( $_POST['cb_cra_migrate_action'] ) );
		$opts   = array(
			'dry_run' => ! empty( $_POST['cb_cra_dry_run'] ),
			'force'   => ! empty( $_POST['cb_cra_force'] ),
			'page_id' => isset( $_POST['cb_cra_source_page'] ) ? (int) $_POST['cb_cra_source_page'] : 0,
		);

		// Only ever a page that actually runs the template.
		if ( $opts['page_id'] && ! in_array( $opts['page_id'], cb_cra_tool_pages(), true ) ) {
			$opts['page_id'] = 0;
		}

		if ( 'questions' === $action ) {
			$result = cb_cra_migrate_questions( $opts );
			$ran    = 'Questions';
		} elseif ( 'analysis' === $action ) {
			$result = cb_cra_migrate_analysis( $opts );
			$ran    = 'Analysis bands';
		}
	}

	$status = cb_cra_migration_status();
	?>
	<div class="wrap">
		<h1>CRA Migration</h1>

		<p>
			The CRA questions moved to <strong>CRA Questions</strong> and the analysis bands to the
			<strong>Lever</strong> taxonomy. Each environment needs these run once.
			Both are safe to re-run: they refuse to overwrite existing data unless
			<em>Overwrite</em> is ticked, and neither deletes anything from the CRA Tool page.
		</p>

		<?php if ( $result ) { ?>
		<h2><?= esc_html( $ran ); ?> migration</h2>
		<div class="notice <?= $result['ok'] ? 'notice-success' : 'notice-warning'; ?>">
			<p><strong><?= $result['ok'] ? 'Finished.' : 'Did not run.'; ?></strong></p>
		</div>
		<pre style="background:#fff;border:1px solid #c3c4c7;padding:1rem;overflow:auto;max-height:32rem;"><?= esc_html( implode( "\n", $result['lines'] ) ); ?></pre>
		<?php } ?>

		<?php
		/*
		 * Several pages run this template in production. The migration reads one
		 * and writes global storage, so afterwards they all show the same
		 * questions. Whether that is a no-op or an editorial change depends
		 * entirely on whether their content already matches - so show it.
		 */
		$tool_pages   = cb_cra_tool_pages();
		$fingerprints = array();

		foreach ( $tool_pages as $tool_page ) {
			$fingerprints[ $tool_page ] = cb_cra_page_fingerprint( $tool_page );
		}

		$q_hashes    = array_filter( wp_list_pluck( $fingerprints, 'q_hash' ) );
		$band_hashes = array_filter( wp_list_pluck( $fingerprints, 'band_hash' ) );
		$q_diverge   = count( array_unique( $q_hashes ) ) > 1;
		$b_diverge   = count( array_unique( $band_hashes ) ) > 1;
		?>

		<h2>Pages using the CRA Tool template</h2>

		<?php if ( count( $tool_pages ) > 1 ) { ?>
		<div class="notice <?= ( $q_diverge || $b_diverge ) ? 'notice-error' : 'notice-info'; ?>" style="padding:0.75rem;">
			<p>
				<strong><?= count( $tool_pages ); ?> pages run this template.</strong>
				The migration reads from <em>one</em> of them and writes to global storage, so afterwards
				<strong>all of them show the same questions</strong>.
			</p>
			<?php if ( $q_diverge || $b_diverge ) { ?>
			<p style="color:#b32d2e;">
				<strong>Their content differs</strong>
				<?php
				$which = array();

				if ( $q_diverge ) {
					$which[] = 'questions';
				}

				if ( $b_diverge ) {
					$which[] = 'analysis bands';
				}

				echo ' (' . esc_html( implode( ' and ', $which ) ) . ').';
				?>
				Pick the page whose content should become the global set. The others keep their
				fields in the database but stop being used, so nothing is lost and this is reversible
				&mdash; but it is an editorial decision, not a technical one. Compare them before running.
			</p>
			<?php } else { ?>
			<p>Their content is identical, so it does not matter which one is used as the source.</p>
			<?php } ?>
		</div>
		<?php } ?>

		<table class="widefat striped" style="max-width:60rem;">
			<thead>
				<tr>
					<th>Page</th>
					<th>Questions</th>
					<th>Header</th>
					<th>Bands</th>
					<th>Fingerprint</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( ! $tool_pages ) {
					?>
				<tr><td colspan="5"><strong>No page uses the CRA Tool template.</strong> Assign it under Page &gt; Template.</td></tr>
					<?php
				}

				foreach ( $tool_pages as $tool_page ) {
					$fp = $fingerprints[ $tool_page ];
					?>
				<tr>
					<td>
						<a href="<?= esc_url( get_edit_post_link( $tool_page ) ); ?>"><?= esc_html( get_the_title( $tool_page ) ); ?></a>
						(ID <?= (int) $tool_page; ?>)
						<?php if ( (int) $tool_page === (int) $status['page_id'] ) { ?>
						<em>&mdash; default source</em>
						<?php } ?>
					</td>
					<td><?= (int) $fp['questions']; ?></td>
					<td><?= $fp['header'] ? 'yes' : '&mdash;'; ?></td>
					<td><?= (int) $fp['bands']; ?></td>
					<td>
						<code><?= esc_html( $fp['q_hash'] ? $fp['q_hash'] : '—' ); ?></code>
						/
						<code><?= esc_html( $fp['band_hash'] ? $fp['band_hash'] : '—' ); ?></code>
					</td>
				</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<p class="description">
			Fingerprint is questions/bands. Matching fingerprints mean matching content.
		</p>

		<h2>Current state</h2>
		<table class="widefat striped" style="max-width:60rem;">
			<tbody>
				<tr>
					<th style="width:16rem;">Default source page</th>
					<td>
						<?php if ( $status['page_id'] ) { ?>
						<?= esc_html( $status['page_title'] ); ?> (ID <?= (int) $status['page_id']; ?>)
						<?php } else { ?>
						<strong>Not found.</strong> Assign the CRA Tool template to a page.
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th>Legacy questions on that page</th>
					<td><?= (int) $status['legacy_rows']; ?> rows<?= $status['legacy_header'] ? ', plus a shared header' : ''; ?></td>
				</tr>
				<tr>
					<th>CRA Questions page</th>
					<td>
						<?= (int) $status['options_steps']; ?> steps, <?= (int) $status['options_qs']; ?> questions
						<?php if ( $status['questions_ready'] ) { ?>
						&mdash; <strong style="color:#008a20;">migrated</strong>
						<?php } else { ?>
						&mdash; <strong style="color:#b32d2e;">not migrated</strong>, the tool is falling back to the page fields
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th>Lever terms</th>
					<td>
						<?php if ( $status['terms_missing'] ) { ?>
						<strong style="color:#b32d2e;"><?= (int) $status['terms_missing']; ?> missing.</strong>
						Every lever needs a term in the Lever taxonomy, slugged
						<code>leadership</code>, <code>drivers</code>, <code>culture</code>,
						<code>engagement</code>, <code>capability</code>, <code>method</code>.
						<?php } else { ?>
						all six present
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>

		<h3>Analysis bands per lever</h3>
		<table class="widefat striped" style="max-width:60rem;">
			<thead>
				<tr>
					<th>Lever</th>
					<th>On the term</th>
					<th>On the page (legacy)</th>
					<th>Coverage 0-100</th>
					<th>Max score</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $status['levers'] as $slug => $lever ) { ?>
				<tr>
					<td><?= esc_html( $lever['label'] ); ?> <code><?= esc_html( $slug ); ?></code></td>
					<td>
						<?php if ( $lever['term_bands'] ) { ?>
						<strong style="color:#008a20;"><?= (int) $lever['term_bands']; ?></strong>
						<?php } else { ?>
						<strong style="color:#b32d2e;">0</strong>
						<?php } ?>
					</td>
					<td><?= (int) $lever['page_bands']; ?></td>
					<td>
						<?php if ( ! $lever['term_bands'] ) { ?>
						&mdash;
						<?php } elseif ( $lever['gaps'] ) { ?>
						<strong style="color:#b32d2e;"><?= (int) $lever['gaps']; ?> uncovered percentages</strong>
						<?php } else { ?>
						complete
						<?php } ?>
					</td>
					<td><?= (int) ( $status['maxima'][ $lever['label'] ] ?? 0 ); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<h2>Run a migration</h2>
		<form method="post">
			<?php wp_nonce_field( 'cb_cra_migrate' ); ?>
			<?php if ( count( $tool_pages ) > 1 ) { ?>
			<p>
				<label for="cb_cra_source_page"><strong>Source page</strong></label><br>
				<select name="cb_cra_source_page" id="cb_cra_source_page">
					<?php foreach ( $tool_pages as $tool_page ) { ?>
					<option value="<?= (int) $tool_page; ?>" <?php selected( (int) $tool_page, (int) $status['page_id'] ); ?>>
						<?= esc_html( get_the_title( $tool_page ) ); ?>
						(<?= (int) $fingerprints[ $tool_page ]['questions']; ?> questions,
						<?= (int) $fingerprints[ $tool_page ]['bands']; ?> bands)
					</option>
					<?php } ?>
				</select>
				<span class="description">Whose content becomes the global set.</span>
			</p>
			<?php } elseif ( $tool_pages ) { ?>
			<input type="hidden" name="cb_cra_source_page" value="<?= (int) $tool_pages[0]; ?>">
			<?php } ?>
			<p>
				<label>
					<input type="checkbox" name="cb_cra_dry_run" value="1" checked>
					<strong>Dry run</strong> &mdash; report what would happen, write nothing. Untick to actually migrate.
				</label>
			</p>
			<p>
				<label>
					<input type="checkbox" name="cb_cra_force" value="1">
					<strong>Overwrite</strong> &mdash; replace data that has already been migrated. Leave unticked normally.
				</label>
			</p>
			<p>
				<button type="submit" name="cb_cra_migrate_action" value="analysis" class="button button-primary">
					1. Migrate analysis bands
				</button>
				<button type="submit" name="cb_cra_migrate_action" value="questions" class="button button-primary">
					2. Migrate questions
				</button>
			</p>
			<p class="description">
				Run them in that order, dry run first. Afterwards, check the tool page still works
				and purge the page cache &mdash; submissions need a token that only exists in freshly
				rendered HTML.
			</p>
		</form>
	</div>
	<?php
}
