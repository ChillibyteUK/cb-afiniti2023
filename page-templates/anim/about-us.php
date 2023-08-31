<style>
    /* About Us */
    #au-anim {
        height: 60px;
        clear: both;
    }

    #au-anim img {
        max-width: none;
    }

    #au-anim #au-star-1 {
        position: relative;
        top: -130px;
        right: 200px;
        float: right;
        animation-name: fadeIn;
        animation-duration: 1s;
        animation-timing-function: ease-in-out;
    }

    #au-anim #au-moon {
        position: relative;
        top: -200px;
        float: right;
        animation-name: fadeIn;
        animation-duration: 1s;
        animation-timing-function: ease-in-out;
    }

    #au-anim #au-scope {
        position: relative;
        top: -160px;
        animation-name: fadeIn;
        animation-duration: 1s;
        animation-timing-function: ease-in-out;
    }

    #au-anim #au-star-2 {
        position: relative;
        top: -190px;
        left: -100px;
        animation-name: fadeIn;
        animation-duration: 1s;
        animation-timing-function: ease-in-out;
    }

    #au-anim #au-star-3 {
        position: relative;
        top: -210px;
        left: -250px;
        animation-name: fadeIn;
        animation-duration: 1s;
        animation-timing-function: ease-in-out;
    }

    @media screen and (max-width: 768px) {
        #au-anim #au-star-1 {
            top: -90px;
            right: 50px;
            height: 30px;
        }

        #au-anim #au-moon {
            top: -170px;
            height: 57px;
            left: 20px;
        }

        #au-anim #au-scope {
            left: -60px;
        }

        #au-anim #au-star-2 {
            left: 20px;
            height: 30px;
        }

        #au-anim #au-star-3 {
            height: 20px;
            left: -90px;
        }
    }
</style>
<div class="container-xl" id="au-anim">
    <div class="row">
        <div class="col-4">
            <img id="au-star-1"
                src="<?=get_stylesheet_directory_uri()?>/img/anim/Star_1-46x51.png">
            <img id="au-moon"
                src="<?=get_stylesheet_directory_uri()?>/img/anim/moon-78x87.png">
        </div>
        <div class="col-4">
            <img id="au-scope"
                src="<?=get_stylesheet_directory_uri()?>/img/anim/telescope-229x191.png">
        </div>
        <div class="col-4">
            <img id="au-star-2"
                src="<?=get_stylesheet_directory_uri()?>/img/anim/Star_3-63x47.png">
            <img id="au-star-3"
                src="<?=get_stylesheet_directory_uri()?>/img/anim/Star_2-34x36.png">
        </div>
    </div>
</div>