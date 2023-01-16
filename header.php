<?php
/**
 * The header for the theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package cb-hydronix
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, minimum-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/fonts/ubuntu-v20-latin-regular.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/fonts/ubuntu-v20-latin-500.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri(); ?>/fonts/ubuntu-v20-latin-700.woff2" as="font" type="font/woff2" crossorigin="anonymous">
<?php
if (get_field('ga_property', 'options')) { 
    ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?=get_field('ga_property','options')?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?=get_field('ga_property','options')?>');
</script>
    <?php
}
if (get_field('gtm_property', 'options')) {
    ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?=get_field('gtm_property','options')?>');</script>
<!-- End Google Tag Manager -->
    <?php
}
if (get_field('google_site_verification','options')) {
    echo '<meta name="google-site-verification" content="' . get_field('google_site_verification','options') . '" />';
}
if (get_field('bing_site_verification','options')) {
    echo '<meta name="msvalidate.01" content="' . get_field('bing_site_verification','options') . '" />';
}

wp_head();
?>
	
<script type="application/ld+json">
{
  "@context": "http://www.schema.org",
  "@type": "Organization",
  "name": "Afiniti",
  "legalName" : "Afiniti LLP",
  "url": "https://www.afiniti.co.uk/",
  "logo": "https://www.afiniti.co.uk/wp-content/uploads/2020/01/afiniti-logo-v2.png",
  "description": "Afiniti is an award-winning business change consultancy that delivers change with a people focus. We are passionate about making change a force for good driving progress and innovation.",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Suite 302, Dowgate Hill House,  14-16 Dowgate Hill",
    "addressLocality": "London",
    "addressRegion": "London",
    "postalCode": "EC4R 2SU",
    "addressCountry": "United Kingdom"
   },
   "contactPoint": [
    { "@type": "ContactPoint",
    "telephone": " 0845 608 0104",
    "email" : " enquiries@afiniti.co.uk",
    "contactType": "enquiries"
    }
   ],
   "sameAs": [
   "https://twitter.com/afinitiLLP",
   "https://www.linkedin.com/company/afiniti",
   "https://www.youtube.com/user/Afinitiltd",
   "https://www.crunchbase.com/organization/afinit-ltd",
   "https://www.facebook.com/AfinitiConsultants",
   "https://www.wikidata.org/wiki/Q107664234"
   ]
}
</script>
	
</head>

<body <?php body_class(); ?> <?php understrap_body_attributes(); ?>>
<?php
do_action('wp_body_open'); 
?>
<style>

</style>
<div class="site" id="page">
	<div id="wrapper-navbar" class="fixed-top">

		<a class="skip-link sr-only sr-only-focusable" href="#content"><?php esc_html_e( 'Skip to content', 'understrap' ); ?></a>

        <nav class="navbar fixed-top navbar-expand-lg navbar-light bg--light">
            <div class="container-xl">
                <div class="d-none d-sm-block mb-0 me-lg-5"><a href="/" aria-title="Home"><img class="logo" src="<?=get_stylesheet_directory_uri()?>/img/rbf-logo--colour.svg"></a></div>
                <div class="d-sm-none mb-0 me-lg-5"><a href="/" aria-title="Home"><img class="logo" src="<?=get_stylesheet_directory_uri()?>/img/rbf-logo--icon.svg"></a></div>
                <a class="d-lg-none btn btn-donate" href="/donate/">DONATE</a>
                <button class="navbar-toggler mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-label="Navigation">
                    <i class="fa fa-navicon" aria-hidden="true"></i>
                </button>
                <div class="collapse navbar-collapse flex-column align-items-end ml-lg-2 ml-0" id="navbarCollapse">
                    <div class="d-none d-lg-flex align-items-lg-center ml-auto">
                        <div class="me-4 search">
                            <form id="search" class="d-flex w-100 justify-content-center" action="/" method="get" accept-charset="utf-8">
                                <input type="text" name="s" placeholder="" style="font-family:Roboto,Arial,FontAwesome">
                            </form>
                        </div>
                        <a class="btn btn-donate" href="/donate/">DONATE</a>
                    </div>
                    <?php
                            wp_nav_menu(
                                array(
                                    'theme_location'  => 'primary_nav',
                                    'container_class' => 'w-100',
                                    // 'container_id'    => 'primaryNav',
                                    'menu_class'      => 'navbar-nav justify-content-between align-items-lg-center',
                                    'fallback_cb'     => '',
                                    'menu_id'         => 'main-menu',
                                    'depth'           => 3,
                                    'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
                                )
                            );
                    ?>
                </div>
            </div>
        </nav>
    </div>
</div>