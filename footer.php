<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
?>
<div id="footer-top"></div>
<div class="footer pt-5 pb-3">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-3">
                <div class="mb-4">
                    <img src="<?=get_stylesheet_directory_uri()?>/img/afiniti-logo--wo.svg" class="footer-logo" alt="Afiniti Logo">
                </div>
                <div>Using our creative and innovative approach to delivering change, we enable and equip your people to progress through every step of the change journey.</div>
            </div>
            <div class="col-lg-3">
                <div class="footer__heading">Contact us</div>
                <ul class="fa-ul mb-4">
                    <li><span class="fa-li"><i class="fa-solid fa-phone"></i></span> <a href="tel:<?=parse_phone(get_field('phone','options'))?>"><?=get_field('phone','options')?></a></li>
                    <li><span class="fa-li"><i class="fa-solid fa-envelope"></i></span> <a href="mailto:<?=get_field('email','options')?>"><?=get_field('email','options')?></a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <div class="footer__heading">Downloads</div>
                <?=wp_nav_menu( array('theme_location' => 'footer_menu1') )?>
            </div>
            <div class="col-lg-3">
                <div class="footer__heading">Connect</div>
                <div class="social-icons mb-2">
                    <?php
                    $social = get_field('social', 'options');
                    if ($social['twitter_url']) {
                        ?>
                    <a href="<?=$social['twitter_url']?>" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    <?php
                    }
                    if ($social['linkedin_url']) {
                        ?>
                    <a href="<?=$social['linkedin_url']?>" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                    <?php
                    }
                    if ($social['facebook_url']) {
                        ?>
                    <a href="<?=$social['facebook_url']?>" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <?php
                    }
                    if ($social['youtube_url']) {
                        ?>
                    <a href="<?=$social['youtube_url']?>" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                    <?php
                    }
                    ?>
                </div>
                <div class="footer__heading">Afiniti Newsletter</div>
                    MC form here
            </div>
        </div>
    </div>
</div>
<div class="colophon">
    <div class="container py-2">
        <div class="d-flex flex-wrap justify-content-between">
            <div class="col-md-8 text-center text-md-start">
                &copy; <?=date('Y')?> Afiniti
            </div>
            <div class="col-md-4 d-flex align-items-center justify-content-end">
                <div class="link"><a href="/privacy-policy/">Privacy & Cookies</a></div>
                <div class="link"><a href="/terms/">Refunds &amp; Cancellations</a></div>
                <a href="https://www.chillibyte.co.uk/" rel="nofollow noopener" target="_blank" class="cb" title="Digital Marketing by Chillibyte"></a>
            </div>
        </div>
    </div>
</div>
<?php
/*
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
    });
</script>
<?php
*/

wp_footer();
if (get_field('gtm_property', 'options')) {
    ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?=get_field('gtm_property', 'options')?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <?php
}
?>
</body>

</html>