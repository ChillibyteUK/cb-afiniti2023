<?php
/**
 * Block template for CB Stat Spinner.
 *
 * Three count-up statistics (title / prefix / number / suffix / post-title)
 * shown side by side. Numbers animate from 0 to their target value when the
 * block scrolls into view (count-up handler keyed off the
 * `.cb-stat-spinner__stat-value` class and `data-stat-target` attribute).
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-stat-spinner-' );

$stat_color_class_attr = '';
$stat_color_style_attr = '';
if ( ! empty( $block['style']['color']['text'] ) ) {
	$raw_text_color = (string) $block['style']['color']['text'];

	if ( str_starts_with( $raw_text_color, 'var:preset|color|' ) ) {
		$raw_text_color = str_replace( 'var:preset|color|', 'var(--wp--preset--color--', $raw_text_color ) . ')';
	}

	$stat_color_style_attr = ' style="color:' . esc_attr( $raw_text_color ) . ';"';
} elseif ( ! empty( $block['textColor'] ) ) {
	$stat_color_style_attr = ' style="color:var(--wp--preset--color--' . esc_attr( (string) $block['textColor'] ) . ');"';
} elseif ( ! empty( $block['className'] ) && preg_match( '/\b(has-[a-z0-9-]+-color)\b/', (string) $block['className'], $match ) ) {
	$stat_color_class_attr = ' ' . $match[1];
}

$stats = array();

for ( $index = 1; $index <= 3; $index++ ) {
	$stats[] = array(
		'prefix' => get_field( 'prefix_' . $index ),
		'value'  => get_field( 'number_' . $index ),
		'suffix' => get_field( 'suffix_' . $index ),
		'title'  => get_field( 'title_' . $index ),
		'link'   => get_field( 'link_' . $index ),
	);
}

// Bail if nothing has been entered in any stat.
$has_content = array_reduce(
	$stats,
	function ( $carry, $stat ) {
		return $carry
			|| '' !== (string) $stat['title']
			|| '' !== (string) $stat['prefix']
			|| '' !== (string) $stat['value']
			|| '' !== (string) $stat['suffix']
			|| '' !== (string) $stat['link'];
	},
	false
);

if ( ! $has_content ) {
	return;
}

?>
<section id="<?= esc_attr( $block_id ); ?>" class="cb-stat-spinner pb-5">
	<div class="container-xl">
		<div class="row justify-content-center">
			<?php
			foreach ( $stats as $stat ) {
				?>
			<div class="col-lg-4 p-4">
				<div class="cb-stat-spinner__item text-center" style="color: var(--col-grey-500);">
					<div class="cb-stat-spinner__stat<?= esc_attr( $stat_color_class_attr ); ?>"<?= $stat_color_style_attr; ?>>
						<?php
						if ( '' !== (string) $stat['prefix'] ) {
							?>
							<span class="cb-stat-spinner__stat-prefix"><?= esc_html( $stat['prefix'] ); ?></span>
							<?php
						}
						?>
						<span class="cb-stat-spinner__stat-value" data-stat-target="<?= esc_attr( is_numeric( $stat['value'] ) ? $stat['value'] : 0 ); ?>">0</span>
						<?php
						if ( '' !== (string) $stat['suffix'] ) {
							?>
							<span class="cb-stat-spinner__stat-suffix"><?= esc_html( $stat['suffix'] ); ?></span>
							<?php
						}
						?>
					</div>
					<?php
					if ( '' !== (string) $stat['title'] ) {
						?>
						<div class="cb-stat-spinner__title"><?= esc_html( $stat['title'] ); ?></div>
						<?php
					}
					if ( is_array( $stat['link'] ) && ! empty( $stat['link']['url'] ) && ! empty( $stat['link']['title'] ) ) {
						?>
					<div class="mt-3">
						<a class="cb-stat-spinner__link" href="<?= esc_url( $stat['link']['url'] ); ?>" target="<?= esc_attr( $stat['link']['target'] ); ?>">
							<span class="anim-arrow--slide"><?= esc_html( $stat['link']['title'] ); ?> <span class="arrow arrow-green"></span></span>
						</a>
					</div>
						<?php
					}
					?>
				</div>
			</div>
				<?php
			}
			?>
		</div>
		<?php
		if ( get_field( 'after_text' ) ) {
			?>
			<div class="cb-stat-spinner__after-text text-center mt-4"><?= wp_kses_post( get_field( 'after_text' ) ); ?></div>
			<?php
		}
		?>
	</div>
</section>
<?php
add_action(
	'wp_footer',
	function () {
		?>
<script>
(function () {
	if (window.cbStatSpinnerInitialized) {
		return;
	}

	window.cbStatSpinnerInitialized = true;

	const blocks = document.querySelectorAll('.cb-stat-spinner');
	if (!blocks.length) {
		return;
	}

	const prefersReducedMotion =
		window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	const animateValue = (element) => {
		const target = Number(element.dataset.statTarget || 0);
		if (!Number.isFinite(target)) {
			return;
		}

		if (prefersReducedMotion || target === 0) {
			element.textContent = Math.round(target).toLocaleString();
			return;
		}

		const duration = 1200;
		const start = performance.now();

		const step = (time) => {
			const progress = Math.min((time - start) / duration, 1);
			const easedProgress = 1 - Math.pow(1 - progress, 3);
			element.textContent = Math.round(target * easedProgress).toLocaleString();

			if (progress < 1) {
				window.requestAnimationFrame(step);
			}
		};

		window.requestAnimationFrame(step);
	};

	const runBlock = (block) => {
		block.querySelectorAll('.cb-stat-spinner__stat-value[data-stat-target]').forEach(animateValue);
	};

	if (!('IntersectionObserver' in window)) {
		blocks.forEach(runBlock);
		return;
	}

	const observer = new IntersectionObserver(
		(entries, obs) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}

				runBlock(entry.target);
				obs.unobserve(entry.target);
			});
		},
		{ threshold: 0.35 }
	);

	blocks.forEach((block) => observer.observe(block));
})();
</script>
		<?php
	}
);