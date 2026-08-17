<?php
/**
 * Block Name: CB Nav Buttons
 *
 * This is the template that displays the CB Nav Buttons block.
 *
 * A bar of buttons linking to anchors further down the page. Sticks below the
 * navbar on desktop; scrolls horizontally on mobile.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$buttons = get_field( 'buttons' );
$buttons = is_array( $buttons ) ? $buttons : array();

$links = array();

foreach ( $buttons as $button ) {
    $btitle = trim( (string) ( $button['title'] ?? '' ) );

    // The field asks for the anchor without a #, but accept one if it is typed
    // anyway. Beyond that the value is passed through untouched - it has to
    // match the target's id exactly, so it can't be slugified here.
    $anchor = ltrim( trim( (string) ( $button['anchor'] ?? '' ) ), '#' );

    if ( ! $btitle || ! $anchor ) {
        continue;
    }

    $links[] = array(
        'title'  => $btitle,
        'anchor' => $anchor,
    );
}

if ( ! $links ) {
    return;
}

?>
<nav class="nav_buttons" aria-label="<?= esc_attr__( 'Page sections', 'cb-afiniti2023' ); ?>">
    <div class="container-xl">
        <ul class="nav_buttons__list">
            <?php foreach ( $links as $l ) { ?>
            <li>
                <a class="btn btn--purple" href="#<?= esc_attr( $l['anchor'] ); ?>"><?= esc_html( $l['title'] ); ?></a>
            </li>
            <?php } ?>
        </ul>
    </div>
</nav>
