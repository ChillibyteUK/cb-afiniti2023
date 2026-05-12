<?php
/**
 * Block template for CB Hover Q&A.
 *
 * Two-column layout. Questions list on the left; clicking a question reveals
 * the corresponding answer on the right. Defaults to the first item active.
 * The first repeater row supplies the column heading titles.
 * Supports Gutenberg background and text colour classes.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$block_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-hover-q-a-' );
$classes  = trim( 'cb-hover-q-a ' . ( $block['className'] ?? '' ) );

// --- Gutenberg colour support ---
// Background colour.
$bg_style = '';
if ( ! empty( $block['style']['color']['background'] ) ) {
	$raw = (string) $block['style']['color']['background'];
	if ( str_starts_with( $raw, 'var:preset|color|' ) ) {
		$raw = 'var(--wp--preset--color--' . substr( $raw, strlen( 'var:preset|color|' ) ) . ')';
	}
	$bg_style = 'background-color:' . $raw . ';';
} elseif ( ! empty( $block['backgroundColor'] ) ) {
	$bg_style = 'background-color:var(--wp--preset--color--' . esc_attr( (string) $block['backgroundColor'] ) . ');';
}

// Text colour.
$text_style = '';
if ( ! empty( $block['style']['color']['text'] ) ) {
	$raw = (string) $block['style']['color']['text'];
	if ( str_starts_with( $raw, 'var:preset|color|' ) ) {
		$raw = 'var(--wp--preset--color--' . substr( $raw, strlen( 'var:preset|color|' ) ) . ')';
	}
	$text_style = 'color:' . $raw . ';';
} elseif ( ! empty( $block['textColor'] ) ) {
	$text_style = 'color:var(--wp--preset--color--' . esc_attr( (string) $block['textColor'] ) . ');';
}

$inline_style = $bg_style . $text_style;
$style_attr   = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

// Collect all rows from the repeater.
$rows = array();
if ( have_rows( 'q_and_a' ) ) {
	while ( have_rows( 'q_and_a' ) ) {
		the_row();
		$rows[] = array(
			'question' => get_sub_field( 'question' ),
			'answer'   => get_sub_field( 'answer' ),
		);
	}
}

// Bail if nothing entered.
if ( empty( $rows ) ) {
	return;
}

// First row = column titles.
$titles    = array_shift( $rows );
$q_title   = $titles['question'] ?? '';
$a_title   = $titles['answer']   ?? '';

// Bail if no Q&A rows remain.
if ( empty( $rows ) ) {
	return;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( $classes ); ?>"<?= $style_attr; ?>>
	<div class="container-xl">
		<div class="cb-hover-q-a__inner">

			<div class="cb-hover-q-a__questions">
				<?php if ( $q_title ) : ?>
					<div class="cb-hover-q-a__col-title"><?= esc_html( $q_title ); ?></div>
				<?php endif; ?>
				<ul class="cb-hover-q-a__question-list" role="list">
					<?php foreach ( $rows as $i => $row ) : ?>
						<li class="cb-hover-q-a__question<?= 0 === $i ? ' is-active' : ''; ?>"
							data-qa-index="<?= esc_attr( $i ); ?>"
							role="button"
							tabindex="0"
							aria-selected="<?= 0 === $i ? 'true' : 'false'; ?>">
							<?= esc_html( $row['question'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="cb-hover-q-a__answers">
				<?php if ( $a_title ) : ?>
					<div class="cb-hover-q-a__col-title"><?= esc_html( wp_strip_all_tags( $a_title ) ); ?></div>
				<?php endif; ?>
				<?php foreach ( $rows as $i => $row ) : ?>
					<div class="cb-hover-q-a__answer<?= 0 === $i ? ' is-active' : ''; ?>"
						data-qa-index="<?= esc_attr( $i ); ?>"
						aria-hidden="<?= 0 === $i ? 'false' : 'true'; ?>">
						<?= wp_kses_post( $row['answer'] ); ?>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>
<?php
add_action(
	'wp_footer',
	function () {
		static $printed = false;
		if ( $printed ) {
			return;
		}
		$printed = true;
		?>
<script>
(function () {
	document.querySelectorAll('.cb-hover-q-a').forEach(function (block) {
		var questions = block.querySelectorAll('.cb-hover-q-a__question');
		var answers   = block.querySelectorAll('.cb-hover-q-a__answer');

		function activate(index) {
			questions.forEach(function (q) {
				var active = parseInt(q.dataset.qaIndex, 10) === index;
				q.classList.toggle('is-active', active);
				q.setAttribute('aria-selected', active ? 'true' : 'false');
			});
			answers.forEach(function (a) {
				var active = parseInt(a.dataset.qaIndex, 10) === index;
				a.classList.toggle('is-active', active);
				a.setAttribute('aria-hidden', active ? 'false' : 'true');
			});
		}

		questions.forEach(function (q) {
			q.addEventListener('click', function () {
				activate(parseInt(q.dataset.qaIndex, 10));
			});
			q.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					activate(parseInt(q.dataset.qaIndex, 10));
				}
			});
		});
	});
})();
</script>
		<?php
	}
);
