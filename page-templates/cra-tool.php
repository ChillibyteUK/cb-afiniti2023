<?php
/**
 * Template Name: CRA Tool
 *
 * Template for displaying the CRA tool.
 *
 * @package cb-afiniti2023
 */

defined( 'ABSPATH' ) || exit;

get_header();

?>
<main id="main">
    <?php
    /*
     * A rejected submission comes back here with ?cra_error=. Without this the
     * visitor lost every step of input and landed back at the top with no
     * explanation.
     */
    $cra_errors = array(
        'invalid' => 'We could not read your contact details. Please check your name, organisation and email address and try again.',
        'expired' => 'This page had been open too long and your session expired. Please reload the page and try again.',
        'rate'    => 'Too many submissions from your connection. Please try again later.',
        'save'    => 'Something went wrong saving your results. Please try again, or contact us if it keeps happening.',
        'method'  => 'That link cannot be opened directly. Please start the assessment from the beginning.',
    );

    $cra_error = isset( $_GET['cra_error'] ) ? sanitize_key( wp_unslash( $_GET['cra_error'] ) ) : '';

    if ( $cra_error && isset( $cra_errors[ $cra_error ] ) ) {
        ?>
    <div class="container-xl">
        <div class="alert alert-danger d-block my-4" role="alert">
            <?= esc_html( $cra_errors[ $cra_error ] ); ?>
        </div>
    </div>
        <?php
    }

    /*
     * The CRA Hero block renders both the intro hero and the compact form
     * hero, so it is lifted out of the page content: the intro section below
     * is hidden once the assessment starts, and the form hero has to survive
     * that. Everything else stays in the intro, rendered through the normal
     * the_content filter chain.
     */
    $cra_hero_blocks  = array();
    $cra_intro_blocks = array();

    foreach ( parse_blocks( get_the_content() ) as $cra_block ) {
        if ( 'acf/cb-cra-hero' === ( $cra_block['blockName'] ?? '' ) ) {
            $cra_hero_blocks[] = $cra_block;
        } else {
            $cra_intro_blocks[] = $cra_block;
        }
    }

    if ( $cra_hero_blocks ) {
        echo apply_filters( 'the_content', serialize_blocks( $cra_hero_blocks ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } else {
        // Fallback for CRA pages that have not had the hero block added yet.
        ?>
    <section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
        <div class="hero__inner container-xl text-center">
            <h1 class="mb-3"><?= wp_kses_post( get_the_title() ); ?></h1>
            <div class="hero__cta">
                <button id="step0" class="btn btn-lg btn--orange">Get Started</button>
            </div>
        </div>
    </section>
        <?php
    }
    ?>
    <section class="stepCard" id="form0">
        <?= apply_filters( 'the_content', serialize_blocks( $cra_intro_blocks ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </section>
    <div class="container-xl">
        <?php
        /*
         * The steps are built here rather than hard-coded, because the question
         * steps come from Site-Wide Settings > CRA Questions and there can be any
         * number of them. Fixed steps top and tail the sequence:
         *
         *   0            About Your Organisation  (orgName + the change context)
         *   1 .. n-2     question steps           (global, editable)
         *   n-1          Contact Details          (last - gates the report)
         *
         * Contact details deliberately come last: the tool used to ask for an
         * email before showing any value. orgName is the exception and stays up
         * front, because it becomes the result's post_title.
         *
         * Both fixed steps stay code-defined. Their inputs map to payload keys
         * cb_cra_clean_contact() validates by name, so they are not content.
         *
         * The markup carries everything cra.js needs - data-cra-step,
         * data-cra-kind, data-cra-field, data-lever - so the JS walks whatever is
         * rendered instead of counting to five. No step or question count appears
         * in the JS at all.
         */
        $cra_question_steps = cb_cra_question_steps( get_the_ID() );

        /*
         * Titles for every step, in order, so the numbered indicator can label
         * each one. The count comes from this rather than an arithmetic guess.
         */
        $cra_step_titles = array( 'About Your Organisation' );

        foreach ( $cra_question_steps as $cra_qstep ) {
            $cra_step_titles[] = $cra_qstep['title'];
        }

        $cra_step_titles[] = 'Your Details';

        $cra_step = 0;

        /**
         * Opens a step section, with its heading and the numbered step indicator.
         *
         * The indicator replaced a Bootstrap progress bar. It is rendered per
         * section and entirely server side: each section knows its own index, so
         * done / current / upcoming is static markup and needs no JS.
         *
         * @param int    $index Zero-based step index.
         * @param string $kind  org|questions|contact.
         * @return void
         */
        $cra_open_step = function ( $index, $kind ) use ( $cra_step_titles ) {
            $title = $cra_step_titles[ $index ] ?? '';
            ?>
        <section class="stepCard" id="craStep<?= (int) $index; ?>" data-cra-step="<?= (int) $index; ?>"
            data-cra-kind="<?= esc_attr( $kind ); ?>">
            <h2>Step <?= (int) $index + 1; ?> - <?= esc_html( $title ); ?></h2>
            <?php // aria-label rather than a heading, so the list is announced as navigation for the assessment. ?>
            <ol class="cra_steps" aria-label="Assessment progress">
                <?php
                foreach ( $cra_step_titles as $i => $step_title ) {
                    if ( $i < $index ) {
                        $state = 'is-done';
                        $note  = 'completed';
                    } elseif ( $i === $index ) {
                        $state = 'is-current';
                        $note  = 'current step';
                    } else {
                        $state = 'is-upcoming';
                        $note  = '';
                    }
                    ?>
                <li class="cra_steps__step <?= esc_attr( $state ); ?>"
                    <?= 'is-current' === $state ? ' aria-current="step"' : ''; ?>>
                    <span class="cra_steps__marker" aria-hidden="true"><?= (int) $i + 1; ?></span>
                    <span class="cra_steps__label">
                        <?= esc_html( $step_title ); ?>
                        <?php if ( $note ) { ?>
                        <span class="visually-hidden">(<?= esc_html( $note ); ?>)</span>
                        <?php } ?>
                    </span>
                </li>
                    <?php
                }
                ?>
            </ol>
            <?php
        };

        /**
         * Renders the Back / Next pair. Step 0's Back leaves the assessment.
         *
         * @param int    $index Zero-based step index.
         * @param string $next  Label for the forward button.
         * @return void
         */
        $cra_step_buttons = function ( $index, $next = 'Next' ) {
            ?>
            <div class="form_buttons d-flex gap-2 justify-content-between">
                <?php if ( 0 === $index ) { ?>
                <a href="<?= esc_url( get_permalink() ); ?>" class="btn btn-secondary">Back</a>
                <?php } else { ?>
                <button type="button" class="btn btn-secondary" data-cra-back>Back</button>
                <?php } ?>
                <button type="button" class="btn btn-primary" data-cra-next><?= esc_html( $next ); ?></button>
            </div>
            <?php
        };

        // ------------------------------------------------ step 1: organisation
        $cra_open_step( $cra_step, 'org' );
        ?>
            <div class="form_panel">
                <div class="form_grid mb-3">
                    <label for="orgName">Organisation Name<sup>*</sup></label>
                    <div>
                        <input type="text" name="orgName" id="orgName" class="form-control" required
                            data-cra-field="orgName" data-cra-required="1">
                        <div class="alert alert-danger" data-cra-warn-for="orgName">Please enter your organisation name
                        </div>
                    </div>
                </div>
                <div class="form_grid mb-3">
                    <label for="changeInProgress">Is your organisation currently implementing or planning a major change
                        initiative?</label>
                    <div>
                        <select name="changeInProgress" id="changeInProgress" class="form-select"
                            data-cra-field="changeInProgress" data-cra-required="1">
                            <option value="" disabled selected>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                        <div class="alert alert-danger" data-cra-warn-for="changeInProgress">Please select an option.
                        </div>
                    </div>
                </div>
                <?php // Shown only when the answer above is Yes - see cra.js. ?>
                <div id="changeDetailContainer" class="form_grid my-3" data-cra-shown-when="changeInProgress=Yes">
                    <label for="changeDetail">Please briefly outline this change</label>
                    <div>
                        <textarea name="changeDetail" id="changeDetail" class="form-control" data-cra-field="changeDetail"
                            data-cra-required="1"
                            placeholder="For example, implementing a new technology or system, creating a new operating model, digital transformation, culture change, regulatory changes."></textarea>
                        <div class="alert alert-danger" data-cra-warn-for="changeDetail">Please tell us about your
                            current/planned change.</div>
                    </div>
                </div>
                <div class="form_grid">
                    <label for="changeRole">What, if any role do you normally undertake in relation to a Change
                        Project/Programme?</label>
                    <div>
                        <select name="changeRole" id="changeRole" class="form-select" data-cra-field="changeRole"
                            data-cra-required="1">
                            <option value="" disabled selected>Select</option>
                            <option value="None">None</option>
                            <option value="End User">End User</option>
                            <option value="Project/Programme Manager">Project/Programme Manager</option>
                            <option value="Sponsor">Sponsor</option>
                            <option value="Stakeholder">Stakeholder</option>
                        </select>
                        <div class="alert alert-danger" data-cra-warn-for="changeRole">Please select an option.</div>
                    </div>
                </div>
                <?php $cra_step_buttons( $cra_step ); ?>
            </div>
        </section>
        <?php
        ++$cra_step;

        // -------------------------------------------------- the question steps
        foreach ( $cra_question_steps as $cra_qstep ) {
            $cra_open_step( $cra_step, 'questions' );
            ?>
            <div class="form_panel">
                <?php if ( '' !== trim( (string) $cra_qstep['header'] ) ) { ?>
                <div class="alert alert-light">
                    <?= wp_kses_post( $cra_qstep['header'] ); ?>
                </div>
                <?php } ?>
                <div class="form_grid form_grid--wide">
                    <div class="d-none d-md-block">&nbsp;</div>
                    <div class="justify-content-between d-none d-md-flex">
                        <div>Strongly<br>Disagree</div>
                        <div>Strongly<br>Agree</div>
                    </div>
                    <?php
                    /*
                     * The 1-10 scale labels, repeated for mobile under each
                     * question. Built once here rather than inlined per question.
                     */
                    ob_start();
                    ?>
                    <div class="d-md-none d-flex justify-content-between small">
                        <div>Strongly Disagree</div>
                        <div>Strongly Agree</div>
                    </div>
                    <?php
                    $cra_mob_labels = ob_get_clean();

                    foreach ( $cra_qstep['questions'] as $cra_question ) {
                        $cra_group = 'cra_' . $cra_question['id'];
                        ?>
                    <label for="<?= esc_attr( $cra_group ); ?>"><?= esc_html( $cra_question['text'] ); ?></label>
                    <?= $cra_mob_labels; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <div class="radio_group" role="radiogroup"
                        aria-label="<?= esc_attr( $cra_question['text'] ); ?>">
                        <?php for ( $cra_i = 1; $cra_i <= CB_CRA_SCALE_MAX; $cra_i++ ) { ?>
                        <input type="radio" name="<?= esc_attr( $cra_group ); ?>"
                            data-lever="<?= esc_attr( $cra_question['lever_key'] ); ?>" value="<?= (int) $cra_i; ?>"
                            class="form-check" aria-label="<?= (int) $cra_i; ?>">
                        <?php } ?>
                    </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="alert alert-danger mt-4" data-cra-warn-step>Please answer all questions.</div>
                <?php $cra_step_buttons( $cra_step ); ?>
            </div>
        </section>
            <?php
            ++$cra_step;
        }

        // --------------------------------------------- last step: the contact
        $cra_open_step( $cra_step, 'contact' );
        ?>
            <div class="form_panel">
                <div class="form_grid">
                    <label for="contactName">Name<sup>*</sup></label>
                    <div>
                        <input type="text" name="contactName" id="contactName" class="form-control" required
                            data-cra-field="contactName" data-cra-required="1">
                        <div class="alert alert-danger" data-cra-warn-for="contactName">Please enter your name</div>
                    </div>
                    <label for="contactTitle">Job Title</label>
                    <input type="text" name="contactTitle" id="contactTitle" class="form-control"
                        data-cra-field="contactTitle">
                    <label for="contactPhone">Contact Number</label>
                    <input type="text" name="contactPhone" id="contactPhone" class="form-control"
                        data-cra-field="contactPhone">
                    <label for="contactMobile">Contact Mobile</label>
                    <input type="text" name="contactMobile" id="contactMobile" class="form-control"
                        data-cra-field="contactMobile">
                    <label for="contactEmail">Contact Email<sup>*</sup></label>
                    <div>
                        <input type="email" name="contactEmail" id="contactEmail" class="form-control" required
                            data-cra-field="contactEmail" data-cra-required="1">
                        <div class="alert alert-danger" data-cra-warn-for="contactEmail">Please enter your email address
                        </div>
                    </div>
                    <label for="contactHowHear">How did you hear about Afiniti?<sup>*</sup></label>
                    <div>
                        <select name="contactHowHear" id="contactHowHear" class="form-select" required
                            data-cra-field="contactHowHear" data-cra-required="1">
                            <option value="" disabled selected>Select</option>
                            <option value="Web Search">Web Search</option>
                            <option value="LinkedIn">LinkedIn</option>
                            <option value="Email">Email</option>
                            <option value="Existing Client">Existing Client</option>
                            <option value="External Referral">External Referral</option>
                            <option value="Internal Referral">Internal Referral</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="alert alert-danger" data-cra-warn-for="contactHowHear">Please select an option</div>
                    </div>
                    <div>
                        <label for="consent"><input type="checkbox" name="consent" id="consent" value="true"
                                data-cra-field="consent" data-cra-required="1">
                            <div>I consent to the terms of the <a href="/privacy-policy/" target="_blank">privacy
                                    policy</a><sup>*</sup>.</div>
                        </label>
                        <div class="alert alert-danger" data-cra-warn-for="consent">Please consent to the terms.</div>
                    </div>
                </div>
                <div class="alert alert-danger mt-4" data-cra-warn-step>Please complete the required fields.</div>
                <div class="form_buttons d-flex gap-2 justify-content-between">
                    <button type="button" class="btn btn-secondary" data-cra-back>Back</button>
                    <?php
                    /*
                     * The submit lives in the form so the button is a real submit
                     * - cra.js fills the hidden fields on click and
                     * preventDefault()s if anything is incomplete.
                     */
                    ?>
                    <form action="<?= esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="craForm">
                        <input type="hidden" name="action" value="cb_cra_submit">
                        <input type="hidden" name="data" id="data" value="">
                        <input type="hidden" name="scores" id="scores" value="">
                        <input type="hidden" name="pageID" id="pageID" value="<?= esc_attr( get_the_ID() ); ?>">
                        <input type="hidden" name="cra_token" value="<?= esc_attr( cb_cra_form_token() ); ?>">
                        <input type="submit" id="craSubmit" class="btn btn-primary" value="View Results">
                        <input class="ohnohoney" autocomplete="off" type="email" id="emailaddress" name="emailaddress"
                            placeholder="Your e-mail here">
                    </form>
                </div>
            </div>
        </section>
        <?php
        ++$cra_step;
        ?>
    </div>
</main>
<?php
add_action('wp_footer', function () {
    ?>
<script>

    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type == 2)) {
          document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
            radio.checked = false;
            });
        }
    });

</script>
<?php
/*
 * The reCAPTCHA v3 handler that used to live in the script above has been
 * removed. It cancelled the native submit, fetched a token and then submitted
 * without ever posting the token, so it verified nothing while still loading a
 * Google script on every view. #step5 is a plain submit button again: cra.js
 * fills the hidden fields on click and preventDefault()s when a step does not
 * validate. A PHP comment, so it does not ship to the browser.
 */
?>
<?php // Bumped from 1.1: cra.js was rewritten to be markup-driven. Bump on every change - the file is not compiled, so nothing else busts its cache. ?>
<script src="<?=get_stylesheet_directory_uri()?>/js/cra.js?v=2.3"></script>
<?php
});
get_footer();
?>