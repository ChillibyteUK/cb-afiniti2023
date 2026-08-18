<?php
/**
 * CRA submission handling.
 *
 * The tool used to post straight at cra.php, a bare web-callable file with no
 * ABSPATH guard that read $_REQUEST and created a published post plus an email
 * with no validation of any kind. A GET was enough:
 *
 *   /wp-content/themes/cb-afiniti2023/cra.php?data={"contactEmail":"..."}
 *
 * which is where submissions with no company name came from - anything that
 * follows URLs with query strings could mint one. It also let anyone send mail
 * appearing to come from enquiries@afiniti.co.uk to an arbitrary address.
 *
 * Everything now funnels through cb_cra_handle_submission(). cra.php is kept as
 * a thin shim because the site sits behind full page caching, so visitors on
 * stale HTML will keep posting to the old URL for a while yet.
 *
 * NOTE: no nonce, and no captcha either. See cb_cra_form_token() for what
 * replaced it and why.
 *
 * The asset worth protecting is not the cra post type - it is wp_mail(). A
 * successful submission makes this server send HTML mail From afiniti.co.uk to
 * an address the submitter chose. Unbounded, that is a mail-bomb amplifier
 * pointed at a third party and a fast route to the sending domain being
 * blocklisted. The controls below are sized for that, not for junk rows.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

/**
 * The levers a score payload may contain, and the maximum per lever.
 */
/*
 * The lever list itself now lives in inc/cb-cra-levers.php - see
 * cb_cra_lever_keys(). It was duplicated here.
 */
const CB_CRA_MAX_LEVER_SCORE = 30;

/**
 * Form token window, and how many past windows still verify.
 *
 * A day, with three accepted, so a token is good for 48-72 hours. That is well
 * past any sane full page cache TTL - the point of the window is to expire
 * scraped tokens, not to be tight.
 */
const CB_CRA_TOKEN_WINDOW           = DAY_IN_SECONDS;
const CB_CRA_TOKEN_WINDOWS_ACCEPTED = 3;

add_action( 'admin_post_nopriv_cb_cra_submit', 'cb_cra_handle_submission' );
add_action( 'admin_post_cb_cra_submit', 'cb_cra_handle_submission' );

/**
 * Reads a CRA setting from Site-Wide Settings, with a constant override.
 *
 * A matching constant always wins, which keeps the option of defining the
 * secret in wp-config.php on a given environment rather than storing it in the
 * database - useful for staging, and it keeps the value out of DB exports.
 *
 * @param string $name     Field name on the options page.
 * @param string $constant Constant that overrides it.
 * @param mixed  $default  Value when neither is set.
 * @return mixed
 */
function cb_cra_setting( $name, $constant, $default = '' ) {
    if ( $constant && defined( $constant ) ) {
        return constant( $constant );
    }

    if ( ! function_exists( 'get_field' ) ) {
        return $default;
    }

    $value = get_field( $name, 'options' );

    if ( null === $value || '' === $value || false === $value ) {
        return $default;
    }

    return $value;
}

/**
 * Max submissions from one IP per hour.
 *
 * A real visitor submits once. Three leaves room for someone retrying after a
 * validation bounce without leaving much headroom for anyone else.
 *
 * @return int
 */
function cb_cra_rate_limit() {
    return (int) cb_cra_setting( 'cra_rate_limit', 'CB_CRA_RATE_LIMIT', 3 );
}

/**
 * Max submissions site wide per hour - a circuit breaker.
 *
 * Per-IP limiting does nothing against a distributed flood, and the cost of one
 * is measured in sending reputation rather than disk. Tripping this stops mail
 * going out at all, which is the outcome to prefer over getting the domain
 * blocklisted. Well above any plausible real hour of traffic for this tool.
 *
 * @return int
 */
function cb_cra_global_limit() {
    return (int) cb_cra_setting( 'cra_global_limit', 'CB_CRA_GLOBAL_LIMIT', 30 );
}

/**
 * Where the internal notification goes.
 *
 * @return string
 */
