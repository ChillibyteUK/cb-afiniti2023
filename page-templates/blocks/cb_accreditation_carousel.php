<?php
$classes = $block['className'] ?? null;
?>
<!-- accreditation_carousel -->
<section class="accreditation_carousel <?=$classes?>">
    <div class="container-xl py-4">
        <div class="acc_container">
            <?php

$accs = get_field('accreditations', 'options');
foreach ($accs as $a) {
    ?>
            <div class="accreditation">
                <a href="/about-us/"><img
                        src="<?=wp_get_attachment_image_url($a)?>"
                        alt=""></a>
            </div>
            <?php
}

?>
        </div>
    </div>
</section>

<?php
add_action('wp_footer', function () {
    ?>
<script src="<?=get_stylesheet_directory_uri()?>/js/slick.min.js"></script>
<script>
    jQuery(function($) {
        $('.acc_container').slick({
            infinite: true,
            slidesToShow: 5,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 4000,
            arrows: false,
            responsive: [{
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 2,
                        autoplay: true
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true
                    }
                }
            ]
        });
    });
</script>
<?php
}, 9999);
?>