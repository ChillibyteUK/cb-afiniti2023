<?php
/**
 * Block template for CB 6Lever Block.
 *
 * Two columns: the 6Lever diagram and a text column. Clicking a circle in the
 * diagram shows that lever's description beside it.
 *
 * The diagram is a flat PNG, so the circles are not clickable in themselves.
 * Transparent round buttons are positioned over them using percentages measured
 * from the image itself - see $hotspots below. They form a tablist, so the
 * interaction works by keyboard and is announced properly, and each button
 * carries the lever name as its accessible label.
 *
 * On mobile the diagram is centred and every description is shown stacked
 * underneath, so nothing depends on tapping a circle. The hotspots are switched
 * off there rather than left as invisible targets that appear to do nothing.
 *
 * @package cb-afiniti
 */

defined( 'ABSPATH' ) || exit;

$classes = $block['className'] ?? '';

/*
 * Circle centres as a percentage of the image box, measured off
 * img/6Lever-Current_logo-962x1024.png by finding each coloured circle's
 * centroid. They form a regular hexagon: x at 17 / 49.3 / 83, y at 13.2 / 31.9 /
 * 68 / 85.5. Radius is ~12% of the width.
 *
 * If the diagram is ever replaced, these need re-measuring.
 */
$hotspots = array(
	'leadership' => array( 49.3, 13.2 ),
	'drivers'    => array( 83.0, 31.9 ),
	'capability' => array( 83.0, 68.0 ),
	'method'     => array( 49.3, 85.5 ),
	'engagement' => array( 17.0, 68.0 ),
	'culture'    => array( 17.0, 31.9 ),
);

/**
 * True when a wysiwyg value holds something a reader would see.
 *
 * Clearing a wysiwyg usually leaves "<p>&nbsp;</p>", and the decoded &nbsp; is
 * U+00A0 which PHP's default trim() does not strip - hence the "\xc2\xa0".
 *
 * @param string $value Raw field value.
 * @return bool
 */
$has_copy = function ( $value ) {
	return '' !== trim(
		html_entity_decode( wp_strip_all_tags( (string) $value ), ENT_QUOTES, 'UTF-8' ),
		" \t\n\r\0\x0B\xc2\xa0"
	);
};

/*
 * Labels and order come from the `lever` taxonomy via cb_cra_levers(), so a lever
 * renamed there is renamed here too. Levers with no copy are dropped entirely -
 * no hotspot and no panel - rather than offering a circle that reveals nothing.
 */
$levers = array();

foreach ( cb_cra_levers() as $slug => $lever ) {
	if ( ! isset( $hotspots[ $slug ] ) ) {
		continue;
	}

	$copy = (string) get_field( 'lever_' . $slug );

	if ( ! $has_copy( $copy ) ) {
		continue;
	}

	$levers[ $slug ] = array(
		'label' => $lever['label'],
		'copy'  => $copy,
		'x'     => $hotspots[ $slug ][0],
		'y'     => $hotspots[ $slug ][1],
	);
}

$intro     = (string) get_field( 'intro' );
$has_intro = $has_copy( $intro );
$uid       = 'sixlever-' . ( $block['id'] ?? uniqid() );
$image     = get_stylesheet_directory() . '/img/6Lever-Current_logo-962x1024.png';

if ( ! $levers || ! file_exists( $image ) ) {
	return;
}