function cb_cra_notify_email() {
    return (string) cb_cra_setting( 'cra_notify_email', 'CB_CRA_NOTIFY_EMAIL', 'enquiries@afiniti.co.uk' );
}

/**
 * A token proving the submission came from a page this site rendered recently.
 *
 * This replaces reCAPTCHA v3, which was removed. The v3 that was here never
 * worked - the template fetched a token and threw it away without ever posting
 * it - but it was not worth finishing either: it is a probabilistic score
 * needing a blind threshold, and on a low volume B2B lead form one silently
 * rejected real enquiry costs more than a hundred junk rows. It also put a
 * Google script on the page, with the consent question that brings.
 *
 * What is left is deliberately not a nonce. A nonce is per user and would go
 * stale behind the host's full page cache, breaking real submissions - the
 * failure mode this whole file exists to stop. Instead: an HMAC of the current
 * time window, so every visitor within a window gets the same value and cached
 * HTML stays valid.
 *
 * Honest about the bar this sets. A determined attacker fetches the page and
 * scrapes the token; nothing short of a captcha stops that, and we are choosing
 * not to have one. What it does stop is the case that actually happens - a
 * script posting a fixed payload straight at admin-post.php, having never
 * loaded the form - plus replay of any payload more than three days old. The
 * honeypot covers indiscriminate form fillers; the rate limits cap whatever
 * gets through.
 *
 * @param int $offset Windows back from now. 0 is the current window.
 * @return string
 */
function cb_cra_form_token( $offset = 0 ) {
    $window = (int) floor( time() / CB_CRA_TOKEN_WINDOW ) + (int) $offset;

    return hash_hmac( 'sha256', 'cb_cra_form|' . $window, wp_salt( 'cb_cra_form' ) );
}

/**
 * Verifies a form token against the current and recent windows.
 *
 * @param string $token Token from the submitted form.
 * @return bool
 */
