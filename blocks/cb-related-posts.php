<?php
/**
 * Block template for CB Related Posts.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$selected = get_field( 'related_posts' );

if ( empty( $selected ) ) {
	return;
}

$r = new WP_Query(
	array(
		'post_type'      => get_post_types(),
		'posts_per_page' => -1,
		'post__in'       => $selected,
		'orderby'        => 'post__in',
	)
);

$classes = $block['className'] ?? null;

if ( $r->have_posts() ) {
	?>
<div class="container-xl py-4 <?= esc_attr( $classes ); ?>">
	<h2 class="mb-4">Related <span>Posts</span></h2>
	<div class="row g-4">
		<?php
		while ( $r->have_posts() ) {
			$r->the_post();
			$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
			?>
		<div class="insight col-12 col-lg-4 mb-4">
			<a href="<?= esc_url( get_the_permalink() ); ?>">
				<div class="post-image-container">
					<div class="post-image mb-2" style="background-image:url('<?= esc_url( $img ); ?>')">
						<div class="img-overlay">
							<div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
						</div>
					</div>
				</div>
				<div class="article-title mt-2"><?= esc_html( get_the_title() ); ?></div>
				<div class="article-excerpt">
					<?= wp_kses_post( wp_trim_words( get_the_content(), 20 ) ); ?>
				</div>
				<div class="fw-bold py-2 arrow-link">
					<div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
				</div>
			</a>
		</div>
			<?php
		}
		?>
	</div>
</div>
	<?php
}
