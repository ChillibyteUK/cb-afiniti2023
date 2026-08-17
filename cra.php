<?php
/**
 * Legacy CRA submission endpoint.
 *
 * This file used to contain the whole submission pipeline, unguarded: it read
 * $_REQUEST, so a GET created a published post and sent an email to whatever
 * address the caller supplied. All of that now lives in
 * cb_cra_handle_submission() in inc/cb-cra-submit.php.
 *
 * The file is kept because the site sits behind full page caching, so visitors
 * served stale HTML will keep posting the old form action here for as long as
 * that cache lives. It is a shim, nothing more.
 *
 * Once the caches have rolled over - and certainly after the move off WP Engine
 * - this can be deleted, as the form now posts to admin-post.php.
 *
 * @package cb-afiniti2023
 */

require_once __DIR__ . '/../../../wp-load.php';

if ( ! function_exists( 'cb_cra_handle_submission' ) ) {
    wp_safe_redirect( home_url( '/' ), 302 );
    exit;
}

cb_cra_handle_submission();
