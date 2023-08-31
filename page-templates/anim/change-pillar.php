<style>
#bcp-anim {
  height: 140px;
  clear: both; }
  #bcp-anim img {
    max-width: none; }
  #bcp-anim #bcp-star-1 {
    position: relative;
    top: -200px;
    height: 47px;
    left: 120px;
    animation-name: fadeIn;
    animation-duration: 1.1s;
    animation-timing-function: ease-in-out;
    float: right; }
  #bcp-anim #bcp-star-2 {
    position: relative;
    top: -230px;
    height: 51px;
    left: 30px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right; }
  #bcp-anim #bcp-ship {
    position: relative;
    top: -240px;
    height: 340px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right; }

@media screen and (max-width: 768px) {
  #bcp-anim {
    height: 100px; }
    #bcp-anim #bcp-ship {
      top: -170px;
      height: 230px;
      right: -105px; }
    #bcp-anim #bcp-star-1 {
      height: 30px;
      left: 70px;
      top: -190px; }
    #bcp-anim #bcp-star-2 {
      height: 45px;
      left: -30px;
      top: -140px; } }
</style>
<div class="container-xl" id="bcp-anim">
    <div class="row">
        <div class="col-2">
            <img id="bcp-star-1" src="<?=get_stylesheet_directory_uri()?>/img/anim/Star_3-63x47.png">
        </div>
        <div class="col-2">
            <img id="bcp-star-2" src="<?=get_stylesheet_directory_uri()?>/img/anim/Star_1-46x51.png">
        </div>
        <div class="col-5">
            <img id="bcp-ship" src="<?=get_stylesheet_directory_uri()?>/img/anim/Flying-Ship_Telescope-clouds-519x436.png">
        </div>
    </div>
</div>