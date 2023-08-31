
<style>
#ins-anim {
  height: 140px;
  clear: both;
}
#ins-anim img {
    max-width: none;
}
#ins-anim #ins-star-1 {
    position: relative;
    top: -160px;
    height: 47px;
    left: 160px;
    animation-name: fadeIn;
    animation-duration: 1.1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#ins-anim #ins-star-2 {
    position: relative;
    top: -120px;
    height: 51px;
    left: 30px;
    animation-name: fadeIn;
    animation-duration: 1.1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#ins-anim #ins-cloud-1 {
    position: relative;
    top: -160px;
    height: 50px;
    left: -240px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
}
#ins-anim #ins-cloud-2 {
    position: relative;
    top: -130px;
    height: 30px;
    left: -260px;
    animation-name: fadeIn;
    animation-duration: 1.1s;
    animation-timing-function: ease-in-out;
}
#ins-anim #ins-insights {
    position: relative;
    top: -120px;
    height: 215px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
}

@media screen and (max-width: 768px) {
  #ins-anim #ins-star-1 {
    top: -200px;
    left: 110px;
  }
  #ins-anim #ins-star-2 {
    top: -150px;
    left: -40px;
  }
  #ins-anim #ins-cloud-1 {
    top: -180px;
    left: -60px;
  }
  #ins-anim #ins-cloud-2 {
    top: -110px;
    left: -40px;
  }
  #ins-anim #ins-insights {
    top: -100px;
    height: 160px;
    left: -100px;
  }
}
</style>
<div class="container-xl" id="ins-anim">
    <div class="row">
        <div class="col-2">
            <img id="ins-star-1" src="<?=get_stylesheet_directory_uri()?>/img/anim/Star_3-63x47.png">
        </div>
        <div class="col-2">
            <img id="ins-star-2" src="<?=get_stylesheet_directory_uri()?>/img/anim/Star_1-46x51.png">
        </div>
        <div class="col-4">
            <img id="ins-insights" src="<?=get_stylesheet_directory_uri()?>/img/anim/insights-436x215.png">
        </div>
        <div class="col-2">
            <img id="ins-cloud-1" src="<?=get_stylesheet_directory_uri()?>/img/anim/Mid-Cloud.png">
        </div>
        <div class="col-2">
            <img id="ins-cloud-2" src="<?=get_stylesheet_directory_uri()?>/img/anim/Small-Cloud.png">
        </div>

    </div>
</div>