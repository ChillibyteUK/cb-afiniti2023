<?php
/**
 * Block Name: CB Quote
 *
 * This is the template that displays one or more quotes.
 * 
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$classes = $block['className'] ?? null;
?>
<!-- quote -->
<div class="container my-5 <?= esc_attr( $classes ); ?>">
    <div class="row g-4 pb-4">
        <div class="col-lg-8 order-2 order-lg-1 my-auto">
            <div class="quote pb-3">
                <?= wp_kses_post( get_field( 'quote' ) ); ?>
            </div>
            <div class="mb-3"><span
                    class="quote__name"><?= esc_html( get_field( 'name' ) ); ?></span>,
                <span
                    class="quote_role"><?= esc_html( get_field('role' ) ); ?></span>
            </div>
        </div>
        <div class="col-lg-4 text-center order-1 order-lg-2"><img
                src="<?= esc_url( get_field( 'image' ) ); ?>"
                class="quote__image mb-4"></div>
    </div>
</div>