function cb_cra_verify_form_token( $token ) {
    if ( ! is_string( $token ) || '' === $token ) {
        return false;
    }

    for ( $offset = 0; $offset > -CB_CRA_TOKEN_WINDOWS_ACCEPTED; $offset-- ) {
        if ( hash_equals( cb_cra_form_token( $offset ), $token ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Best guess at the client IP, for rate limiting only.
 *
 * X-Forwarded-For is spoofable in general, but is set by the host's proxy here.
 * Rate limiting is defence in depth, not the primary control, so a bad value
 * costs little.
 *
 * @return string
 */
function cb_cra_client_ip() {
    if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $forwarded = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
        $candidate = trim( $forwarded[0] );

        if ( filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
            return $candidate;
        }
    }

    return isset( $_SERVER['REMOTE_ADDR'] )
        ? (string) sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) )
        : '';
}

/**
 * Sends the visitor back to the tool with an error code rather than dumping
 * them on the home page, which is what used to happen on any failure.
 *
 * @param string $code Short machine readable reason.
 * @return void
 */
function cb_cra_bail( $code ) {
    /*
     * The referer first, because it is the page the visitor was actually on and
     * needs no configuration. cra_tool_page_id is a hand-entered setting and is
     * empty as often as not - when it is, every rejection used to land on the
     * home page, which is the silent bounce this error code exists to replace.
     * wp_safe_redirect() rejects off-site hosts, so an attacker-supplied referer
     * cannot turn this into an open redirect.
     */
    $target  = '';
    $referer = wp_get_referer();

    if ( $referer ) {
        $target = remove_query_arg( 'cra_error', $referer );
    }

    if ( ! $target ) {
        $page_id = cb_cra_tool_page_id();
        $target  = $page_id ? get_permalink( $page_id ) : '';
    }

    if ( ! $target ) {
        $target = home_url( '/' );
    }

    wp_safe_redirect( add_query_arg( 'cra_error', rawurlencode( $code ), $target ), 302 );
    exit;
}

/**
 * Cleans the contact payload, returning null when it is not usable.
 *
 * @param mixed $raw Decoded JSON payload.
 * @return array|null
 */
function cb_cra_clean_contact( $raw ) {
    if ( ! is_array( $raw ) ) {
        return null;
    }

    $fields = array(
        'contactName',
        'contactTitle',
        'orgName',
        'contactPhone',
        'contactMobile',
        'contactEmail',
        'contactHowHear',
        'changeInProgress',
        'changeDetail',
        'changeRole',
        'consent',
    );

    $clean = array();

    foreach ( $fields as $field ) {
        $value = $raw[ $field ] ?? '';
        $value = is_scalar( $value ) ? trim( (string) $value ) : '';

        $clean[ $field ] = 'changeDetail' === $field
            ? sanitize_textarea_field( $value )
            : sanitize_text_field( $value );
    }

    // A real email address, not merely a non-empty string. The old check was
    // empty(), so "abc" or " " sailed through and wp_mail() then failed
    // silently, leaving a result nobody could reach.
    if ( ! is_email( $clean['contactEmail'] ) ) {
        return null;
    }

    $clean['contactEmail'] = sanitize_email( $clean['contactEmail'] );

    // Both were required in the UI but never server side, which is how results
    // with no company name got created.
    if ( '' === $clean['orgName'] || '' === $clean['contactName'] ) {
        return null;
    }

    return $clean;
}

/**
 * Cleans the scores payload: known levers only, clamped to a sane range.
 *
 * @param mixed $raw Decoded JSON payload.
 * @return array
 */
function cb_cra_clean_scores( $raw ) {
    $clean = array();

    foreach ( cb_cra_lever_keys() as $lever ) {
        $value = is_array( $raw ) ? ( $raw[ $lever ] ?? 0 ) : 0;
        $value = is_numeric( $value ) ? (int) $value : 0;

        $clean[ $lever ] = max( 0, min( CB_CRA_MAX_LEVER_SCORE, $value ) );
    }

    return $clean;
}

/**
 * Validates a CRA submission, stores it, emails the visitor and redirects to
 * the results page.
 *
 * @return void
 */
function cb_cra_handle_submission() {
    // POST only. Accepting $_REQUEST meant a GET could create a submission,
    // which is what any crawler following a query string would do.
    if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) ) {
        cb_cra_bail( 'method' );
    }

    // Honeypot: a real visitor never sees this field.
    if ( ! empty( $_POST['emailaddress'] ) ) {
        wp_safe_redirect( home_url( '/' ), 302 );
        exit;
    }

    $token = isset( $_POST['cra_token'] )
        ? sanitize_text_field( wp_unslash( $_POST['cra_token'] ) )
        : '';

    if ( ! cb_cra_verify_form_token( $token ) ) {
        cb_cra_bail( 'expired' );
    }

    $ip       = cb_cra_client_ip();
    $rate_key = 'cb_cra_rate_' . md5( $ip );
    $recent   = (int) get_transient( $rate_key );

    if ( $ip && $recent >= cb_cra_rate_limit() ) {
        cb_cra_bail( 'rate' );
    }

    /*
     * Keyed by the hour rather than using a rolling transient, so the count
     * cannot be walked forward indefinitely by submitting just under the limit.
     */
    $global_key = 'cb_cra_rate_all_' . (int) floor( time() / HOUR_IN_SECONDS );
    $global     = (int) get_transient( $global_key );

    if ( $global >= cb_cra_global_limit() ) {
        error_log( 'CRA: global hourly submission ceiling hit - mail suppressed until the hour rolls over.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        cb_cra_bail( 'rate' );
    }

    $data   = cb_cra_clean_contact( json_decode( wp_unslash( $_POST['data'] ?? '' ), true ) );
    $scores = cb_cra_clean_scores( json_decode( wp_unslash( $_POST['scores'] ?? '' ), true ) );

    if ( null === $data ) {
        cb_cra_bail( 'invalid' );
    }

    if ( $ip ) {
        set_transient( $rate_key, $recent + 1, HOUR_IN_SECONDS );
    }

    set_transient( $global_key, $global + 1, 2 * HOUR_IN_SECONDS );

    $post_id = wp_insert_post(
        array(
            'post_title'  => $data['orgName'],
            'post_name'   => function_exists( 'random_str' ) ? random_str( 32 ) : wp_generate_password( 32, false, false ),
            'post_type'   => 'cra',
            'post_status' => 'publish',
        ),
        true
    );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        cb_cra_bail( 'save' );
    }

    update_field( 'field_64955d11c677b', $data, $post_id );
    update_field( 'field_649564f0c6785', $scores, $post_id );

    /*
     * The denominator this result was scored against, stored with it. The
     * question set is editable, so the maximum per lever can change - and
     * without this, changing it would retroactively reinterpret every result
     * already saved. See cb_cra_result_maxima().
     */
    update_post_meta( $post_id, CB_CRA_MAXIMA_META, cb_cra_lever_maxima( cb_cra_tool_page_id() ) );

    $results = get_permalink( $post_id );

    cb_cra_send_results_email( $data, $results );
    cb_cra_notify_team( $data, $results );

    wp_safe_redirect( $results, 302 );
    exit;
}