// The first lever opens on desktop unless there is intro copy to show instead.
$first = array_key_first( $levers );
?>
<!-- sixlever -->
<section class="sixlever <?= esc_attr( $classes ); ?>">
	<div class="container-xl">
		<div class="sixlever__grid">

			<div class="sixlever__figure">
				<div class="sixlever__image" role="tablist" aria-label="<?= esc_attr__( 'The six levers', 'cb-afiniti' ); ?>">
					<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/6Lever-Current_logo-962x1024.png' ); ?>"
						alt="<?= esc_attr__( 'The Afiniti 6Lever Change Readiness Assessment model: Leadership, Drivers, Capability, Method, Engagement and Culture.', 'cb-afiniti' ); ?>"
						width="962" height="1024" loading="lazy">
					<?php foreach ( $levers as $slug => $lever ) { ?>
					<button type="button" class="sixlever__hotspot" role="tab"
						id="<?= esc_attr( $uid . '-tab-' . $slug ); ?>"
						aria-controls="<?= esc_attr( $uid . '-panel-' . $slug ); ?>"
						aria-selected="<?= ( ! $has_intro && $slug === $first ) ? 'true' : 'false'; ?>"
						tabindex="<?= ( ! $has_intro && $slug === $first ) ? '0' : '-1'; ?>"
						data-lever="<?= esc_attr( $slug ); ?>"
						style="left:<?= esc_attr( $lever['x'] ); ?>%;top:<?= esc_attr( $lever['y'] ); ?>%;">
						<span class="visually-hidden"><?= esc_html( $lever['label'] ); ?></span>
					</button>
					<?php } ?>
				</div>
			</div>

			<div class="sixlever__content">
				<?php if ( $has_intro ) { ?>
				<div class="sixlever__intro" data-sixlever-intro>
					<?= wp_kses_post( $intro ); ?>
				</div>
				<?php } ?>

				<?php foreach ( $levers as $slug => $lever ) { ?>
				<div class="sixlever__panel" role="tabpanel"
					id="<?= esc_attr( $uid . '-panel-' . $slug ); ?>"
					aria-labelledby="<?= esc_attr( $uid . '-tab-' . $slug ); ?>"
					data-lever="<?= esc_attr( $slug ); ?>"
					<?= ( ! $has_intro && $slug === $first ) ? ' data-open' : ''; ?>>
					<h3 class="sixlever__panel-title"><?= esc_html( $lever['label'] ); ?></h3>
					<?= wp_kses_post( $lever['copy'] ); ?>
				</div>
				<?php } ?>
			</div>

		</div>
	</div>
</section>
<script>
	( function () {
		var root = document.getElementById( '<?= esc_js( $uid ); ?>-root' ) || document.currentScript.previousElementSibling;

		// The <script> sits straight after the section, so walk back to it.
		while ( root && ! root.classList.contains( 'sixlever' ) ) {
			root = root.previousElementSibling;
		}

		if ( ! root ) {
			return;
		}

		var tabs = [].slice.call( root.querySelectorAll( '.sixlever__hotspot' ) );
		var panels = [].slice.call( root.querySelectorAll( '.sixlever__panel' ) );
		var intro = root.querySelector( '[data-sixlever-intro]' );

		if ( ! tabs.length ) {
			return;
		}

		function select( slug, focus ) {
			tabs.forEach( function ( t ) {
				var on = t.getAttribute( 'data-lever' ) === slug;
				t.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				t.setAttribute( 'tabindex', on ? '0' : '-1' );

				if ( on && focus ) {
					t.focus();
				}
			} );

			panels.forEach( function ( p ) {
				if ( p.getAttribute( 'data-lever' ) === slug ) {
					p.setAttribute( 'data-open', '' );
				} else {
					p.removeAttribute( 'data-open' );
				}
			} );

			// Intro copy is only the resting state, so it goes once a lever is picked.
			if ( intro ) {
				intro.hidden = true;
			}
		}

		tabs.forEach( function ( tab, i ) {
			tab.addEventListener( 'click', function () {
				select( tab.getAttribute( 'data-lever' ), false );
			} );

			// Left/right and home/end move between circles, per the tablist pattern.
			tab.addEventListener( 'keydown', function ( e ) {
				var next = null;

				if ( 'ArrowRight' === e.key || 'ArrowDown' === e.key ) {
					next = tabs[ ( i + 1 ) % tabs.length ];
				} else if ( 'ArrowLeft' === e.key || 'ArrowUp' === e.key ) {
					next = tabs[ ( i - 1 + tabs.length ) % tabs.length ];
				} else if ( 'Home' === e.key ) {
					next = tabs[ 0 ];
				} else if ( 'End' === e.key ) {
					next = tabs[ tabs.length - 1 ];
				}

				if ( next ) {
					e.preventDefault();
					select( next.getAttribute( 'data-lever' ), true );
				}
			} );
		} );
	}() );
</script>
