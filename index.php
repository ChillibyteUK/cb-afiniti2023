<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
get_header();
?>
<style>
    .catflash {
        position: absolute;
        top: 0;
        left: 0;
        font-size: .875rem;
        padding: 0.25rem 0.5rem;
        color: white;
    }

    .afiniti-insight .catflash {
        background-color: var(--col-green-500);
    }

    .team-insight .catflash {
        background-color: var(--col-purple-500);
    }

    .change-101 .catflash {
        background-color: var(--col-orange-500);
    }

    .news .catflash {
        background-color: var(--col-grey-500);
    }
</style>
<main id="main">
    <!-- hero -->
    <section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
        <div class="hero__inner container-xl text-center">
            <div class="h1">Key <span>Insights from Afiniti</span></div>
        </div>
    </section>
    <?php
$anim = 'insights';
include get_stylesheet_directory() . '/page-templates/anim/' . $anim . '.php';

$page_for_posts = get_option('page_for_posts');
?>

    <div class="container-xl py-5">
        <?php
        if (get_the_content(null, false, $page_for_posts)) {
            $content = get_the_content(null, false, $page_for_posts);
            $myblocks = parse_blocks($content);
            foreach ($myblocks as $block) {
                echo render_block($block);
            }
        }
?>

        <div class="row gx-4 gy-2 mb-4">
            <div class="col-lg-4 filters">
                <?php
    $terms = get_terms(
        array(
                        'taxonomy'   => 'category',
                        'hide_empty' => true,
                        'order' => 'DESC',
                    )
    );

echo '<select class="filters-select form-select" value-group="category">';
echo '<option value="" disabled selected>Filter by category</option>';

echo '<option value="*">Show all</option>';

foreach ($terms as $term) {
    echo '<option value=".' . $term->slug . '">' . $term->name . '</option>';
}
?>
                </select>
            </div>
            <div class="col-lg-4 filters">
                <?php
    $terms = get_terms(
        array(
                        'taxonomy'   => 'insight-type',
                        'hide_empty' => true,
                        'order' => 'DESC',
                    )
    );

echo '<select class="filters-select form-select" value-group="type">';
echo '<option value="" disabled selected>Filter by type</option>';

echo '<option value="*">Show all</option>';

foreach ($terms as $term) {
    echo '<option value=".' . $term->slug . '">' . $term->name . '</option>';
}
?>
                </select>
            </div>
            <div class="col-lg-4">
                <form id="search" class="d-flex w-100 justify-content-center" action="/" method="get"
                    accept-charset="utf-8">
                    <input type="text" class="form-control quicksearch" name="s" placeholder="Search Insights">
                    <button type="submit" class="search"></button>
                    <input type="hidden" name="post_type" value="post" />
                </form>
            </div>
            <div class="col-12">
                <div class="status">
                    <div class="count"><span class="filter-count"></span> items found.</div>
                </div>
            </div>
        </div>

        <div class="row w-100" id="grid">
            <?php
        while (have_posts()) {
            the_post();
            $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if (!$img) {
                $img = get_stylesheet_directory_uri() . '/img/default.png';
            }
            $cats = get_the_category();
            $category = wp_list_pluck($cats, 'name');
            $catclass = implode(' ', array_map('cbslugify', $category));

            $types = get_the_terms($post->ID, 'insight-type');
            $type = wp_list_pluck($types, 'name');
            $catclass .= ' ' . implode(' ', array_map('cbslugify', $type));

            $the_date = get_the_date('jS F, Y');

            if (isset($types[0]->slug)) {
                switch($types[0]->slug) {
                    case 'article':
                        $flash = '<i class="fa-solid fa-newspaper"></i>';
                        break;
                    case 'video':
                        $flash = '<i class="fa-solid fa-video"></i>';
                        break;
                    case 'podcast':
                        $flash = '<i class="fa-solid fa-podcast"></i>';
                        break;
                    default:
                        $flash = '<i class="fa-solid fa-question"></i>';
                }
            }
            
            $catflash = $cats[0]->name;

            ?>
            <div class="<?=$catclass?> insight col-12 col-lg-4 mb-4">
                <a href="<?=get_the_permalink()?>">
                    <div class="post-image-container">
                        <div class="post-image mb-2"
                            style="background-image:url('<?=$img?>')">
                            <div class="img-overlay">
                                <div class="middle"><span class="arrow arrow-block arrow-white"></span></div>
                            </div>
                        </div>
                        <div class="catflash"><?=$catflash?></div>
                        <div class="flash"><?=$flash?></div>
                    </div>
                    <div class="article-title mt-2">
                        <?=get_the_title()?>
                    </div>
                    <div class="article-excerpt">
                        <?=wp_trim_words(get_the_content(), 20)?>
                    </div>
                    <div class="fw-bold py-2 arrow-link">
                        <div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span></div>
                    </div>
                </a>
            </div>
            <?php
        }
?>
        </div>
    </div>
</main>
<?php
add_action('wp_footer', function () {
    ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js"
    integrity="sha512-Zq2BOxyhvnRFXu0+WE6ojpZLOU2jdnqbrM1hmVdGzyeCa1DgM3X5Q4A/Is9xA1IkbUeDd7755dNNI/PzSf2Pew=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    (function($) {

        var $filterCount = $('.filter-count');

        // init Isotope
        var $grid = $('#grid').isotope({
            itemSelector: '.insight'
        });

        // store filter for each group
        var filters = {};

        $('.filters').on('change', function(event) {
            console.log('changed');
            var $select = $(event.target);
            // get group key
            var filterGroup = $select.attr('value-group');
            // set filter for group
            filters[filterGroup] = event.target.value;
            // combine filters
            var filterValue = concatValues(filters);
            // set filter for Isotope
            $grid.isotope({
                filter: filterValue
            });
            updateFilterCount();
        });

        // flatten object by concatting values
        function concatValues(obj) {
            var value = '';
            for (var prop in obj) {
                value += obj[prop];
            }
            console.log(value);
            return value;
        }
        var iso = $grid.data('isotope');

        function updateFilterCount() {
            $filterCount.text(iso.filteredItems.length);
        }
        updateFilterCount();

    })(jQuery);
</script>
<?php
}, 9999);

get_footer();
?>