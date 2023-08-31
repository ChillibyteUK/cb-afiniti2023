<style>
#pw-anim {
    height: 140px;
    clear: both;
    text-align: center;
}
#pw-anim img {
    max-width: none;
}
#pw-anim #pw-machine {
    position: relative;
    top: -220px;
    height: 320px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    object-fit: contain;
    max-width: 90vw;
}
</style>
<div class="container-xl" id="pw-anim">
    <div class="row">
        <div class="col-12">
            <img id="pw-machine" src="<?=get_stylesheet_directory_uri()?>/img/anim/Pocket-Watch.png">
        </div>
    </div>
</div>