<style>
#rw-anim {
    height: 140px;
    clear: both;
    text-align: center;
}
#rw-anim img {
    max-width: none;
}
#rw-anim #rw-machine {
    position: relative;
    top: -190px;
    height: 260px;
    animation-name: slideInLeft;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    object-fit: contain;
    max-width: 90vw;
}
</style>
<div class="container-xl" id="rw-anim">
    <div class="row">
        <div class="col-12">
            <img id="rw-machine" src="<?=get_stylesheet_directory_uri()?>/img/anim/Rowers.png">
        </div>
    </div>
</div>