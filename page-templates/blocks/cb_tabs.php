<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Block Name: Tabs
 *
 * This is the template that displays the tabs block.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

$classes = $block['className'] ?? null;
$colours = array(
    '#accf83' => 'green',
    '#575b8a' => 'purple',
    '#ed9025' => 'orange',
    '#474747' => 'grey',
);
?>
<!-- tabs -->
<!-- tab content  -->
<div class="container-xl responsive-tabs <?= esc_attr( $classes ); ?>">
    <ul class="row nav nav-tabs" role="tablist">
	<?php
	$active = 'active';
	$c      = 0;

	while ( have_rows( 'tabs' ) ) {
		the_row();
		?>
			<li class="col-lg-3 nav-item">
				<a id="tab-<?= esc_attr( $c ); ?>"
					href="#pane-<?= esc_attr( $c ); ?>"
					class="bg--<?= esc_attr( get_sub_field( 'background' ) ); ?> px-3 py-3 nav-link <?= esc_attr( $active ); ?>"
					data-bs-toggle="tab" role="tab">
					<span
						class="fs-4 fw-bold text-uppercase"><?= wp_kses_post( get_sub_field( 'tab_title' ) ); ?></span><br />
					<span
						class="fs-5"><?= wp_kses_post( get_sub_field( 'tab_subtitle' ) ); ?></span>
				</a>
			</li>
		<?php
		$active = '';
		++$c;
	}
	?>
    </ul>


    <div id="content" class="tab-content mb-4" role="tablist">
        <?php
    	$c      = 0;
		$active = 'active';
		$show   = 'show';
        while ( have_rows( 'tabs' ) ) {
            the_row();
            $cta_link = get_sub_field( 'cta_link' ) ? get_sub_field( 'cta_link' ) : null;
            ?>
        <div id="pane-<?= esc_attr( $c ); ?>"
            class="card tab-pane <?= esc_attr( $show ); ?> <?= esc_attr( $active ); ?>"
            role="tabpanel" aria-labelledby="tab-<?= esc_attr( $c ); ?>">
			
            <div class="card-header bg--<?= esc_attr( get_sub_field( 'background' ) ); ?>"
                role="tab" id="heading-<?= esc_attr( $c ); ?>">
                <h5 class="mb-0">
                    <a data-bs-toggle="collapse"
                        href="#collapse-<?= esc_attr( $c ); ?>" aria-expanded="true"
                        aria-controls="collapse-<?= esc_attr( $c ); ?>"><strong><?= esc_html( get_sub_field( 'tab_title' ) ); ?></strong>
                        <?= esc_html( get_sub_field( 'tab_subtitle' ) ); ?></a>
                </h5>
            </div>

            <div id="collapse-<?= esc_attr( $c ); ?>"
                class="collapse <?= esc_attr( $show ); ?>" data-bs-parent="#content"
                role="tabpanel" aria-labelledby="heading-<?= esc_attr( $c ); ?>">
                <div class="card-body">
                    <div
                        class="bg--<?= esc_attr( get_sub_field( 'background' ) ); ?> breakout-lg">
                        <div class="container py-4">
                            <div class="row">
                                <div class="col-12 col-lg-9 order-2 order-lg-1 text-white">
                                    <?php
									if ( get_sub_field( 'alt_content_title' ) ) {
										?>
                                    <h2 class="py-3">
                                        <strong><?= esc_html( get_sub_field( 'alt_content_title' ) ); ?></strong>
                                    </h2>
											<?php
									} else {
										?>
                                    <h2 class="py-3">
                                        <strong><?= esc_html( get_sub_field( 'tab_title' ) ); ?></strong>
                                    </h2>
                                    	<?php
                            		}
    								?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-8 order-2 order-lg-1 text-white div-235">
                                    <?= wp_kses_post( get_sub_field( 'content' ) ); ?>
                                </div>
                                <div class="col-12 col-lg-4 order-1 order-lg-2 text-center">
                                    <div class="image-with-circle-border mb-5">
                                        <img src="<?= esc_url( wp_get_attachment_image_url( get_sub_field( 'image' ), 'full' ) ); ?>"
                                            class="bc-img img-fluid wow animated fadeIn">
                                    </div>
                                </div>
                            </div>
                            <?php
                            if ( $cta_link ) {
                                ?>
                            <div class="only-mobile pt-4">
                                <a href="<?= esc_url( $cta_link['url'] ); ?>"
                                    class="text-white">Find
                                    out more <span class="arrow arrow-white"></span></a>
                            </div>
                                <?php
							}
                            ?>
                        </div>
                    </div>
                    <?php
                    if ( $cta_link ) {
                        $colname = get_sub_field( 'background' );
                        $parts   = preg_split( '/-/', $colname );
                        $colname = $parts[0];
                        ?>
                    <div class="container no-mobile">
                        <div class="row">
                            <div class="col-12 col-lg-4 offset-lg-4 text-center halfcircle-container">
                                <div
                                    class="div-rounded ss-halfcircle halfcircle-<?= esc_attr( $colname ); ?> text-white">
                                    <div class="halfcircle-content font-weight-bold">
                                        <a
                                            href="<?= esc_url( $cta_link['url'] ); ?>">
                                            <div class="text-white">Find out more</div><span
                                                class="arrow arrow-block arrow-white mt-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        	<?php
            ++$c;
			$active = '';
			$show   = '';
		}
		?>
    </div>
</div>