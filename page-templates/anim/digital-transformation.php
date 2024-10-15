<style>
#bc-anim-1 {
    height: 90px;
    clear: both;
}
#bc-anim-1 img { max-width: none; }
#bc-clouds-1 {
    position: relative;
    top: -170px;
    height: 50px;
    left: 200px;
    animation-name: slideInLeft;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#bc-tree {
    position: relative;
    top: -160px;
    height: 190px;
    left: -60px;
    animation-name: fadeIn;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#bc-clouds-2 {
    position: relative;
    top: -190px;
    height: 80px;
    left: -110px;
    animation-name: slideInRight;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
}
@media screen and (max-width:768px) {
    #bc-anim-1 { 
        height: 100px;
    }
    #bc-tree {
        top: -140px;
        height: 200px;
        left: 70px;
    }
    #bc-clouds-1 {
        height: 30px;
        left: 20px;
    }
    #bc-clouds-2 {
        height: 45px;
        left: -10px;
    }
}
</style>
<div class="container-xl" id="bc-anim-1">
    <div class="row">
        <div class="col-3">
            <img id="bc-clouds-1" src="<?=get_stylesheet_directory_uri()?>/img/anim/Mid-Cloud.png">
        </div>
        <div class="col-5">
            <img id="bc-tree" src="<?=get_stylesheet_directory_uri()?>/img/anim/Tree.png">
        </div>
        <div class="col-4">
            <img id="bc-clouds-2" src="<?=get_stylesheet_directory_uri()?>/img/anim/Big-Cloud.png">
        </div>
    </div>
</div>