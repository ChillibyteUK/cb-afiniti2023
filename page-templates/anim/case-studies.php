<style>
    #ics-anim {
  height: 140px;
  clear: both; }
  #ics-anim img {
    max-width: none; }
  #ics-anim #ics-lamp {
    position: relative;
    top: -160px;
    height: 250px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right; }
  #ics-anim #ics-case {
    position: relative;
    top: -140px;
    height: 250px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out; }

@media screen and (max-width: 768px) {
  #ics-anim {
    height: 80px; }
    #ics-anim #ics-lamp {
      top: -190px;
      height: 210px;
      right: -100px; }
    #ics-anim #ics-case {
      top: -110px;
      height: 200px;
      left: -50px; } }
</style>
<div class="container-xl" id="ics-anim">
    <div class="row">
        <div class="col-6">
            <img id="ics-lamp" src="<?=get_stylesheet_directory_uri()?>/img/anim/desk_lamp-300x299.png">
        </div>
        <div class="col-6">
            <img id="ics-case" src="<?=get_stylesheet_directory_uri()?>/img/anim/case-300x294.png">
        </div>
    </div>
</div>