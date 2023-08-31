<style>
#bc-anim-1 {
    height: 200px;
    clear: both;
}
#bc-anim-1 img { max-width: none; }
#bc-clouds-1 {
    position: relative;
    top: -200px;
    height: 50px;
    left: 120px;
    animation-name: slideInLeft;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#bc-clouds-2 {
    position: relative;
    top: -210px;
    height: 80px;
    left: 60px;
    animation-name: slideInLeft;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#bc-ship {
    position: relative;
    top: -160px;
    height: 280px;
    animation-name: slideInLeft;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
    float: right;
}
#bc-person {
    position: relative;
    top: -35px;
    height: 170px;
    animation-name: slideInRight;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
}
#bc-birds {
    position: relative;
    top: -130px;
    height: 80px;
    left: -180px;
    animation-name: slideInRight;
    animation-duration: 1s;
    animation-timing-function: ease-in-out;
}
@media screen and (max-width:768px) {
    #bc-anim-1 { 
        height: 100px;
    }
    #bc-ship {
        top: -140px;
        height: 200px;
        right: -45px;
    }
    #bc-clouds-1 {
        height: 30px;
        left: 90px;
    }
    #bc-clouds-2 {
        height: 45px;
        left: 30px;
    }
    #bc-person {
        height: 80px;
        top: -15px;
        left: 40px;
    }
    #bc-birds {
        height: 45px;
        left: -50px;
    }
}
</style>
<div class="container-xl" id="bc-anim-1">
    <div class="row">
        <div class="col-2">
            <img id="bc-clouds-1" src="<?=get_stylesheet_directory_uri()?>/img/anim/Small-Cloud.png">
            <img id="bc-clouds-2" src="<?=get_stylesheet_directory_uri()?>/img/anim/Mid-Cloud.png">
        </div>
        <div class="col-5">
            <img id="bc-ship" src="<?=get_stylesheet_directory_uri()?>/img/anim/Flying-Ship.png">
        </div>
        <div class="col-2">
            <img id="bc-person" src="<?=get_stylesheet_directory_uri()?>/img/anim/Thinking-Man-Reverse.png">
        </div>
        <div class="col-3">
            <img id="bc-birds" src="<?=get_stylesheet_directory_uri()?>/img/anim/Birds.png">
        </div>
    </div>
</div>