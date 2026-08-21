<?php
/**
 * Template for displaying the CRA tool results.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

/*
 * The document title used to be swapped by buffering the whole of get_header()
 * and running a regex over it. Filtering is cheaper and does not depend on the
 * markup. cb_strip_cra_seo_title() in cb-posttypes.php covers the Yoast path,
 * which is what actually renders the title while Yoast is active.
 */
add_filter( 'document_title_parts', 'cb_cra_document_title' );
function cb_cra_document_title( $parts ) {
	$parts['title'] = 'Change Readiness Assessment Results';
	unset( $parts['tagline'] );

	return $parts;
}

get_header();

$page_id = cb_cra_tool_page_id();
$data    = get_field( 'data' );
$scores  = get_field( 'scores' );

// Both come back from a JSON payload stored by cra.php, so treat them as
// untrusted shapes rather than reading keys off them blind.
$data   = is_array( $data ) ? $data : array();
$scores = is_array( $scores ) ? $scores : array();

/*
 * Resolve everything each lever needs up front, in one pass.
 *
 * The percentages and the matching score band used to be worked out twice over
 * - once for the Summary section and again for Detailed Results - which meant
 * walking all six ACF repeaters twice and duplicating the band-matching logic.
 * The chart JS then recalculated the same six percentages twice more.
 *
 * The lever list and its analysis copy now come from the `lever` taxonomy via
 * cb_cra_levers() rather than a hard-coded array plus six {slug}_analysis
 * repeaters on the tool page. $page_id is still passed through because
 * cb_cra_lever_bands() falls back to those page fields when a term has no bands,
 * which keeps an unmigrated environment rendering.
 */
$results = array();

/*
 * The denominator comes from the result itself, not from the live question set.
 * Editing the questions changes the maximum per lever, and a stored result has
 * to keep meaning what it meant when it was scored. Results saved before this
 * was recorded fall back to 30.
 */
$maxima = cb_cra_result_maxima( get_the_ID() );

foreach ( cb_cra_levers() as $slug => $lever ) {
	$key     = $lever['key'];
	$max     = max( 1, (int) ( $maxima[ $key ] ?? CB_CRA_MAX_LEVER_SCORE ) );
	$percent = round( ( ( $scores[ $key ] ?? 0 ) / $max ) * 100 );
	$band    = cb_cra_match_band( cb_cra_lever_bands( $slug, $page_id ), $percent );

	$results[ $key ] = array(
		'slug'            => $slug,
		'label'           => $lever['label'],
		'percent'         => $percent,
		'summary'         => $band['summary'] ?? '',
		'analysis'        => $band['analysis'] ?? '',
		'recommendations' => $band['recommendations'] ?? '',
	);
}

// Storage keys, in canonical order. Paired with the benchmark series below.
$levers      = array_keys( $results );
$percentages = wp_list_pluck( $results, 'percent' );

// Benchmark plotted against the user's scores on both charts.
$change_index = array( 90, 80, 70, 75, 85, 75 );

