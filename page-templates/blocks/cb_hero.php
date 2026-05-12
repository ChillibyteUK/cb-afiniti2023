<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Template part for displaying the hero block.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$theme = strtolower( get_field( 'theme' ) );
$front = is_front_page() ? 'front' : 'mb-4';
?>
<!-- hero -->
<section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
    <div class="hero__inner container-xl text-center">
        <h1><?= wp_kses_post( get_field( 'title' ) ); ?></h1>
        <?php
        if ( get_field( 'content' ) ) {
            ?>
        <div class="hero__content fs-5 mb-4">
            <?= wp_kses_post( get_field( 'content' ) ); ?>
        </div>
        	<?php
        }
		?>
        <div class="hero__cta">
            <?php
			if ( get_field( 'cta1' ) ) {
				$cta    = get_field( 'cta1' );
				$colour = strtolower( get_field( 'cta1_colour' ) );
				$parts  = preg_split( '/-/', $colour );
				$colour = $parts[0];
				?>
            <a class="btn btn--<?= esc_attr( $colour ); ?>"
                href="<?= esc_url( $cta['url'] ); ?>"
                target="<?= esc_attr( $cta['target'] ); ?>"><?= esc_html( $cta['title'] ); ?></a>
				<?php
			}
			if ( get_field( 'cta2' ) ) {
				$cta    = get_field( 'cta2' );
				$colour = strtolower( get_field( 'cta2_colour' ) );
				$parts  = preg_split( '/-/', $colour );
				$colour = $parts[0];
				?>
            <a class="btn btn--<?= esc_attr( $colour ); ?>"
                href="<?= esc_url( $cta['url'] ); ?>"
                target="<?= esc_attr( $cta['target'] ); ?>"><?= esc_html( $cta['title'] ); ?></a>
				<?php
			}
			if ( get_field( 'cta3' ) ) {
				$cta    = get_field( 'cta3' );
				$colour = strtolower( get_field( 'cta3_colour' ) );
				$parts  = preg_split( '/-/', $colour );
				$colour = $parts[0];
				?>
            <a class="btn btn--<?= esc_attr( $colour ); ?>"
                href="<?= esc_url( $cta['url'] ); ?>"
                target="<?= esc_attr( $cta['target'] ); ?>"><?= esc_html( $cta['title'] ); ?></a>
				<?php
			}
			?>
        </div>
    </div>
</section>