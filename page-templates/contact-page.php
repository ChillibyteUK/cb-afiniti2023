<?php
/**
 * Template Name: Contact Us
 *
 * Template for displaying the Contact Us page.
 *
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

the_post();

?>
<main id="main">
<!-- hero -->
<section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
    <div class="hero__inner container-xl text-center">
        <h1><?=get_the_title()?></h1>
    </div>
</section>
<div class="container-xl pb-4">
  <div class="row">
    <div class="col-lg-8 pb-2">
      <div class="bg--green-700 p-4">
        <h2><?=get_field('intro_title')?></h2>
      </div>
      <div class="bg--green-500 p-4">
        <?=get_field('intro_body'); ?>
      </div>
      <div class="mt-4">
        <h2>Enquiry Form</h2>
        <div>Type your enquiry below, complete the relevant information and we'll get straight back to you.</div>
        <?=do_shortcode('[gravityform id="' . get_field('contact_form_id') . '" title="false" description="false" ajax="true"]')?>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="row">
        <div class="col-12">
          <div class="bg--green-500 px-5 py-4">
            <h2 class="pb-2 fs-4 fw-bold"><?=get_field('sidebar_title')?></h2>
            <div class="pb-2"><?=get_field('address_block')?></div>
          </div>
        </div>
        <div class="col-8 offset-2 text-center halfcircle-container">
          <div class="div-rounded ss-halfcircle halfcircle-green">
            <div class="halfcircle-content halfcircle-content--noborder fw-bold">
              <img class="wow animated fadeIn" src="<?=get_stylesheet_directory_uri()?>/img/illustrations/Pocket-Watch_small.png" id="contact-icon">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
</main><!-- #main -->
<div class="contact-map" id="map">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2483.129207153052!2d-0.0915708!3d51.510845499999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48760355ca6f9cd5%3A0x924fd23c9b2c878c!2s14%20Dowgate%20Hill%2C%20London%20EC4R%202SU!5e0!3m2!1sen!2suk!4v1584966775854!5m2!1sen!2suk" width="100%" height="530" frameborder="0" style="border:0;display:block" allowfullscreen="" aria-hidden="false"></iframe>
</div>

<?php get_footer(); ?>
