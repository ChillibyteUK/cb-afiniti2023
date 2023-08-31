<?php
/**
 * The template for displaying search forms
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

$uid               = wp_unique_id('s-'); // The search form specific unique ID for the input.

$aria_label = '';
if (isset($args['aria_label']) && ! empty($args['aria_label'])) {
    $aria_label = 'aria-label="' . esc_attr($args['aria_label']) . '"';
}

if (isset($_GET['post_type'])) {
    $type = $_GET['post_type'];
}

?>
<div class="row">
    <div class="col-md-6">
        <form role="search" class="search-form" method="get"
            action="<?php echo esc_url(home_url('/')); ?>"
            <?php echo $aria_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.?>>
            <label class="screen-reader-text"
                for="<?php echo $uid; ?>"><?php echo esc_html_x('Search for:', 'label', 'understrap'); ?></label>
            <input type="search" class="form-control quicksearch"
                id="<?php echo $uid; ?>" name="s"
                value="<?php the_search_query(); ?>"
                placeholder="Search Insights">
            <button type="submit" class="search"></button>
            <input type="hidden" name="post_type" value="<?=$type?>">
        </form>
    </div>
</div>
<?php
?>