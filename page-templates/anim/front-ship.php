<style>
/* front page */
#front-anim-1 {
    height: 200px;
    clear: both;
}
#front-anim-1 img {
    max-width: none;
}
#front-ship {
    position: relative;
    top: -160px;
    height: 280px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#front-birds {
    position: relative;
    top: -180px;
    height: 80px;
    left: 120px;
    animation-name: slideInLeft;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#front-beam {
    position: relative;
    top: -35px;
    height: 170px;
    animation-name: slideInRight;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
}

@media screen and (max-width:768px) {
    #front-anim-1 { 
        height: 100px;
    }
    #front-ship {
        top: -110px;
        height: 170px;
        right: -50px;
    }
    #front-birds {
        top: -125px;
        height: 50px;
        left: 80px;
    }
    #front-beam {
        height: 85px;
        top: -45px;
    }
}
</style>
<div class="container-xl" id="front-anim-1">
  <div class="row">
    <div class="col-2">
      <img id="front-birds" src="<?=get_stylesheet_directory_uri()?>/img/anim/Birds.png">
    </div>
    <div class="col-5">
      <img id="front-ship" src="<?=get_stylesheet_directory_uri()?>/img/anim/Building-Ship.png">
    </div>
    <div class="col-5">
      <img id="front-beam" src="<?=get_stylesheet_directory_uri()?>/img/anim/Carrying-Beam.png">
    </div>
  </div>
</div>