?>
<main id="main">
	<section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
		<div class="hero__inner container-xl text-center">
			<h1><span>Change Readiness</span> Assessment</h1>
			<div class="hero__cta">
				<a class="btn btn--green" href="/contact-us/">Contact us</a>
			</div>
		</div>
	</section>
	<?php
	require get_stylesheet_directory() . '/page-templates/anim/business-change.php';
	?>
	<div class="container-xl">
		<section class="contact mb-5">
			<div class="row bg--grey-700 p-4">
				<div class="col-md-4">
					<div class="row">
						<div class="col-sm-6 fw-bold">Company Name</div>
						<div class="col-sm-6">
							<?= esc_html( $data['orgName'] ?? '' ); ?>
						</div>
						<div class="col-sm-6 fw-bold">Date</div>
						<div class="col-sm-6">
							<?php // The date the assessment was taken, not today - this page is meant to be bookmarked and revisited. ?>
							<?= esc_html( get_the_date( 'd M Y' ) ); ?>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<ul class="fa-ul">
						<li><span class="fa-li"><i class="fa-solid fa-map-pin"></i></span> <a
								href="<?= esc_url( get_the_permalink() ); ?>"
								class="text-white">Bookmark this link</a> for future reference.</li>
						<li><span class="fa-li"><i class="fa-solid fa-envelope"></i></span> <a
								href="mailto:?subject=Afiniti Change Readiness Assessment&body=<?= esc_url( get_the_permalink() ); ?>"
								class="text-white">Share via email</a></li>
						<li><span class="fa-li"><i class="fa-solid fa-star"></i></span> Found your results useful? Help others by <a
								href="https://g.page/r/Cfn508DiV5pLEAI/review"
								target="_blank"
								class="text-white">leaving a review</a></li>
					</ul>
				</div>
			</div>
		</section>

		<section class="graphs mb-5">
			<h2>Graphical Results</h2>
			<div class="row">
				<div class="col-md-4">
					<canvas id="radar"></canvas>
				</div>
				<div class="col-md-8">
					<canvas id="chart"></canvas>
				</div>
			</div>
		</section>

		<section class="summary mb-5">
			<h2>Summary Assessment</h2>
			<div>
				<?php
				foreach ( $results as $result ) {
					if ( ! $result['summary'] ) {
						continue;
					}

					// Unwrapped so the six summaries read as one paragraph.
					echo str_replace( array( '<p>', '</p>' ), '', apply_filters( 'the_content', $result['summary'] ) ) . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</section>

		<?php
		/*
		* Results CTA, editable under Site-Wide Settings > CRA Questions.
		*
		* White text comes from .bg--green-500 itself, which sets `color: white
		* !important`, and headings inherit it.
		*
		* .text-white is applied to the body copy only, NOT the whole section. It
		* carries `a { color: $white !important }`, which is wanted for links inside
		* the copy but outranks .btn--white's own colour - put it on the section and
		* the button renders white text on a white background, i.e. invisible.
		*
		* The wysiwyg emptiness test strips tags, decodes entities and trims
		* "\xc2\xa0" - clearing a wysiwyg usually leaves "<p>&nbsp;</p>", and the
		* decoded &nbsp; is U+00A0, which PHP's default trim() does not strip.
		*/
		$cra_cta_title  = (string) get_field( 'cra_cta_title', 'options' );
		$cra_cta_text   = (string) get_field( 'cra_cta_text', 'options' );
		$cra_cta_button = get_field( 'cra_cta_button', 'options' );

		$cra_cta_has_text = '' !== trim(
			html_entity_decode( wp_strip_all_tags( $cra_cta_text ), ENT_QUOTES, 'UTF-8' ),
			" \t\n\r\0\x0B\xc2\xa0"
		);

		// The link field can come back as an array or a bare URL string.
		if ( is_array( $cra_cta_button ) ) {
			$cra_cta_url    = $cra_cta_button['url'] ?? '';
			$cra_cta_label  = $cra_cta_button['title'] ?? '';
			$cra_cta_target = $cra_cta_button['target'] ?? '';
		} elseif ( is_string( $cra_cta_button ) ) {
			$cra_cta_url    = $cra_cta_button;
			$cra_cta_label  = '';
			$cra_cta_target = '';
		} else {
			$cra_cta_url    = '';
			$cra_cta_label  = '';
			$cra_cta_target = '';
		}

		if ( $cra_cta_url && '' === trim( $cra_cta_label ) ) {
			$cra_cta_label = __( 'Get in touch', 'cb-afiniti' );
		}

		if ( '' !== trim( $cra_cta_title ) || $cra_cta_has_text || $cra_cta_url ) {
			?>
		<!-- CTA -->
		<section class="cra_cta bg--green-500 py-5 mb-5">
			<div class="container-xl text-center">
				<?php if ( '' !== trim( $cra_cta_title ) ) { ?>
				<h2 class="mb-3"><?= wp_kses_post( $cra_cta_title ); ?></h2>
				<?php } ?>
				<?php if ( $cra_cta_has_text ) { ?>
				<div class="cra_cta__text text-white mb-4"><?= wp_kses_post( $cra_cta_text ); ?></div>
				<?php } ?>
				<?php if ( $cra_cta_url ) { ?>
				<a class="btn btn--white" href="<?= esc_url( $cra_cta_url ); ?>"
					<?php
					if ( $cra_cta_target ) {
						?>
						target="<?= esc_attr( $cra_cta_target ); ?>" rel="noopener"
						<?php
					}
					?>
					><?= esc_html( $cra_cta_label ); ?></a>
				<?php } ?>
			</div>
		</section>
			<?php
		}
		?>

		<section class="results mb-5">
			<h2>Detailed Results</h2>
			<div class="results__grid d-none d-md-grid">
				<div class="fw-bold">Lever</div>
				<div class="fw-bold">Score</div>
				<div class="fw-bold">Analysis</div>
				<div class="fw-bold">Recommended Action</div>
			</div>

			<?php foreach ( $results as $lever => $result ) { ?>
			<div class="results__grid">
				<div class="d-flex justify-content-between">
					<div class="fw-bold"><?= esc_html( $result['label'] ); ?></div>
					<div class="d-md-none fw-normal"><?= esc_html( $result['percent'] ); ?>%
					</div>
				</div>
				<div class="d-none d-md-block"><?= esc_html( $result['percent'] ); ?>%</div>
				<?php // Always two cells, so a lever with no matching band does not collapse the four column grid. ?>
				<div>
					<?= wp_kses_post( apply_filters( 'the_content', $result['analysis'] ) ); ?>
				</div>
				<div>
					<?= wp_kses_post( apply_filters( 'the_content', cb_list( $result['recommendations'] ) ) ); ?>
				</div>
			</div>
			<?php } ?>
	</section>

	<section class="mb-5">
		<div class="container-xl">
			<p>This online version of the Afiniti 6Lever™ diagnostic tool provides a general overview of your change readiness strengths and weaknesses.</p>
			<p>Our consultants regularly conduct the full change readiness assessment across our clients' organisations. This experience allows them to deliver specific, tailored analysis and recommendations for your change projects, as well as help with implementation.</p>
			<p>Please <a href="/contact-us/">get in touch</a> if you'd like to know more.</p>
		</div>
	</section>

	<!-- latest_insights -->
	<section class="latest_news py-5">
		<div class="container">
			<h2 class="mb-4">Related <span>Insights</span></h2>
			<div class="slider mb-4">
				<?php
				// Sort a copy. $percentages itself has to stay in lever order,
				// because the charts further down pair it with $levers.
				$weakest_first = $percentages;
				asort( $weakest_first );
				$weakest = array_slice( array_keys( $weakest_first ), 0, 2 );

				/*
				 * Two posts for the weakest lever, one for the second weakest,
				 * then the most recent insights to fill up to $max_cards.
				 *
				 * This was three near identical query-and-render blocks. The
				 * posts are now collected first and rendered by a single loop,
				 * and the queries read from ->posts rather than calling
				 * the_post(), so the global post is never touched.
				 */
				$max_cards = 6;
				$cards     = array();
				$seen      = array();

				$not_team_insight = array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => 'team-insight',
					'operator' => 'NOT IN',
				);

				/*
				 * Matched on slug rather than name: the term name is now an
				 * editable label, so rewording a lever must not silently stop
				 * matching its insights.
				 */
				$lever_picks = array(
					array(
						'lever' => $weakest[0] ?? '',
						'count' => 2,
					),
					array(
						'lever' => $weakest[1] ?? '',
						'count' => 1,
					),
				);

				foreach ( $lever_picks as $pick ) {
					if ( ! $pick['lever'] || ! isset( $results[ $pick['lever'] ] ) ) {
						continue;
					}

					$lever_query = new WP_Query(
						array(
							'post_type'           => 'post',
							'posts_per_page'      => $pick['count'],
							'post_status'         => 'publish',
							'post__not_in'        => $seen,
							'no_found_rows'       => true,
							'ignore_sticky_posts' => true,
							'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
								'relation' => 'AND',
								$not_team_insight,
								array(
									'taxonomy' => CB_CRA_LEVER_TAXONOMY,
									'field'    => 'slug',
									'terms'    => array( $results[ $pick['lever'] ]['slug'] ),
								),
							),
						)
					);

					foreach ( $lever_query->posts as $lever_post ) {
						$seen[]  = $lever_post->ID;
						$cards[] = array(
							'id'   => $lever_post->ID,
							'flag' => $results[ $pick['lever'] ]['label'],
						);
					}
				}

				$remaining = $max_cards - count( $cards );

				if ( $remaining > 0 ) {
					$latest_query = new WP_Query(
						array(
							'post_type'           => 'post',
							'posts_per_page'      => $remaining,
							'post_status'         => 'publish',
							'post__not_in'        => $seen,
							'no_found_rows'       => true,
							'ignore_sticky_posts' => true,
							'tax_query'           => array( $not_team_insight ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						)
					);

					foreach ( $latest_query->posts as $latest_post ) {
						$cards[] = array(
							'id'   => $latest_post->ID,
							'flag' => '',
						);
					}
				}

				$fallback_image = get_stylesheet_directory_uri() . '/img/default-blog.jpg';

				foreach ( $cards as $card ) {
					$image = get_the_post_thumbnail_url( $card['id'], 'large' );
					$image = $image ? $image : $fallback_image;
					?>
				<div class="slider__item insight px-3">
					<a href="<?= esc_url( get_permalink( $card['id'] ) ); ?>">
						<div class="post-image-container">
							<?php if ( $card['flag'] ) { ?>
							<div class="post-image-flag"><?= esc_html( $card['flag'] ); ?></div>
							<?php } ?>
							<div class="post-image mb-2" style="background-image:url('<?= esc_url( $image ); ?>')">
								<div class="img-overlay">
									<div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
								</div>
							</div>
						</div>
						<div class="article-title mt-2">
							<?= esc_html( get_the_title( $card['id'] ) ); ?>
						</div>
						<div class="article-excerpt">
							<?= esc_html( wp_trim_words( get_the_excerpt( $card['id'] ), 20 ) ); ?>
						</div>
						<div class="fw-bold py-2 arrow-link">
							<div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
						</div>
					</a>
				</div>
					<?php
				}
				?>
			</div>
			<div class="text-center"><a href="/insights/" class="btn btn--green">Read more</a></div>
		</div>
	</section>
	<?php
	add_action('wp_footer', function () {
		?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"
		integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"
		integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="<?= esc_url( get_stylesheet_directory_uri() . '/js/slick.min.js' ); ?>"></script>
	<script>
		$('.slider').slick({
			infinite: true,
			slidesToShow: 3,
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 4000,
			dots: false,
			arrows: true,
			responsive: [{
					breakpoint: 992,
					settings: {
						arrows: false,
						slidesToShow: 2,
						slidesToScroll: 1,
					},
				},
				{
					breakpoint: 768,
					settings: {
						arrows: false,
						slidesToShow: 1,
						slidesToScroll: 1,
					},
				}
			]
		});
	</script>
		<?php
	}, 9999);

	?>
	</div>
</main>
<?php
/*
 * Pinned rather than tracking latest: an unversioned CDN URL means a Chart.js
 * major release silently breaks this page.
 *
 * Both charts plot the same two series, so the labels, the scores and the
 * benchmark are encoded once here instead of being repeated - the six
 * percentages used to be recalculated twelve times between the two configs.
 */
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
	// Editable term labels, not the storage keys the scores are keyed by.
	const craLabels = <?= wp_json_encode( array_values( wp_list_pluck( $results, 'label' ) ) ); ?>;
	const craScores = <?= wp_json_encode( array_values( $percentages ) ); ?>;
	const craIndex = <?= wp_json_encode( $change_index ); ?>;

	const craOrange = "#f07d19";
	const craGreen = "#87bd75";

	const craSeries = (fill) => [{
			label: 'Your Score',
			data: craScores,
			backgroundColor: fill ? craOrange + "66" : craOrange,
			borderWidth: 1,
			pointBackgroundColor: craOrange,
			pointBorderColor: craOrange,
			pointHoverBackgroundColor: craOrange,
			pointHoverBorderColor: craOrange
		},
		{
			label: 'Afiniti Change Index',
			data: craIndex,
			backgroundColor: fill ? craGreen + "66" : craGreen,
			borderWidth: 1,
			pointBackgroundColor: craGreen,
			pointBorderColor: craGreen,
			pointHoverBackgroundColor: craGreen,
			pointHoverBorderColor: craGreen
		}
	];

	new Chart(document.getElementById('chart'), {
		type: 'bar',
		data: {
			labels: craLabels,
			datasets: craSeries(false)
		},
		options: {
			scales: {
				y: {
					max: 100
				}
			}
		}
	});

	new Chart(document.getElementById('radar'), {
		type: 'radar',
		data: {
			labels: craLabels,
			datasets: craSeries(true)
		},
		options: {
			elements: {
				line: {
					borderWidth: 3
				}
			},
			scales: {
				r: {
					min: 0,
					max: 100
				}
			}
		}
	})
</script>
<?php

get_footer();
