<style>
#op-anim {
    height: 140px;
    clear: both;
    text-align: center;
}
#op-anim img {
    max-width: none;
}
#op-anim #op-team {
    position: relative;
    top: -250px;
    height: 380px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
}
</style>
<div class="container-xl" id="op-anim">
    <div class="row">
        <div class="col-12">
            <img id="op-team" src="<?=get_stylesheet_directory_uri()?>/img/anim/Team_Map-800px.png">
        </div>
    </div>
</div>