<?php
/**
 * Video Carousel Block Template
 *
 * This template displays a video carousel with posts filtered by the 'video' term
 * in the 'insight-type' taxonomy.
 *
 * @package cb-afiniti2023
 */

$colour     = strtolower( get_field( 'background' ) ) ?? null;
$background = 'bg--' . $colour;

$bg_size = get_field( 'bg_size' ) ?? null;

$bg_inner = '';
$bg_outer = '';

if ( 'Full Width' === $bg_size ) {
	$bg_outer = $background;
} else {
	$bg_inner = $background;
}

?>
<section class="video_carousel <?= esc_attr( $bg_outer ); ?> py-4">
    <div class="container-xl <?= esc_attr( $bg_inner ); ?> py-4">
        <?php
        if ( get_field( 'title' ) ?? null ) {
            ?>
        <h2 class="text-center"><?= esc_html( get_field( 'title' ) ); ?></h2>
            <?php
        }

		$args = array(
			'post_type' => 'post',
			'tax_query' => array(
				array(
					'taxonomy' => 'insight-type',
					'field'    => 'slug',
					'terms'    => 'video',
				),
			),
		);

		$q = new WP_Query( $args );

		if ( $q->have_posts() ) {
    		echo '<div class="video_carousel__slider">';
    		while ( $q->have_posts() ) {
        		$q->the_post();
        		$img = get_vimeo_data_from_id( get_field( 'video_id', get_the_ID() ), 'thumbnail_url' );
        		?>
        <div class="insight insight--short p-2">
            <a href="<?= esc_url( get_the_permalink() ); ?>">
                <div class="post-image-container">
                    <div class="post-image mb-2"
                        style="background-image:url('<?= esc_url( $img ); ?>')">
                        <div class="img-overlay">
                            <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                        </div>
                    </div>
                </div>
                <div class="article-title mt-2">
                    <?= esc_html( get_the_title() ); ?>
                </div>
                <!-- <div class="fw-bold py-2 arrow-link">
                    <div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
                </div> -->
            </a>
        </div>
		        <?php
    		}
    		echo '</div>';
		}

		wp_reset_postdata();
		?>
    </div>
</section>
<?php
add_action(
	'wp_footer',
	function () {
    	?>
<script src="<?= esc_url( get_stylesheet_directory_uri() ); ?>/js/slick.min.js"></script>
<script>
    jQuery(function($) {
        $('.video_carousel__slider').slick({
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            autoplay: false,
            autoplaySpeed: 4000,
            arrows: false,
            responsive: [{
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        autoplay: true
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true
                    }
                }
            ]
        });
    });
</script>
		<?php
	},
	9999
);
?>