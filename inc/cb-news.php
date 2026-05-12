<?php
/**
 * CB News Functions
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

/**
 * Display single post sidebar.
 *
 * Per-post overrides are read from the `sidebar` ACF group on the current
 * post. Each sub-field falls back to the site-wide `single_post_sidebar`
 * option when not set on the post. The nubbin (halfcircle) can be replaced
 * per-post via `show_nubbin` / `nubbin_link` / `nubbin_link_title`.
 *
 * @return string
 */
function single_sidebar() {
    ob_start();

    $opt      = get_field( 'single_post_sidebar', 'options' ) ?: array();
    $post_id  = get_the_ID();
    $override = ( $post_id ? get_field( 'sidebar', $post_id ) : null ) ?: array();

    // Determine nubbin state first so we can use it in the override check.
    $show_nubbin  = ( $override['show_nubbin'] ?? [] ) === [ 'Yes' ];

    // If any meaningful field is set on the post, use the override entirely;
    // nubbin fields only count when show_nubbin is actually checked.
    $meaningful = array_filter( [
        $override['title']      ?? '',
        $override['body']       ?? '',
        $override['link']       ?? '',
        $override['link_title'] ?? '',
        $show_nubbin ? ( $override['nubbin_link']       ?? '' ) : '',
        $show_nubbin ? ( $override['nubbin_link_title'] ?? '' ) : '',
    ] );
    $has_override = ! empty( $meaningful );
    $data         = $has_override ? $override : $opt;

    $title      = $data['title']      ?? '';
    $body       = $data['body']       ?? '';
    $link       = $data['link']       ?? '';
    $link_title = $data['link_title'] ?? 'Newsletter Sign Up';
    $nubbin_link  = $show_nubbin ? ( $override['nubbin_link']       ?? '' ) : '';
    $nubbin_title = $show_nubbin ? ( $override['nubbin_link_title'] ?? '' ) : '';
    ?>
<div class="sticky-top pb-4" style="top:1rem">
	<div class="bg--green-500 px-5 py-4">
		<?php if ( $title ) { ?>
		<div class="fs-4 fw-bold pb-2"><?= esc_html( $title ); ?></div>
		<?php } ?>
		<?php if ( $body ) { ?>
		<div class="py-2"><?= wp_kses_post( $body ); ?></div>
		<?php } ?>
		<?php if ( $link ) { ?>
		<div class="text-center py-2">
			<a href="<?= esc_url( $link ); ?>" class="btn btn--white"><?= esc_html( $link_title ); ?></a>
		</div>
		<?php } ?>
	</div>
	<?php if ( $show_nubbin && $nubbin_link ) { ?>
	<div class="col-8 offset-2 text-center halfcircle-container">
		<div class="div-rounded ss-halfcircle halfcircle-green">
			<div class="halfcircle-content fw-bold">
				<a href="<?= esc_url( $nubbin_link ); ?>" target="_blank"><?= esc_html( $nubbin_title ); ?> <span class="arrow arrow-block mt-2"></span></a>
			</div>
		</div>
	</div>
	<?php } elseif ( ! $has_override ) { ?>
	<div class="col-8 offset-2 text-center halfcircle-container">
		<div class="div-rounded ss-halfcircle halfcircle-green">
			<div class="halfcircle-content fw-bold">
				<a href="<?= esc_url( get_field( 'social', 'options' )['linkedin_url'] ?? '' ); ?>" target="_blank">Follow Afiniti on LinkedIn <span class="arrow arrow-block mt-2"></span></a>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}