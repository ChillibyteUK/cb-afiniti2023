<?php
/**
 * Pillar Nav Editable Block
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$classes = $block['className'] ?? null;

// Positional dashed-border classes, matching the original cb-pillar-nav layout.
$dash_borders = array(
	array( '', 'border-dash-top-left h-30px' ),
	array( 'border-dash-top', 'border-dash-top-left h-30px' ),
	array( 'border-dash-top-right h-30px', 'border-dash-top' ),
	array( 'border-dash-top-right h-30px', '' ),
);

$sections = array( 'shaping', 'readiness', 'delivering', 'embedding' );
?>
<!-- pillar_nav_editable -->
<section class="pillar_nav <?= esc_attr( $classes ); ?>">
	<div class="container-xl">
		<div class="d-none d-lg-block col-6 border-dash-right h-90px"></div>
		<div class="d-none d-lg-block col-6"></div>
		<div class="row mb-5 g-4">
			<?php
			foreach ( $sections as $i => $section ) {
				$btitle      = get_field( $section . '_title' );
				$subtitle    = get_field( $section . '_subtitle' ) ? get_field( $section . '_subtitle' ) : '&nbsp;';
				$intro       = get_field( $section . '_intro' );
				$l           = get_field( $section . '_link' );
				$bg_colour   = get_field( $section . '_bg_colour' ) ? get_field( $section . '_bg_colour' ) : 'col-white';
				$text_colour = get_field( $section . '_text_colour' ) ? get_field( $section . '_text_colour' ) : 'col-white';
				$style       = 'background-color: var(--' . esc_attr( $bg_colour ) . '); color: var(--' . esc_attr( $text_colour ) . ');';
				$has_link    = ! empty( $l['url'] );
				$link_url    = $l['url'] ?? '';
				$link_target = $l['target'] ?? '_self';
				$link_title  = $l['title'] ?? 'Find out more';
				$wrapper_tag = $has_link ? 'a' : 'div';
				?>
			<div class="col-12 col-lg-3">
				<div class="row no-mobile">
					<div class="col-6 <?= esc_attr( $dash_borders[ $i ][0] ); ?>"></div>
					<div class="col-6 <?= esc_attr( $dash_borders[ $i ][1] ); ?>"></div>
				</div>
				<<?= $wrapper_tag; ?><?php if ( $has_link ) : ?> href="<?= esc_url( $link_url ); ?>" target="<?= esc_attr( $link_target ); ?>"<?php endif; ?>>
					<div class="pillar-shadow" style="<?= esc_attr( $style ); ?>">
						<div class="px-3 py-3 border-bottom border-white">
							<span class="fs-4 fw-bold"><?= esc_html( $btitle ); ?></span><br>
							<span class="fs-5"><?= esc_html( $subtitle ); ?></span>
						</div>
						<div class="px-3 py-3">
							<div class="pillar-text">
								<?= wp_kses_post( $intro ); ?>
							</div>
							<?php if ( $has_link ) : ?>
							<div class="fw-bold pt-4 pb-2">
								<div class="anim-arrow--slide"><?= esc_html( $link_title ); ?> <span class="arrow me-3"></span></div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</<?= $wrapper_tag; ?>>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
