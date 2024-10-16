<style>
  #contact-anim {
  height: 140px;
  clear: both; }
  #contact-anim img {
    max-width: none; }
  #contact-anim #contact-man {
    position: relative;
    top: -160px;
    right: -80px;
    height: 250px;
    animation-name: slideInLeft;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right; }
  #contact-anim #contact-sign {
    position: relative;
    top: -140px;
    height: 250px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out; }

@media screen and (max-width: 768px) {
  #contact-anim {
    height: 80px; }
    #contact-anim #contact-man {
      top: -160px;
      height: 210px;
      right: -70px; }
    #contact-anim #contact-sign {
      top: -140px;
      height: 200px;
      left: -30px; } }
</style>
<div class="container-xl" id="contact-anim">
    <div class="row">
        <div class="col-6">
            <img id="contact-man" src="<?=get_stylesheet_directory_uri()?>/img/anim/Thinking-Man-Reverse.png">
        </div>
        <div class="col-6">
            <img id="contact-sign" src="<?=get_stylesheet_directory_uri()?>/img/anim/Sign-post.png">
        </div>
    </div>
</div>