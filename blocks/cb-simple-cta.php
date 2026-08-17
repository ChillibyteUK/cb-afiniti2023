<?php
/**
 * Simple CTA Block
 *
 * @package cb
 */

defined( 'ABSPATH' ) || exit;

$btitle  = get_field( 'title' );
$content = get_field( 'content' );
$colour  = get_field( 'background' );
$cta     = get_field( 'link' );

// The link field can come back as an array, a plain URL string, or empty
// depending on the field's return format, so normalise it before use.
if ( is_array( $cta ) ) {
    $cta_url    = $cta['url'] ?? '';
    $cta_title  = $cta['title'] ?? '';
    $cta_target = $cta['target'] ?? '';
} elseif ( is_string( $cta ) ) {
    $cta_url    = $cta;
    $cta_title  = '';
    $cta_target = '';
} else {
    $cta_url    = '';
    $cta_title  = '';
    $cta_target = '';
}

if ( $cta_url && ! $cta_title ) {
    $cta_title = __( 'Find out more', 'cb' );
}

$colour     = is_string( $colour ) ? $colour : '';
$background = $colour ? 'bg--' . $colour : '';

if ( ! $btitle && ! $content && ! $cta_url ) {
    return;
}

?>
<section class="simple_cta">
    <div class="container">
        <?php if ( $btitle || $content ) { ?>
        <div class="simple_cta__content p-4 <?= esc_attr( $background ); ?>">
            <?php if ( $btitle ) { ?>
            <h2><?= wp_kses_post( $btitle ); ?></h2>
            <?php } ?>
            <?php if ( $content ) { ?>
            <p><?= wp_kses_post( $content ); ?></p>
            <?php } ?>
        </div>
        <?php } ?>
        <?php if ( $cta_url ) { ?>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-4 text-center halfcircle-container">
                <div
                    class="div-rounded ss-halfcircle halfcircle-<?= esc_attr( $colour ); ?>">
                    <div class="halfcircle-content fw-bold">
                        <a class="anim-arrow--pulse"
                            href="<?= esc_url( $cta_url ); ?>"
                            <?php
                            if ( $cta_target ) {
                                ?>
                                target="<?= esc_attr( $cta_target ); ?>" rel="noopener"
                                <?php
                            }
                            ?>
                            ><?= esc_html( $cta_title ); ?>
                            <span class="arrow mt-2"></span></a>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>
