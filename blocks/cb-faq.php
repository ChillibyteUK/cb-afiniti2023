<?php // phpcs:disable WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * FAQ Block Template.
 *
 * Multiple instances of this block on the same page are supported. Each
 * instance registers its Q&A pairs via cb_faq_add_schema_items(); a single
 * wp_footer hook outputs one FAQPage JSON-LD block covering all instances,
 * satisfying Google's one-FAQPage-per-page requirement.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

// cb_faq_add_schema_items() is defined once (function_exists guard prevents
// fatal errors on re-include). Static variables inside it persist across
// all calls for the lifetime of the request, unlike file-scope statics.
if ( ! function_exists( 'cb_faq_add_schema_items' ) ) {
	/**
	 * Collect FAQ items and output FAQPage schema.
	 *
	 * @param array $items Array of FAQ items with 'question' and 'answer' keys.
	 * @return void
	 */
	function cb_faq_add_schema_items( array $items ) {
		static $all_items = array();
		static $hooked    = false;

		foreach ( $items as $item ) {
			$all_items[] = $item;
		}

		if ( ! $hooked ) {
			$hooked = true;
			add_action(
				'wp_footer',
				function () use ( &$all_items ) {
					if ( empty( $all_items ) ) {
						return;
					}

					$entities = array_map(
						function ( $item ) {
							return array(
								'@type'          => 'Question',
								'name'           => $item['question'],
								'acceptedAnswer' => array(
									'@type' => 'Answer',
									'text'  => $item['answer'],
								),
							);
						},
						$all_items
					);

					$schema = array(
						'@context'   => 'https://schema.org',
						'@type'      => 'FAQPage',
						'mainEntity' => $entities,
					);

					echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
				}
			);
		}
	}
}

// Collect this block's Q&A pairs.
$block_faq_items = array();
while ( have_rows( 'faqs' ) ) {
	the_row();
	$block_faq_items[] = array(
		'question' => wp_strip_all_tags( get_sub_field( 'question' ) ),
		'answer'   => wp_strip_all_tags( get_sub_field( 'answer' ) ),
	);
}

cb_faq_add_schema_items( $block_faq_items );

$block_id  = $block['anchor'] ?? null;
$accordion = random_str( 5 );
?>
<section class="faq_block py-5" id="<?= esc_attr( $block_id ); ?>">
	<div class="container-xl">
		<?php if ( get_field( 'faq_title' ) ) : ?>
			<h2><?= wp_kses_post( get_field( 'faq_title' ) ); ?></h2>
		<?php endif; ?>

		<div class="faq_block__inner">
			<div id="accordion<?= esc_attr( $accordion ); ?>" class="accordion accordion-flush">
				<?php
				$counter = 0;
				while ( have_rows( 'faqs' ) ) {
					the_row();
					$ac = $accordion . '_' . $counter;
					?>
					<div class="accordion-item">
						<div class="accordion-head accordion-collapse collapsed"
							data-bs-toggle="collapse"
							id="heading_<?= esc_attr( $ac ); ?>"
							data-bs-target="#c<?= esc_attr( $ac ); ?>"
							role="button"
							aria-expanded="false"
							aria-controls="c<?= esc_attr( $ac ); ?>">
							<div class="pb-1"><?= esc_html( get_sub_field( 'question' ) ); ?></div>
						</div>
						<div class="collapse"
							id="c<?= esc_attr( $ac ); ?>"
							aria-labelledby="heading_<?= esc_attr( $ac ); ?>"
							data-bs-parent="#accordion<?= esc_attr( $accordion ); ?>">
							<div class="faq__answer">
								<?= wp_kses_post( get_sub_field( 'answer' ) ); ?>
							</div>
						</div>
					</div>
					<?php
					++$counter;
				}
				?>
			</div>
		</div>
	</div>
</section>