/**
 * Emails the visitor a link to their results.
 *
 * @param array  $data    Cleaned contact payload.
 * @param string $results Results page URL.
 * @return void
 */
function cb_cra_send_results_email( $data, $results ) {
    $name    = $data['contactName'];
    $subject = 'Afiniti Change Readiness Assessment Tool Results';

    // The logo is a hosted image rather than the base64 data URI this used to
    // carry - most mail clients block data URIs outright.
    $logo = get_stylesheet_directory_uri() . '/img/afiniti-logo-v2--dark.png';

    $message  = '<p>Dear ' . esc_html( $name ) . ',</p>';
    $message .= '<p>Thank you for completing the online Afiniti Change Readiness Assessment. You can view and share your results at any time here: <a href="' . esc_url( $results ) . '">' . esc_html( $results ) . '</a>.</p>';
    $message .= '<p>If you would like to speak with our team about your change programme, just reply to this email or complete our <a href="' . esc_url( home_url( '/contact-us/' ) ) . '">online enquiry form</a> and we will get back to you.</p>';
    $message .= '<p>Best regards,</p><p>Afiniti</p>';
    $message .= '<img src="' . esc_url( $logo ) . '" width="200" alt="Afiniti">';

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Afiniti <enquiries@afiniti.co.uk>',
    );

    wp_mail( $data['contactEmail'], $subject, $message, $headers );
}

/**
 * Tells the team about a new submission.
 *
 * This used to be a Bcc on the visitor's mail. Separating them means the two
 * can be throttled, filtered or turned off independently - a flood aimed at the
 * public endpoint no longer arrives as a flood in the enquiries inbox with the
 * team's own copy attached to it. The submitted address is only ever shown as
 * text here, never used as a header.
 *
 * @param array  $data    Cleaned contact payload.
 * @param string $results Results page URL.
 * @return void
 */
function cb_cra_notify_team( $data, $results ) {
    $to = cb_cra_notify_email();

    if ( ! is_email( $to ) ) {
        return;
    }

    $subject = 'CRA submission: ' . $data['orgName'];

    $message  = '<p>A new Change Readiness Assessment has been completed.</p>';
    $message .= '<ul>';
    $message .= '<li><strong>Organisation:</strong> ' . esc_html( $data['orgName'] ) . '</li>';
    $message .= '<li><strong>Contact:</strong> ' . esc_html( $data['contactName'] ) . '</li>';
    $message .= '<li><strong>Email:</strong> ' . esc_html( $data['contactEmail'] ) . '</li>';
    $message .= '<li><strong>Phone:</strong> ' . esc_html( $data['contactPhone'] ) . '</li>';
    $message .= '</ul>';
    $message .= '<p><a href="' . esc_url( $results ) . '">View the results</a></p>';

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Afiniti <enquiries@afiniti.co.uk>',
    );

    wp_mail( $to, $subject, $message, $headers );
}
