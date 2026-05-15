<?php
/**
 * Block Name: CB Val Prop
 *
 * Interactive value proposition diagram with hover popovers and Vimeo video modals.
 * Desktop: SVG lines + positioned cards with hover popover.
 * Mobile: static fallback image.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$block_id = 'vp-' . ( $block['id'] ?? uniqid() );
$classes  = $block['className'] ?? '';

// General fields.
$centre_text  = get_field( 'centre_text' );
$centre_icon  = get_field( 'centre_icon' );
$mobile_image = get_field( 'mobile_image' );

// Card data — 1: top-left (green), 2: top-right (purple), 3: bottom-right (orange), 4: bottom-left (grey).
$cards = array(
	1 => array(
		'colour'      => 'green',
		'title'       => get_field( 'card_1_title' ),
		'icon'        => get_field( 'card_1_icon' ),
		'desc'        => get_field( 'card_1_description' ),
		'popup_text'  => get_field( 'card_1_popup_text' ),
		'video_id'    => get_field( 'card_1_video_id' ),
		'video_title' => get_field( 'card_1_video_title' ),
		'link_1'      => get_field( 'card_1_link_1' ),
		'link_2'      => get_field( 'card_1_link_2' ),
	),
	2 => array(
		'colour'      => 'purple',
		'title'       => get_field( 'card_2_title' ),
		'icon'        => get_field( 'card_2_icon' ),
		'desc'        => get_field( 'card_2_description' ),
		'popup_text'  => get_field( 'card_2_popup_text' ),
		'video_id'    => get_field( 'card_2_video_id' ),
		'video_title' => get_field( 'card_2_video_title' ),
		'link_1'      => get_field( 'card_2_link_1' ),
		'link_2'      => get_field( 'card_2_link_2' ),
	),
	3 => array(
		'colour'      => 'orange',
		'title'       => get_field( 'card_3_title' ),
		'icon'        => get_field( 'card_3_icon' ),
		'desc'        => get_field( 'card_3_description' ),
		'popup_text'  => get_field( 'card_3_popup_text' ),
		'video_id'    => get_field( 'card_3_video_id' ),
		'video_title' => get_field( 'card_3_video_title' ),
		'link_1'      => get_field( 'card_3_link_1' ),
		'link_2'      => get_field( 'card_3_link_2' ),
	),
	4 => array(
		'colour'      => 'grey',
		'title'       => get_field( 'card_4_title' ),
		'icon'        => get_field( 'card_4_icon' ),
		'desc'        => get_field( 'card_4_description' ),
		'popup_text'  => get_field( 'card_4_popup_text' ),
		'video_id'    => get_field( 'card_4_video_id' ),
		'video_title' => get_field( 'card_4_video_title' ),
		'link_1'      => get_field( 'card_4_link_1' ),
		'link_2'      => get_field( 'card_4_link_2' ),
	),
);

$svg_lines  = file_get_contents( get_stylesheet_directory() . '/img/valprop/val-prop-lines.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
$svg_centre = file_get_contents( get_stylesheet_directory() . '/img/valprop/val-prop-centre.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
?>
<section class="val-prop <?= esc_attr( $classes ); ?>" id="<?= esc_attr( $block_id ); ?>">
	<div class="container-xl">
	<?php /* ── MOBILE: static fallback image ── */ ?>
	<?php if ( $mobile_image ) : ?>
	<div class="val-prop__mobile d-block d-lg-none">
		<img
			src="<?= esc_url( $mobile_image['url'] ); ?>"
			alt="<?= esc_attr( $mobile_image['alt'] ); ?>"
			class="img-fluid w-100"
		/>
	</div>
	<?php endif; ?>

	<?php /* ── DESKTOP: interactive diagram ── */ ?>
	<div class="val-prop__desktop d-none d-lg-block">

		<?php /* SVG lines layer — decorative, pointer-events: none via CSS */ ?>
		<div class="val-prop__svg" aria-hidden="true">
			<?= $svg_lines; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<?php /* ── Centre circle ── */ ?>
		<div class="val-prop__centre">
			<div class="val-prop__centre-icon" aria-hidden="true">
				<?php if ( $centre_icon ) : ?>
					<img src="<?= esc_url( $centre_icon['url'] ); ?>" alt="" />
				<?php else : ?>
					<?= $svg_centre; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</div>
			<?php if ( $centre_text ) : ?>
			<p class="val-prop__centre-text"><?= nl2br( esc_html( $centre_text ) ); ?></p>
			<?php endif; ?>
		</div>

		<?php /* ── Cards ── */ ?>
		<?php
        foreach ( $cards as $n => $card ) :
			$modal_id   = esc_attr( $block_id . '-modal-' . $n );
			$popover_id = esc_attr( $block_id . '-popover-' . $n );
			?>
		<div
			class="val-prop__card val-prop__card--<?= esc_attr( $n ); ?> val-prop__card--<?= esc_attr( $card['colour'] ); ?>"
			aria-describedby="<?= esc_attr( $popover_id ); ?>"
		>
			<?php /* Card header: icon + title */ ?>
			<div class="val-prop__card-header">
				<?php if ( $card['icon'] ) : ?>
				<div class="val-prop__card-icon">
					<img src="<?= esc_url( $card['icon']['url'] ); ?>" alt="<?= esc_attr( $card['icon']['alt'] ); ?>" />
				</div>
				<?php endif; ?>
				<?php if ( $card['title'] ) : ?>
				<h3 class="val-prop__card-title"><?= esc_html( $card['title'] ); ?></h3>
				<?php endif; ?>
			</div>

			<?php /* Card description */ ?>
			<?php if ( $card['desc'] ) : ?>
			<p class="val-prop__card-desc"><em>&ldquo;<?= nl2br( esc_html( $card['desc'] ) ); ?>&rdquo;</em></p>
			<?php endif; ?>

			<?php /* ── Hover popover ── */ ?>
			<div class="val-prop__popover  val-prop__popover--<?= esc_attr( $card['colour'] ); ?>" id="<?= esc_attr( $popover_id ); ?>" role="tooltip">
				<?php if ( $card['popup_text'] ) : ?>
				<p class="val-prop__popover-title"><?= esc_html( $card['title'] ); ?></p>
				<p class="val-prop__popover-text"><?= nl2br( esc_html( $card['popup_text'] ) ); ?></p>
				<?php endif; ?>

				<div class="val-prop__popover-actions">
					<?php if ( ! empty( $card['video_id'] ) ) : ?>
					<button
						class="val-prop__popover-video btn btn-sm mb-3"
						data-bs-toggle="modal"
						data-bs-target="#<?= esc_attr( $modal_id ); ?>"
						aria-label="<?= esc_attr( 'Watch video: ' . $card['title'] ); ?>"
					>
						<span aria-hidden="true">&#9654;</span> <?= esc_attr( $card['video_title'] ? $card['video_title'] : 'Watch Video' ); ?>
					</button>
					<?php endif; ?>

					<div class="d-flex flex-column flex-wrap gap-2">
					<?php if ( $card['link_1'] ) : ?>
					<a
						href="<?= esc_url( $card['link_1']['url'] ); ?>"
						class="val-prop__popover-link"
						<?= ! empty( $card['link_1']['target'] ) ? 'target="' . esc_attr( $card['link_1']['target'] ) . '"' : ''; ?>
					><?= esc_html( $card['link_1']['title'] ); ?></a>
					<?php endif; ?>

					<?php if ( $card['link_2'] ) : ?>
					<a
						href="<?= esc_url( $card['link_2']['url'] ); ?>"
						class="val-prop__popover-link"
						<?= ! empty( $card['link_2']['target'] ) ? 'target="' . esc_attr( $card['link_2']['target'] ) . '"' : ''; ?>
					><?= esc_html( $card['link_2']['title'] ); ?></a>
					<?php endif; ?>
					</div>
				</div>
			</div>
			<?php /* end popover */ ?>

		</div>
		<?php endforeach; ?>
		<?php /* end cards */ ?>

	</div>
	<?php /* end desktop */ ?>

	<?php /* ── Bootstrap modals (one per card that has a video ID) ── */ ?>
	<?php
    foreach ( $cards as $n => $card ) :
		if ( empty( $card['video_id'] ) ) {
			continue;
		}
		$modal_id  = esc_attr( $block_id . '-modal-' . $n );
		$embed_url = 'https://player.vimeo.com/video/' . rawurlencode( $card['video_id'] ) . '?autoplay=1&title=0&byline=0&portrait=0&badge=0';
		?>
	<div
		class="modal fade val-prop__modal"
		id="<?= esc_attr( $modal_id ); ?>"
		tabindex="-1"
		aria-label="<?= esc_attr( $card['title'] ); ?> video"
		aria-hidden="true"
		data-vp-embed="<?= esc_url( $embed_url ); ?>"
	>
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content bg-dark border-0">
				<div class="modal-header border-0 pb-0">
					<h5 class="modal-title text-white"><?= esc_html( $card['title'] ); ?></h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body pt-1">
					<div class="ratio ratio-16x9">
						<iframe
							class="val-prop__modal-iframe"
							src=""
							allow="autoplay; fullscreen; picture-in-picture"
							allowfullscreen
							title="<?= esc_attr( $card['title'] ); ?>"
						></iframe>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
	</div>
</section>
<?php /* end .val-prop */ ?>

<script>
(function () {
	var blockEl = document.getElementById('<?= esc_js( $block_id ); ?>');
	if ( ! blockEl ) { return; }

	// Load Vimeo src when modal opens (autoplay).
	blockEl.addEventListener('show.bs.modal', function (e) {
		var modal  = e.target;
		var iframe = modal.querySelector('.val-prop__modal-iframe');
		if ( iframe && modal.dataset.vpEmbed ) {
			iframe.src = modal.dataset.vpEmbed;
		}
	});

	// Clear src on close — stops video playback.
	blockEl.addEventListener('hide.bs.modal', function (e) {
		var iframe = e.target.querySelector('.val-prop__modal-iframe');
		if ( iframe ) {
			iframe.src = '';
		}
	});
})();
</script>
