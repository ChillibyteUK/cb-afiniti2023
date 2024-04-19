<?php
$colour = strtolower(get_field('colour'));
$breakout = '';
$background = '';
if ($colour != '') {
    $background = 'bg--' . $colour;
    $parts = preg_split('/-/', $colour);
    $colourName = $parts[0];
}

$link = get_field('cta_link');

?>
<div class="container-xl pb-4">
  <div class="row">
    <div class="col-12 col-lg-8 pb-2">
      <div class="bg--<?=$colourName?>-700 p-4">
        <div class="font-large text-white">
          <h2><?=get_field('title')?></h2>
        </div>
      </div>
      <div class="bg--<?=$colour?> text-white p-4">
        <?=get_field('content')?>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="row">
        <div class="col-12">
          <div class="bg--<?=$colour?> px-5 py-4">
            <div class="fs-5 fw-bold pb-2">
              <?=get_field('right_title')?>
            </div>
            <div>
              <?=get_field('right_content')?>
              <?php
              if (get_field('image')) {
                  ?>
              <div class="text-center py-4"><img
                  src="<?=wp_get_attachment_image_url(get_field('image'), 'large')?>"
                  class="wow fadeIn"></div>
              <?php
              }
?>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-8 text-center halfcircle-container">
              <div
                class="div-rounded ss-halfcircle halfcircle-<?=$colourName?>">
                <div class="halfcircle-content halfcircle-content-10 fw-bold">
                  <a href="<?=$link['url']?>"
                    target="<?=$link['target']?>"><?=$link['title']?>
                    <span class="arrow arrow-block arrow mt-2"></span></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>