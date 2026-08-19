<?php
/**
 * Block template for CB People CTA.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$people = get_field( 'people' );

?>
<section class="people_cta py-5">
	<div class="container-xl">
		<?php
		if ( get_field( 'title' ) ?? null ) {
			?>
		<div class="h2 mb-4 d-md-none"><?= wp_kses_post( get_field( 'title' ) ); ?></div>
			<?php
		}
		?>
		<div class="people_cta__grid">
			<div class="people_cta__slider swiper">
				<div class="swiper-wrapper">
				<?php
				foreach ( $people as $person ) {
					?>
					<div class="swiper-slide">
						<div class="people_cta__inner">
						<?= wp_get_attachment_image( get_field( 'photo', $person ), 'medium', false, array( 'class' => 'people_cta__image' ) ); ?>
						<div class="fw-bold"><?= esc_html( get_the_title( $person ) ); ?></div>
						<?= esc_html( get_field( 'job_title', $person ) ); ?>
						</div>
					</div>
					<?php
				}
				?>
				</div>
			</div>
			<div class="people_cta__content my-auto">
				<?php
				if ( get_field( 'title' ) ?? null ) {
					?>
				<h2 class="mb-4 d-none d-md-block"><?= esc_html( get_field( 'title' ) ); ?></h2>
					<?php
				}
				if ( get_field( 'content' ) ?? null ) {
					?>
				<div class="mb-4"><?= wp_kses_post( get_field( 'content' ) ); ?></div>
					<?php
				}
				if ( get_field( 'cta' ) ?? null ) {
					$cta = get_field( 'cta' );
					?>
				<a class="btn btn--green"
				href="<?= esc_url( $cta['url'] ); ?>"
				target="<?= esc_attr( $cta['target'] ); ?>"><?= esc_html( $cta['title'] ); ?></a>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</section>
