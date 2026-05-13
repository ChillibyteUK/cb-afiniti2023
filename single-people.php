<?php
/**
 * The template for displaying all single people posts
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<!-- hero -->
	<section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
		<div class="hero__inner container-xl text-center">
			<div class="h1">Our experts at <span>Afiniti</span></div>
		</div>
	</section>
	<?php
	$anim = 'our-people';
	require get_stylesheet_directory() . '/page-templates/anim/' . $anim . '.php';
	?>
	<div class="container-xl">
		<div class="row g-4 mb-4">
			<div class="col-lg-4">
				<div class="single-person-photo"
					style="background-image:url(<?= esc_url( wp_get_attachment_image_url( get_field( 'photo' ), 'medium' ) ); ?>)">
				</div>
				<h1 class="fs-4 fw-bold text--green mt-4">
					<?= esc_html( strtoupper( get_the_title() ) ); ?>
				</h1>
				<div class="fs-5 text--green pb-2">
					<?= esc_html( get_field( 'job_title' ) ); ?>
				</div>
			<?php
			/*
			<div class="person-links pb-2">
				<a class="linkedin-circle"
					href="<?=get_field('linkedin_url')?>"
					target="_blank" itemprop="url"><span class="fa-stack fa-2x"><i
							class="fa fa-circle fa-stack-2x"></i><i
								class="fa-brands fa-linkedin-in fa-stack-1x fa-inverse"></i></span></a>
			</div>
			*/
			$contact_form_id    = cb_people_get_contact_form_id();
			$fields             = $contact_form_id ? cb_people_resolve_form_fields( $contact_form_id ) : null;
			$recipient_field_id = ( $fields && ! empty( $fields['recipient'] ) ) ? (int) $fields['recipient'] : 0;
			$full_name          = get_the_title();
			$name_parts         = explode( ' ', $full_name, 2 );
			$first_name         = $name_parts[0];
			if ( $contact_form_id ) {
				?>
			<div class="person-links pb-2">
				<a href="#modal-contact-person"
					class="cb-people__contact-link cb-people__contact-link--contact"
					data-bs-toggle="modal"
					data-bs-target="#modal-contact-person"
					data-person-id="<?= esc_attr( get_the_ID() ); ?>"
					data-person-firstname="<?= esc_attr( $first_name ); ?>"
					data-person-fullname="<?= esc_attr( $full_name ); ?>"
					aria-label="Contact <?= esc_attr( $first_name ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
					<span>Contact <?= esc_html( $first_name ); ?></span<
				</a>
			</div>
				<?php
			}
			?>
			</div>
			<div class="col-lg-8">
				<?php
				the_content();
				?>
			</div>
		</div>
		<div class="d-none d-lg-block col-lg-1 offset-lg-7 border-dash-bottom-right h-30px"></div>
		<div class="d-none d-lg-block col-lg-1 offset-lg-6 border-dash-top-left mt-minus-2 h-30px"></div>
		<h2 class="text-center mb-4"><span>Insights</span></h2>
		<div class="row g-4">
			<?php
			$counter = 0;

			if ( get_field( 'author' ) ?? null ) {
				$author_id = get_field( 'author' )[0];
				$args      = array(
					'post_type'      => 'post',
					'posts_per_page' => -1,
				);

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$peeps = get_field( 'person', get_the_ID() ) ?? null;
						if ( is_array( $peeps ) && isset( $peeps[0] ) ) {
							$post_author = $peeps[0]->ID;
							if ( $post_author === $author_id ) {
								++$counter;
								$img = get_the_post_thumbnail_url( $query->ID, 'large' );
								?>
			<div class="insight insight--short col-12 col-lg-4 mb-4">
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
					<div class="fw-bold py-2 arrow-link">
						<div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
					</div>
				</a>
			</div>
								<?php
							}
						}
					}
				}
			}
			if ( 0 === $counter ) {
				$i = new WP_Query(
					array(
						'post_status'    => 'publish',
						'posts_per_page' => 3,
						'orderby'        => 'rand',
					)
				);
				while ( $i->have_posts() ) {
					$i->the_post();
					$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					?>
			<div class="insight insight--short col-12 col-lg-4 mb-4">
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
					<div class="fw-bold py-2 arrow-link">
						<div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
					</div>
				</a>
			</div>
					<?php
				}
			}
			?>
		</div>
	</div>
</main>
<?php
// Render the shared contact modal once per page (guard works across shortcode
// and single template if both appear on the same request).
if ( isset( $contact_form_id ) && $contact_form_id && ! cb_people_modal_emitted() ) {
	?>
<div class="modal fade"
	id="modal-contact-person"
	tabindex="-1"
	role="dialog"
	aria-labelledby="modal-contact-person-title"
	aria-hidden="true"
	data-gf-form-id="<?= esc_attr( $contact_form_id ); ?>"
	data-gf-recipient-field="<?= esc_attr( $recipient_field_id ); ?>">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header border-0 pb-0">
				<h5 class="modal-title" id="modal-contact-person-title">Contact</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body pt-2">
				<?php
				gravity_form(
					$contact_form_id,
					/* display_title    */ false,
					/* display_desc     */ false,
					/* display_inactive */ false,
					/* field_values     */ array( 'recipient_pid' => 0 ),
					/* ajax             */ true
				);
				?>
			</div>
		</div>
	</div>
</div>
	<?php
}
get_footer();
?>