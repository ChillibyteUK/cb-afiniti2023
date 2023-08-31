<style>
#crt-anim {
    height: 140px;
    clear: both;
    text-align: center;
}
#crt-anim img {
    max-width: none;
}
#crt-anim #crt-machine {
    position: relative;
    top: -220px;
    height: 320px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    transform: translateX(15%);
    object-fit: contain;
    max-width: 90vw;
}
</style>
<div class="container-xl" id="crt-anim">
    <div class="row">
        <div class="col-12">
            <img id="crt-machine" src="<?=get_stylesheet_directory_uri()?>/img/anim/Change-Readiness-Tool.png">
        </div>
    </div>
</div>