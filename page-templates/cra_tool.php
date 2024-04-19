<?php
/**
 * Template Name: CRA Tool
 *
 * Template for displaying the CRA tool.
 *
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

?>
<style>
    :root {
        --col-light-400: #fafafa;
    }

    .stepCard {
        padding-bottom: 3rem;
        display: none;
    }

    .form_grid {
        display: grid;
        gap: 1rem;

        @media (min-width:768px) {
            grid-template-columns: 1fr 2fr;
        }
    }

    .form_grid--wide {
        @media (min-width:768px) {
            grid-template-columns: 2fr 1fr;
        }
    }

    .form_panel {
        background-color: var(--col-light-400);
        padding: 1rem;
    }

    .form_buttons {
        margin-block: 1rem;
    }

    .alert-danger {
        display: none;
        padding: 0.5rem;
        margin-top: 0.25rem;
        margin-bottom: 0;
    }

    #changeDetailContainer {
        display: none;
    }

    .radio_group {
        display: grid;
        gap: 0.25rem;
        grid-template-columns: repeat(10, 1fr);
        height: 1.5rem;
    }

    .radio_group>div {
        text-align: center;
    }

    label[for="consent"] {
        display: flex;
        gap: 0.5rem;
        align-items: flex-start;
    }

    label[for="consent"] input[type="checkbox"] {
        width: 1.5rem;
        height: 1.5rem;
    }

    .form-panel input[type="radio"]::before {
        content: "1";

    }

    .mobLabels {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
    }

    .mobLabels div {
        text-align: center;
    }

    .ohnohoney {
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        height: 0;
        width: 0;
        z-index: -1;
    }
</style>
<main id="main">
    <section id="hero" class="hero d-flex align-items-start pt-lg-0 align-items-lg-center">
        <div class="hero__inner container-xl text-center">
            <h1><span>Change Readiness</span> Assessment Tool</h1>
        </div>
    </section>
    <div class="container-xl">
        <section class="stepCard" id="form0">
            <?php
        the_content();
?>
        </section>
        <section class="stepCard" id="form1">
            <h2>Step 1 - Contact Details</h2>
            <div class="progress">
                <div class="progress-bar progress-bar-striped" role="progressbar" style="width:0%" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="form_panel">
                <div class="form_grid">
                    <label for="contactName">Name<sup>*</sup></label>
                    <div>
                        <input type="text" name="contactName" id="contactName" class="form-control" required>
                        <div class="alert alert-danger" id="contactNameWarn">Please enter your name</div>
                    </div>
                    <label for="contactTitle">Job Title</label>
                    <input type="text" name="contactTitle" id="contactTitle" class="form-control">
                    <label for="orgName">Organisation Name<sup>*</sup></label>
                    <div>
                        <input type="text" name="orgName" id="orgName" class="form-control" required>
                        <div class="alert alert-danger" id="orgNameWarn">Please enter your organisation name</div>
                    </div>
                    <label for="contactPhone">Contact Number</label>
                    <input type="text" name="contactPhone" id="contactPhone" class="form-control">
                    <label for="contactMobile">Contact Mobile</label>
                    <input type="text" name="contactMobile" id="contactMobile" class="form-control">
                    <label for="contactEmail">Contact Email<sup>*</sup></label>
                    <div>
                        <input type="email" name="contactEmail" id="contactEmail" class="form-control" required>
                        <div class="alert alert-danger" id="contactEmailWarn">Please enter your email address</div>
                    </div>
                    <div>&nbsp;</div>
                    <div>
                        <label for="consent"><input type="checkbox" name="consent" id="consent" value="true">
                            <div>I consent to the terms of the <a href="/privacy-policy/" target="_blank">privacy
                                    policy</a><sup>*</sup>.</div>
                        </label>
                        <div class="alert alert-danger" id="consentWarn">Please consent to the terms.</div>
                    </div>
                </div>
                <div class="form_buttons text-end">
                    <button id="step1" class="btn btn-primary">Next</button>
                </div>
            </div>
        </section>
        <section class="stepCard" id="form2">
            <h2>Step 2 - About Your Organisation</h2>
            <div class="progress">
                <div class="progress-bar progress-bar-striped" role="progressbar" style="width:20%" aria-valuenow="20"
                    aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="form_panel">
                <div class="form_grid mb-3">
                    <label for="changeInProgress">Is your organisation currently implementing or planning a major change
                        initiative?</label>
                    <div>
                        <select name="changeInProgress" id="changeInProgress" class="form-select">
                            <option value="" disabled selected>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                        <div class="alert alert-danger" id="changeInProgressWarn">Please select an option.</div>
                    </div>
                </div>
                <div id="changeDetailContainer" class="form_grid my-3">
                    <label for="changeDetail">Please briefly outline this change</label>
                    <div>
                        <textarea name="changeDetail" id="changeDetail" class="form-control" placeholder="For example, implementing a new technology or system, creating a new operating model, digital transformation, culture change, regulatory changes.
"></textarea>
                        <div class="alert alert-danger" id="changeDetailWarn">Please tell us about your current/planned
                            change.</div>
                    </div>
                </div>
                <div class="form_grid">
                    <label for="changeRole">What, if any role do you normally undertake in relation to a Change
                        Project/Programme?</label>
                    <div>
                        <select name="changeRole" id="changeRole" class="form-select">
                            <option value="" disabled selected>Select</option>
                            <option value="None">None</option>
                            <option value="End User">End User</option>
                            <option value="Project/Programme Manager">Project/Programme Manager</option>
                            <option value="Sponsor">Sponsor</option>
                            <option value="Stakeholder">Stakeholder</option>
                        </select>
                        <div class="alert alert-danger" id="changeRoleWarn">Please select an option.</div>
                    </div>
                </div>
                <div class="form_buttons text-end">
                    <button id="step2" class="btn btn-primary">Next</button>
                </div>
            </div>
        </section>
        <section class="stepCard" id="form3">
            <h2>Step 3 - Making Change Stick</h2>
            <div class="progress">
                <div class="progress-bar progress-bar-striped" role="progressbar" style="width:40%" aria-valuenow="40"
                    aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="form_panel">
                <div class="alert alert-light">
                    <?=get_field('question_header')?>
                </div>
                <div class="form_grid form_grid--wide">
                    <div class="d-none d-md-block">&nbsp;</div>
                    <div class="justify-content-between d-none d-md-flex">
                        <div>Strongly<br>Disagree</div>
                        <div>Strongly<br>Agree</div>
                    </div>
                    <div class="d-none d-md-block">&nbsp;</div>
                    <div class="radio_group radio_group--labels d-none d-md-grid">
                        <?php
            for ($i = 1; $i <= 10; $i++) {
                echo '<div>' . $i . '</div>';
            }
?>
                    </div>
                    <?php

                $mobLabels = '<div class="d-md-none mobLabels"><div>Strongly<br>Disagree</div><div>Strongly<br>Agree</div></div>';

$q = 1;
while (have_rows('questions_page_1')) {
    the_row();
    ?>
                    <label
                        for="form3_answers"><?=get_sub_field('question')?><!-- [<?=get_sub_field('lever')?>]
                        --></label>
                    <?=$mobLabels?>
                    <div class="radio_group">
                        <?php
        for ($i = 1; $i <= 10; $i++) {
            ?>
                        <input type="radio"
                            name="form3_answers_<?=$q?>"
                            data-lever="<?=get_sub_field('lever')?>"
                            value="<?=$i?>" class="form-check">
                        <?php
        }
    ?>
                    </div>
                    <?php
                    $q++;
}
?>
                </div>
                <div class="alert alert-danger mt-4" id="form3Warn">Please answer all questions.</div>
                <div class="form_buttons text-end">
                    <button id="step3" class="btn btn-primary">Next</button>
                </div>
            </div>
        </section>
        <section class="stepCard" id="form4">
            <h2>Step 4 - Making Change Stick</h2>
            <div class="progress">
                <div class="progress-bar progress-bar-striped" role="progressbar" style="width:60%" aria-valuenow="60"
                    aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="form_panel">
                <div class="alert alert-light">
                    <?=get_field('question_header')?>
                </div>
                <div class="form_grid form_grid--wide">
                    <div class="d-none d-md-block">&nbsp;</div>
                    <div class="justify-content-between d-none d-md-flex">
                        <div>Strongly<br>Disagree</div>
                        <div>Strongly<br>Agree</div>
                    </div>
                    <div class="d-none d-md-block">&nbsp;</div>
                    <div class="radio_group radio_group--labels d-none d-md-grid">
                        <?php
    for ($i = 1; $i <= 10; $i++) {
        echo '<div>' . $i . '</div>';
    }
?>
                    </div>
                    <?php
                $q = 1;
while (have_rows('questions_page_2')) {
    the_row();
    ?>
                    <label
                        for="form4_answers"><?=get_sub_field('question')?><!-- [<?=get_sub_field('lever')?>]
                        --></label>
                    <?=$mobLabels?>
                    <div class="radio_group">
                        <?php
        for ($i = 1; $i <= 10; $i++) {
            ?>
                        <input type="radio"
                            name="form4_answers_<?=$q?>"
                            data-lever="<?=get_sub_field('lever')?>"
                            value="<?=$i?>" class="form-check">
                        <?php
        }
    ?>
                    </div>
                    <?php
                    $q++;
}
?>
                </div>
                <div class="alert alert-danger mt-4" id="form4Warn">Please answer all questions.</div>
                <div class="form_buttons text-end">
                    <button id="step4" class="btn btn-primary">Next</button>
                </div>
            </div>
        </section>
        <section class="stepCard" id="form5">
            <h2>Step 5 - Making Change Stick</h2>
            <div class="progress">
                <div class="progress-bar progress-bar-striped" role="progressbar" style="width:80%" aria-valuenow="80"
                    aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="form_panel">
                <div class="alert alert-light">
                    <?=get_field('question_header')?>
                </div>
                <div class="form_grid form_grid--wide">
                    <div>&nbsp;</div>
                    <div class="d-none d-md-flex justify-content-between">
                        <div>Strongly<br>Disagree</div>
                        <div>Strongly<br>Agree</div>
                    </div>
                    <div>&nbsp;</div>
                    <div class="radio_group radio_group--labels d-none d-md-grid">
                        <?php
    for ($i = 1; $i <= 10; $i++) {
        echo '<div>' . $i . '</div>';
    }
?>
                    </div>
                    <?php
                $q = 1;
while (have_rows('questions_page_3')) {
    the_row();
    ?>
                    <label
                        for="form5_answers"><?=get_sub_field('question')?><!-- [<?=get_sub_field('lever')?>]
                        --></label>
                    <?=$mobLabels?>
                    <div class="radio_group">
                        <?php
        for ($i = 1; $i <= 10; $i++) {
            ?>
                        <input type="radio"
                            name="form5_answers_<?=$q?>"
                            data-lever="<?=get_sub_field('lever')?>"
                            value="<?=$i?>" class="form-check">
                        <?php
        }
    ?>
                    </div>
                    <?php
                    $q++;
}
?>
                </div>
                <div class="alert alert-danger mt-4" id="form5Warn">Please answer all questions.</div>
                <div class="form_buttons text-end">
                    <form
                        action="<?=get_stylesheet_directory_uri()?>/cra.php"
                        method="post" id="craForm">
                        <input type="hidden" name="data" id="data" value="">
                        <input type="hidden" name="scores" id="scores" value="">
                        <input type="hidden" name="pageID" id="pageID"
                            value="<?=get_the_ID()?>">
                        <input type="submit" id="step5" class="btn btn-primary" value="View Results">
                        <input class="ohnohoney" autocomplete="off" type="email" id="emailaddress" name="emailaddress"
                            placeholder="Your e-mail here">
                    </form>
                </div>
            </div>
        </section>

    </div>
</main>
<?php
add_action('wp_footer', function () {
    ?>
<script src="https://www.google.com/recaptcha/api.js?render=6LeKUsApAAAAAD9wCXHTKx5BaujLUJVE8BdMQlLY"></script>
<script>
    function onClick(e) {
        console.log('submitting');
        e.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('6LeKUsApAAAAAD9wCXHTKx5BaujLUJVE8BdMQlLY', {
                action: 'submit'
            }).then(function(token) {
                document.getElementById("craForm").submit();
            });
        });
    }
</script>
<script src="<?=get_stylesheet_directory_uri()?>/js/cra.js"></script>
<?php
});
get_footer();
?>