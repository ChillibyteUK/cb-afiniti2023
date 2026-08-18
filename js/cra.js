/*
 * CRA tool stepper.
 *
 * Hand-edited - NOT compiled from src/. See CLAUDE.md.
 *
 * This walks whatever the template rendered rather than counting to five. The
 * previous version hard-coded #form0..#form5, #step0..#step5, #step1back..
 * #step3back, UpdateScores(3|4|5) and checkAllRadioGroups('form3'...), so
 * adding a step meant editing this file. The question steps are now global
 * content and there can be any number of them, so every count comes from the
 * DOM:
 *
 *   [data-cra-step]              a step section, in document order
 *   [data-cra-kind]              org | questions | contact
 *   [data-cra-field="key"]       an input whose value goes into the payload
 *   [data-cra-required]          must be filled before leaving the step
 *   [data-cra-warn-for="key"]    that field's error message
 *   [data-cra-warn-step]         the step-level error message
 *   [data-cra-shown-when="k=v"]  shown only while field k has value v
 *   [data-lever]                 on a radio: which lever its score belongs to
 *
 * Contact details are the LAST step and gate the report. Scores are summed from
 * checked radios across every question step, so moving a question between steps
 * does not change the result.
 */

(function () {
    'use strict';

    var steps = [].slice.call(document.querySelectorAll('[data-cra-step]'));

    if (!steps.length) {
        // Not the CRA tool template, or the tool rendered no steps.
        return;
    }

    var intro = document.getElementById('form0');
    var current = -1;

    /*
     * The CB CRA Hero block renders two heroes: the tall intro one and a compact
     * form one. Starting the tool swaps them. Both are flex, not block - setting
     * 'block' here silently broke their internal layout.
     */
    function showFormHero(showing) {
        var primary = document.querySelector('.cra-hero--primary');
        var compact = document.getElementById('cra-form-hero');

        if (primary) {
            primary.style.display = showing ? 'none' : 'flex';
        }

        if (compact) {
            compact.style.display = showing ? 'flex' : 'none';
        }
    }

    // ------------------------------------------------------------- utilities

    function fieldsIn(step) {
        return [].slice.call(step.querySelectorAll('[data-cra-field]'));
    }

    function warnFor(step, key) {
        return step.querySelector('[data-cra-warn-for="' + key + '"]');
    }

    function show(el, on) {
        if (el) {
            el.style.display = on ? 'block' : 'none';
        }
    }

    /*
     * Bootstrap utilities are !important, so d-flex would beat an inline
     * style.display and the element could never be hidden. Sections are shown
     * and hidden here only, never with a utility class. See CLAUDE.md.
     */
    function hideAllSteps() {
        steps.forEach(function (step) {
            step.style.display = 'none';
        });

        current = -1;
    }

    function showIntro() {
        if (intro) {
            intro.style.display = 'block';
        }

        showFormHero(false);
    }

    function showStep(index) {
        steps.forEach(function (step, i) {
            step.style.display = i === index ? 'block' : 'none';
        });

        current = index;

        if (intro) {
            intro.style.display = 'none';
        }

        showFormHero(true);

        /*
         * No scrollIntoView on step change. The compact form hero is short
         * enough that the next step is already in view, and jumping the page
         * was more disorienting than helpful.
         */
    }

    // ------------------------------------------------------------ validation

    function isFilled(input) {
        if (input.type === 'checkbox') {
            return input.checked;
        }

        var value = input.value.trim();

        // Trim in place, so a single space does not count as filled in.
        if (value !== input.value) {
            input.value = value;
        }

        if (value === '') {
            return false;
        }

        /*
         * These inputs are not inside a <form>, so `required` and type="email"
         * never fire natively. checkValidity() works on a detached input, which
         * is what stops "abc" being accepted as an email address.
         */
        return typeof input.checkValidity !== 'function' || input.checkValidity();
    }

    function validateFields(step) {
        var ok = true;

        fieldsIn(step).forEach(function (input) {
            if (!input.hasAttribute('data-cra-required')) {
                return;
            }

            // A conditionally hidden field is not required while it is hidden.
            if (input.closest('[data-cra-shown-when]') &&
                input.closest('[data-cra-shown-when]').style.display === 'none') {
                show(warnFor(step, input.getAttribute('data-cra-field')), false);
                return;
            }

            var filled = isFilled(input);

            show(warnFor(step, input.getAttribute('data-cra-field')), !filled);

            if (!filled) {
                ok = false;
            }
        });

        return ok;
    }

    function validateQuestions(step) {
        var groups = {};

        [].slice.call(step.querySelectorAll('input[type="radio"][name]')).forEach(function (radio) {
            if (!(radio.name in groups)) {
                groups[radio.name] = false;
            }

            if (radio.checked) {
                groups[radio.name] = true;
            }
        });

        return Object.keys(groups).every(function (name) {
            return groups[name];
        });
    }

    function validate(step) {
        var kind = step.getAttribute('data-cra-kind');
        var ok = kind === 'questions' ? validateQuestions(step) : validateFields(step);

        show(step.querySelector('[data-cra-warn-step]'), !ok);

        return ok;
    }

    // --------------------------------------------------------------- payload

    function collectData() {
        var data = {};

        steps.forEach(function (step) {
            fieldsIn(step).forEach(function (input) {
                var key = input.getAttribute('data-cra-field');

                if (input.type === 'checkbox') {
                    data[key] = input.checked ? 'true' : '';
                } else {
                    data[key] = input.value.trim();
                }
            });
        });

        return data;
    }

    function collectScores() {
        var scores = {};

        steps.forEach(function (step) {
            [].slice.call(step.querySelectorAll('input[type="radio"][data-lever]')).forEach(function (radio) {
                var lever = radio.getAttribute('data-lever');

                if (!(lever in scores)) {
                    scores[lever] = 0;
                }

                if (radio.checked) {
                    scores[lever] += parseInt(radio.value, 10) || 0;
                }
            });
        });

        return scores;
    }

    // ---------------------------------------------------------------- wiring

    steps.forEach(function (step, index) {
        var next = step.querySelector('[data-cra-next]');
        var back = step.querySelector('[data-cra-back]');

        if (next) {
            next.addEventListener('click', function (e) {
                e.preventDefault();

                if (!validate(step)) {
                    return;
                }

                if (index + 1 < steps.length) {
                    showStep(index + 1);
                }
            });
        }

        if (back) {
            back.addEventListener('click', function (e) {
                e.preventDefault();

                if (index > 0) {
                    showStep(index - 1);
                }
            });
        }
    });

    // Conditional blocks, e.g. "outline this change" only when the answer is Yes.
    [].slice.call(document.querySelectorAll('[data-cra-shown-when]')).forEach(function (block) {
        var parts = block.getAttribute('data-cra-shown-when').split('=');
        var source = document.querySelector('[data-cra-field="' + parts[0] + '"]');

        if (!source) {
            return;
        }

        var sync = function () {
            block.style.display = source.value === parts[1] ? 'grid' : 'none';
        };

        source.addEventListener('change', sync);
        sync();
    });

    // Start buttons. The hero renders #step0; other buttons use .start.
    var starters = [].slice.call(document.querySelectorAll('.start'));
    var step0 = document.getElementById('step0');

    if (step0 && starters.indexOf(step0) === -1) {
        starters.push(step0);
    }

    starters.forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            showStep(0);
        });
    });

    // .reset buttons abandon the assessment and go back to the intro.
    [].slice.call(document.querySelectorAll('.reset')).forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            hideAllSteps();
            showIntro();
        });
    });

    // ---------------------------------------------------------------- submit

    var submit = document.getElementById('craSubmit');

    if (submit) {
        submit.addEventListener('click', function (e) {
            var step = steps[steps.length - 1];

            if (!validate(step)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            /*
             * Every step, not just this one. The submit button sits inside the
             * last section, which is in the DOM the whole time - so validating
             * only the contact step would let an incomplete question step through
             * and post a payload of zero scores. Walking all of them means the
             * only way to submit is to have genuinely finished.
             */
            for (var i = 0; i < steps.length; i++) {
                if (!validate(steps[i])) {
                    showStep(i);
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return;
                }
            }

            var data = collectData();

            /*
             * Last line of defence: never post a payload the server will only
             * reject. orgName is captured on the first step, so a broken jump
             * between steps would otherwise submit without it.
             */
            if (!data.contactEmail || !data.orgName || !data.contactName) {
                var warn = step.querySelector('[data-cra-warn-step]');

                if (warn) {
                    warn.textContent = 'Some details are missing. Please check the earlier steps.';
                    warn.style.display = 'block';
                }

                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            document.getElementById('data').value = JSON.stringify(data);
            document.getElementById('scores').value = JSON.stringify(collectScores());
        });
    }

    /*
     * .stepCard is display:none in CSS, and #form0 is a .stepCard too, so the
     * intro has to be shown explicitly - it does not appear on its own.
     */
    hideAllSteps();
    showIntro();
}());
