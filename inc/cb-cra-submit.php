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
 * NOTE: no nonce. The page is cached, so an embedded nonce would go stale and
 * break submissions, and on an anonymous form it adds nothing a bot cannot get
 * for itself. reCAPTCHA verification is the control that matters here.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

/**
 * The levers a score payload may contain, and the maximum per lever.
 */
const CB_CRA_LEVERS         = array( 'Leadership', 'Drivers', 'Culture', 'Engagement', 'Capability', 'Method' );
const CB_CRA_MAX_LEVER_SCORE = 30;

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
 * reCAPTCHA v3 site key. Public - it ships in the page markup.
 *
 * Falls back to the key that was previously hardcoded in the template, so the
 * form keeps working until the field is filled in.
 *
 * @return string
 */
function cb_cra_recaptcha_site_key() {
    return (string) cb_cra_setting(
        'recaptcha_site_key',
        'CB_RECAPTCHA_SITE_KEY',
        '6LeKUsApAAAAAD9wCXHTKx5BaujLUJVE8BdMQlLY'
    );
}

/**
 * Returns the reCAPTCHA secret, or an empty string when it has not been set up.
 *
 * @return string
 */
function cb_cra_recaptcha_secret() {
    return (string) cb_cra_setting( 'recaptcha_secret_key', 'CB_RECAPTCHA_SECRET', '' );
}

/**
 * Minimum reCAPTCHA v3 score to accept. 0.5 is Google's suggested default.
 *
 * @return float
 */
function cb_cra_recaptcha_min_score() {
    return (float) cb_cra_setting( 'recaptcha_min_score', 'CB_RECAPTCHA_MIN_SCORE', 0.5 );
}

/**
 * Max submissions from one IP per hour.
 *
 * @return int
 */
function cb_cra_rate_limit() {
    return (int) cb_cra_setting( 'cra_rate_limit', 'CB_CRA_RATE_LIMIT', 10 );
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
    $page_id = get_field( 'cra_tool_page_id', 'options' );
    $target  = $page_id ? get_permalink( $page_id ) : home_url( '/' );

    if ( ! $target ) {
        $target = home_url( '/' );
    }

    wp_safe_redirect( add_query_arg( 'cra_error', rawurlencode( $code ), $target ), 302 );
    exit;
}

/**
 * Verifies the reCAPTCHA token.
 *
 * Returns true when no secret is configured: the alternative is breaking every
 * submission on a live site the moment this deploys. That is deliberately loud
 * in the log so it does not go unnoticed - the protection is inert until the
 * secret is filled in under Site-Wide Settings > CRA Tool (or defined as
 * CB_RECAPTCHA_SECRET, which takes precedence).
 *
 * @param string $token The g-recaptcha-response token.
 * @return bool
 */
function cb_cra_verify_recaptcha( $token ) {
    $secret = cb_cra_recaptcha_secret();

    if ( '' === $secret ) {
        error_log( 'CRA: no reCAPTCHA secret set (Site-Wide Settings > CRA Tool) - submission accepted without captcha verification.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        return true;
    }

    if ( '' === $token ) {
        return false;
    }

    $response = wp_remote_post(
        'https://www.google.com/recaptcha/api/siteverify',
        array(
            'timeout' => 10,
            'body'    => array(
                'secret'   => $secret,
                'response' => $token,
                'remoteip' => cb_cra_client_ip(),
            ),
        )
    );

    if ( is_wp_error( $response ) ) {
        // Do not punish the visitor for Google being unreachable.
        error_log( 'CRA: reCAPTCHA request failed - ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        return true;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! is_array( $body ) || empty( $body['success'] ) ) {
        return false;
    }

    // v3 returns a score; v2 does not, in which case success alone is enough.
    if ( isset( $body['score'] ) && (float) $body['score'] < cb_cra_recaptcha_min_score() ) {
        return false;
    }

    return true;
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

    foreach ( CB_CRA_LEVERS as $lever ) {
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

    $ip            = cb_cra_client_ip();
    $rate_key      = 'cb_cra_rate_' . md5( $ip );
    $recent        = (int) get_transient( $rate_key );

    if ( $ip && $recent >= cb_cra_rate_limit() ) {
        cb_cra_bail( 'rate' );
    }

    $token = isset( $_POST['g-recaptcha-response'] )
        ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) )
        : '';

    if ( ! cb_cra_verify_recaptcha( $token ) ) {
        cb_cra_bail( 'captcha' );
    }

    $data   = cb_cra_clean_contact( json_decode( wp_unslash( $_POST['data'] ?? '' ), true ) );
    $scores = cb_cra_clean_scores( json_decode( wp_unslash( $_POST['scores'] ?? '' ), true ) );

    if ( null === $data ) {
        cb_cra_bail( 'invalid' );
    }

    if ( $ip ) {
        set_transient( $rate_key, $recent + 1, HOUR_IN_SECONDS );
    }

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

    $results = get_permalink( $post_id );

    cb_cra_send_results_email( $data, $results );

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
        'Bcc: enquiries@afiniti.co.uk',
    );

    wp_mail( $data['contactEmail'], $subject, $message, $headers );
}
