<?php

function single_sidebar() {
    ob_start();

    $git = get_field('single_post_sidebar', 'options');
    ?>
<div class="sticky-top pb-4" style="top:1rem">
<div class="bg--green-500 px-5 py-4">
  <div class="fs-4 fw-bold pb-2"><?=$git['title']?></div>
  <div class="py-2"><?=$git['body']?></div>
  <div class="social-icons social-icons__sidebar  text-center pt-2 pb-4">
    <?php
        if (in_array('Facebook', $git['social'])) {
            echo '<a href="' . get_field('facebook_url', 'options') . '" target="_blank" itemprop="url"><span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i class="fa-brands fa-facebook-f fa-stack-1x fa-inverse"></i></span></a>';
        }
        if (in_array('LinkedIn', $git['social'])) {
            echo '<a href="' . get_field('linkedin_url', 'options') . '" target="_blank" itemprop="url"><span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i class="fa-brands fa-linkedin-in fa-stack-1x fa-inverse"></i></span></a>';
        }
        if (in_array('Twitter', $git['social'])) {
            echo '<a href="' . get_field('twitter_url', 'options') . '" target="_blank" itemprop="url"><span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i class="fa-brands fa-twitter fa-stack-1x fa-inverse"></i></span></a>';
        }
        if (in_array('YouTube', $git['social'])) {
            echo '<a href="' . get_field('youtube_url', 'options') . '" target="_blank" itemprop="url"><span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i class="fa-brands fa-youtube-play fa-stack-1x fa-inverse"></i></span></a>';
        }
    ?>
  </div>
</div>
<div class="col-8 offset-2 text-center halfcircle-container">
  <div class="div-rounded ss-halfcircle halfcircle-green">
    <div class="halfcircle-content fw-bold">
      <a href="<?=$git['link']?>"><?=$git['link_title']?> <span class="arrow arrow-block mt-2"></span></a>
    </div>
  </div>
</div>
</div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}