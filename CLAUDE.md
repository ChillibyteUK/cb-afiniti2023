# cb-afiniti2023

Understrap child theme for afiniti.co.uk. Bootstrap 5, ACF blocks, ACF local JSON
in `acf-json/`.

## Build

```bash
npm run css     # src/sass -> css/child-theme{,.min}.css
npm run js      # src/js  -> js/child-theme{,.min}.js
npm run dist    # both
```

Never edit `css/*.css` or `js/child-theme*.js` directly - they are compiled.
`js/cra.js` and `js/slick.min.js` are NOT compiled; edit them in place.

Blocks live in `blocks/`, registered in `inc/cb-blocks.php` at the
`// INSERT NEW BLOCKS HERE.` marker. `add_block.sh` scaffolds a new one.

## Environments

- Local: `afiniti.local`. A one-page crash/burn copy, **not** a mirror of
  production - never infer production scope from a local DB query.
- Production: WP Engine, heavy sticky full-page caching. Moving to Kinsta soon.
- Cache busting is via `Version:` in `style.css` (currently **0.7**). Bump it
  whenever shipping CSS/JS, or returning visitors keep the stale files.
  `js/cra.js` is the exception - it is not compiled and not covered by that
  version, so it carries its own `?v=` in `cra-tool.php`. Bump that too.

## Before the next production deploy

**1. Tick the cbp-blog-options toggles on production.** Comment and tag
disabling was deleted from this theme (`inc/cb-utility.php`,
`inc/cb-theme.php`) because the `cbp-blog-options` plugin does it all, and more.
But its switches are conditional. Locally `disable_tags` was **off**, so removing
the theme code brought tags straight back until it was ticked. Confirm both
**Disable comments** and **Disable tags** are on under Settings > Blog Options on
production, or deploying re-enables both. The theme's Chillibyte dashboard widget
was removed too - the plugin registers the same `cb_dashboard_widget` id
unconditionally, so that one needs nothing.

**2. Eyeball the colour change.** Reconciling `theme.json` with `--col-*` moved
every palette colour, because the two lists had drifted. Green `#accf83` ->
`#87bd70` (darker, more saturated), grey `#474747` -> `#616062` (lighter).
Anything using core colour classes shifts; ACF block backgrounds do not, so the
effect is core-styled content converging on what blocks already showed. Worth
checking a few page types before it goes out.

**3. Check the live CRA tool page.** It was found on `page-template-default`
having lost its CRA Tool template assignment, which meant no form and no
`cra.js` at all - the reported "buttons don't work". Re-select **CRA Tool** under
Page > Template if it happens again. That page's content also contains a
hand-written `<button id="step0">`, which is a duplicate of the one the hero
block renders; `getElementById` only returns the first, so the in-content one is
dead. It should be `<button class="start btn btn--orange">` - `cra.js` binds all
`.start` buttons, and other buttons on that page already use it.

This recurred locally on 2026-08-18: the template had been left unassigned, and
the symptom was exactly "none of the start buttons work". Assigning the template
fixed it. Template assignment is the first thing to check for that report.

**4. Purge the page cache after deploying the CRA changes.** Submissions now
require a `cra_token` field that only exists in freshly rendered HTML. Anyone
served a cached copy of the tool page from before the deploy posts without one
and gets bounced with `?cra_error=expired`. One purge of that page clears it;
without a purge it resolves itself only as the cache expires.

## In-flight work: CRA tool hardening

### Done

`inc/cb-cra-submit.php` (new) holds the whole validated submission pipeline.
`cra.php` is now a 10-line shim delegating to it.

The old `cra.php` was a bare web-callable file with no `ABSPATH` guard that read
`$_REQUEST` and created a published post plus an email with no validation. A
plain `GET` was enough to mint a submission - which is where results with no
company name came from - and it let anyone send mail apparently from
`enquiries@afiniti.co.uk` to any address. Now closed: POST only, honeypot,
signed form token, per-IP and site-wide rate limiting, `is_email()`, required
`orgName` and `contactName`, sanitised fields, scores clamped to known levers.

`js/cra.js`: failed validation now calls `stopImmediatePropagation()` so the
template's submit handler cannot fire anyway (it used to submit regardless, with
empty hidden fields). Email is validated with `checkValidity()` and values are
trimmed, so `abc` and `" "` no longer pass.

Settings live under **Site-Wide Settings > CRA Tool**: notification email,
submissions per hour per IP, submissions per hour site wide. `cra_tool_page_id`
moved into that tab. A matching constant (`CB_CRA_RATE_LIMIT` etc.) overrides
the field if defined.

### reCAPTCHA was removed, deliberately

There is no captcha. The v3 that was here never worked anyway - both templates
fetched a token and submitted without ever posting it - and finishing it was
judged not worth it: a probabilistic score needing a blind threshold, where one
silently rejected real enquiry costs more than a hundred junk rows, plus a Google
script on the page and the consent question that brings. **Do not re-add it
without discussing it first.**

What replaced it, all in `inc/cb-cra-submit.php`:

- `cb_cra_form_token()` - HMAC of a day-long time window, three windows accepted,
  so a token is good for 48-72h. Deliberately **not** a nonce: a nonce is per
  user and would go stale behind the host's full page cache, which is the exact
  failure mode this file exists to prevent. Same value for every visitor in a
  window, so cached HTML stays valid. Posted as `cra_token`; rejection code is
  `?cra_error=expired`.
- Rate limits: 3/hour/IP, and a site-wide ceiling of 30/hour as a circuit
  breaker, since per-IP limiting cannot see a distributed flood. Tripping the
  ceiling logs `CRA: global hourly submission ceiling hit`.
- The visitor email and the internal notification are now **two** `wp_mail()`
  calls, not one with a `Bcc`, so inbox volume can be throttled independently of
  what a submitter can trigger.

The thing being protected is `wp_mail()`, not the `cra` post type: a submission
makes the server send mail From `afiniti.co.uk` to an address the submitter
chose, so unbounded it is a mail-bomb amplifier and a route to the sending
domain being blocklisted. The controls are sized for that.

Honest about the bar: anyone can fetch the page and scrape the token. Nothing
short of a captcha stops that and we are choosing not to have one. It stops the
case that actually happens - a script posting a fixed payload straight at
`admin-post.php`, having never loaded the form - and expires stale replays.

### Superseded by the restructure

`cra-tool.php` was briefly rolled back to a pre-hardening version, with the phase
1 work parked in `cra-tool-working.php`. Both of those are gone: the template was
rewritten in the restructure below, and the parked copy was deleted.

What survived from that work: the `cra_token` field, the `?cra_error=` notice, and
`cb_cra_bail()` preferring `wp_get_referer()`. Step 1's **Back** link had been
hard-coded to `/change-readiness-assessment-tool/`, which is not the live page's
slug; it is `get_permalink()` now.

## CRA restructure

Agreed 2026-08-18. Developed on `cra-block-rewrite`, fast-forwarded onto `main`
the same day - **WP Pusher tracks `main`**, so nothing reaches an environment
until it is there and pushed. Work on a branch by all means, but merge to `main`
before expecting a deploy to pick it up. The old "phase 2" - deduplicating steps 3/4/5 in
`cra-tool.php` - is **superseded**: those blocks are being deleted, so there is
no point refactoring them.

### The shape agreed

- **Questions are global**, on a single options page: a steps repeater, each step
  holding a questions sub-repeater (`question` + `lever`). Arbitrary numbers of
  steps and questions, no code change to add either.
- **Analysis bands are global**, on the `lever` taxonomy. Done, see below.
- **A page using the CRA Tool template automatically renders the test.** The page
  content is then just slide 1's content, so several pages can present different
  intros over the same global questions - that is what "different versions of the
  test" means here.
- **Contact details move to the last step**, required before the report is
  created or viewed. Currently step 1, which means the tool asks for an email
  before showing any value. `orgName` is the exception - it moves *up* into
  "About Your Organisation", because the organisation name is needed early (it
  becomes `post_title`).

  The resulting order, agreed 2026-08-18:

  | step | fields |
  |---|---|
  | slide 1 | page content, no fields |
  | About Your Organisation | `orgName`, `changeInProgress`, `changeDetail`, `changeRole` |
  | question steps | from the global options page, any number |
  | Contact Details (last) | `contactName`, `contactTitle`, `contactPhone`, `contactMobile`, `contactEmail`, `contactHowHear`, `consent` |

  The `data`/`scores` server contract does **not** change -
  `cb_cra_clean_contact()` still requires `orgName`, `contactName` and a valid
  email, and all eleven keys are still collected. What changes is when. `cra.js`
  gates progression on contact validity at step 1 today; that gate moves to the
  end, and the "last line of defence" check before submit becomes the primary
  one. `orgNameWarn` moves with its field.

  These two steps stay **code-defined**, not author-editable: their fields map to
  payload keys the server validates by name, so making them editable would break
  the contract for no gain. Only the question steps are content.
- **Scale stays 1-10 globally.** One setting, not per step or per question.

An earlier plan to make each question an ACF block was dropped: questions being
global means blocks would duplicate them per page. It also avoids being the first
block in this theme to need `'jsx' => true` and three-level `InnerBlocks`
nesting, which was the riskiest unknown.

Consequence worth knowing: `cra_tool_page_id` can be retired. It exists to locate
the analysis fields (no longer needed) and as a `cb_cra_bail()` fallback
(superseded by the referer), and a single page ID is wrong once several pages run
the tool.

### Phase 1: levers own their own content - DONE

`inc/cb-cra-levers.php` (new) is the single source of truth. It splits the job:

- `CB_CRA_LEVER_MAP` owns the canonical **order** and the **storage key**. Fixed
  at six, because it is the client's trademarked methodology, not a growing list.
- The `lever` taxonomy owns the **label** and the **analysis copy**.

Storage keys are the capitalised names every existing `cra` post already uses, so
**no historical result data moved**. Slugs equal `strtolower( key )`, which was
also the prefix of the old `{slug}_analysis` fields, so the join was exact.

`bin/migrate-lever-analysis.php` copies the six `{slug}_analysis` repeaters from
the tool page onto the matching terms. Idempotent, non-destructive (the page
fields are read, never deleted), and it prints a 0-100 coverage check per lever.
Arguments are **positional**, not flags - `wp eval-file` rejects `--`:

```bash
wp eval-file bin/migrate-lever-analysis.php dry-run
```

`cb_cra_lever_bands()` falls back to the page fields when a term has no bands, so
an unmigrated environment still renders and rolling back means doing nothing.
That fallback is also why "it still works" is not proof the migration worked -
see the sentinel test below.

`single-cra.php` now reads levers, labels and bands through those helpers, uses
the editable term **label** for display and chart labels, and matches insights by
term **slug** rather than name so rewording a lever cannot silently break the
Related Insights query. `CB_CRA_LEVERS` is gone from `inc/cb-cra-submit.php`;
`cb_cra_clean_scores()` validates against `cb_cra_lever_keys()`.

### Phase 2: questions are global - DONE (data model only)

**Site-Wide Settings > CRA Questions** (`acf_add_options_sub_page`, slug
`cra-questions`) holds a `cra_steps` repeater: `step_title`, `step_header`, and a
nested `questions` repeater of `question` + `lever`. Any number of steps, any
number of questions per step.

Two deliberate differences from the old fields:

- `step_header` is **per step**. It was one `question_header` shared by all three,
  fetched three times. The migration copies the same text into each, so nothing
  changes visually until someone edits one.
- `lever` is an ACF **taxonomy** field storing a term id, not a select with six
  hard-coded choices. That was the last of the six hard-codings.

`cb_cra_question_steps()` normalises whichever source is live into
`[ title, header, questions[ id, text, lever_slug, lever_key ] ]`, and falls back
to the legacy `questions_page_1/2/3` repeaters when the options page is empty -
same principle as `cb_cra_lever_bands()`. Question ids (`q1`…`qN`) are assigned
over the flattened set, so they are stable for a configuration and independent of
which step a question sits in. A question whose lever does not resolve is
dropped, not scored against nothing.

`cb_cra_lever_maxima()` derives the per-lever maximum from the live question set
(`CB_CRA_SCALE_MAX` is a constant, 10 - fixed, not a setting, because every
stored result was scored on it). Against the production question set it returns
30 for all six, **exactly matching the hard-coded `CB_CRA_MAX_LEVER_SCORE`** -
which is what makes switching to the derived value provably a no-op today.

`bin/migrate-cra-questions.php` moves the questions. Positional args as above,
plus `force`. Refuses to run over a populated options page. Reports unresolved
levers, and warns if the migration leaves a lever with no questions or with
unequal weight.

> **The front end still reads the legacy repeaters.** `cra-tool.php` has not been
> touched, so the tool renders exactly as before - phase 2 is the data model and
> the migration only. Wiring the template to `cb_cra_question_steps()` is phase 3.
> This is why the legacy field definitions must stay for now.

### Phase 3: data-driven template and JS - DONE

`cra-tool.php` builds its steps from `cb_cra_question_steps()`. The order is now
organisation → question steps → contact, as agreed. Progress percentages are
computed from the step count, and headings are numbered from the loop.

`js/cra.js` was rewritten to be **markup-driven**. No step or question count
appears in it at all; it walks whatever the template rendered:

| attribute | meaning |
|---|---|
| `data-cra-step` | a step section, in document order |
| `data-cra-kind` | `org` / `questions` / `contact` |
| `data-cra-field="key"` | an input whose value goes into the payload |
| `data-cra-required` | must be filled before leaving the step |
| `data-cra-warn-for="key"` | that field's error message |
| `data-cra-warn-step` | the step-level error message |
| `data-cra-shown-when="k=v"` | shown only while field k has value v |
| `data-lever` | on a radio: which lever its score belongs to |

Scores are summed from checked radios across every question step, so **moving a
question between steps does not change the result**. Verified: answering step 1
with 2s, step 2 with 3s and step 3 with 4s gave `Method: 8` and `Capability: 10`
where the others gave 9, which is right - the production question set puts two
Method questions in step 2 and two Capability in step 3.

Three things the rewrite nearly lost, all found by driving the real thing:

- **`.stepCard` is `display:none` in CSS, and `#form0` is a `.stepCard` too**, so
  the intro does not appear unless JS shows it explicitly.
- **The CB CRA Hero block renders two heroes** and starting the tool swaps them
  (`.cra-hero--primary` ↔ `#cra-form-hero`). Both are `flex`; setting `block`
  silently breaks their internal layout.
- **`.reset` buttons** return to the intro and restore the primary hero.

The submit handler validates **every** step, not just the contact one. The submit
button lives inside the last section, which is in the DOM the whole time, so
validating only that step let an incomplete question step through and posted a
payload of all-zero scores. Found by a faulty test that accidentally checked no
radios - worth keeping in mind as a real class of bug rather than a test artefact.

`cra.js` is cache-busted **only** by its own `?v=` query string in
`cra-tool.php` - it is not compiled, so nothing else busts it. Bump it on every
change. It is at `2.3`. A browser test against a stale bundle is worthless; fetch
the asset with `cache: 'no-store'` and grep it if behaviour contradicts the file.

Step changes no longer `scrollIntoView`. The compact hero is short enough that
the next step is already in view, and the jump was disorienting.

### The legacy field group is detached, not deleted

`group_6494183e38c8d.json` ("TPL CRA Tool") is now attached to
`post_type == cb_cra_legacy_retained`, a post type that does not exist. That
removes the metabox from CRA Tool pages while keeping the fields **registered**,
because location rules only control display - `get_field()` still reads them, and
`cb_cra_question_steps()` / `cb_cra_lever_bands()` still fall back to them on any
environment that has not run the migrations. **Production has not been migrated**,
so deleting the group outright would take its questions with it. Delete it, and
the fallbacks, only after production is migrated and verified.

### Phase 4: tidying - DONE except the final deletion

**Inline styles moved to SCSS.** The 92-line `<style>` block is now
`src/sass/theme/_cra_tool.scss`, imported from `_child_theme.scss`. Everything is
scoped under `body.page-template-cra-tool`, which is **not cosmetic**: the block
contained `.alert-danger { display: none; }`, and as a global rule that would hide
every Bootstrap danger alert on the site, including the `?cra_error=` notice this
template renders. The inline block got away with it by only ever loading on one
page. The `:root { --col-light-400: #fafafa }` override became a scoped
`--cb-cra-panel-bg` for the same reason. One rule was dropped as dead:
`.form-panel input[type="radio"]::before` used a hyphen where the markup has
`.form_panel`, so it never matched anything.

Theme `Version:` is **0.5** for that CSS (now **0.6**, see the step indicator below).

**`cra_tool_page_id` is retired as a dependency.** Everything now resolves through
`cb_cra_tool_page_id()`: the setting if present, otherwise a page on the CRA Tool
template. The setting was empty as often as not and every consumer failed quietly
when it was - bails landed on the home page, the results page found no bands. It
is also meaningless once several pages run the tool. The field still exists as an
explicit override; nothing depends on it. Verified by emptying it and confirming
the page still resolved.

**`cra-tool-working.php` deleted.** It still had the old `#form1`..`#form5`
markup, which the rewritten `cra.js` ignores entirely - the JS returns early when
it finds no `[data-cra-step]`, so selecting "CRA Tool (working)" gave a page whose
buttons did nothing. No page was assigned to it.

### Running the migrations: Tools > CRA Migration

**WP Engine has no wp-cli**, so the migrations cannot be shell scripts. The logic
lives in `inc/cb-cra-migrate.php` as `cb_cra_migrate_analysis()` and
`cb_cra_migrate_questions()`; **Tools > CRA Migration** runs them, and
`bin/migrate-*.php` are thin wrappers over the same functions for environments
that do have wp-cli. One implementation, two front ends - do not reimplement
either in the scripts.

The page shows current state before you touch anything: which page is the source,
how many legacy rows it holds, whether the options page and each lever's bands are
populated, the 0-100 coverage per lever, and the derived max score. Dry run is
ticked by default; Overwrite is not.

> **Several pages run this template in production, and that matters.** The
> migration reads from **one** page and writes to **global** storage, so
> afterwards every template page renders the same questions. If those pages
> currently differ, one set wins and the others stop being used.
>
> The page therefore lists every template page with a fingerprint of its
> questions and bands. Matching fingerprints mean the choice is arbitrary;
> differing fingerprints raise a red warning and the source has to be picked
> explicitly. Nothing is deleted either way - the other pages keep their fields
> and the change is reversible - but which content becomes global is an editorial
> decision, not a technical one. **Do not pick for the client.**

### Editable intro on the contact step

`cra_contact_intro` on the **CRA Questions** options page (wysiwyg, alongside the
`cra_steps` repeater) renders above the contact fields on the final step, in the
same `.alert-light` panel the question steps use for their own introduction.

Empty renders nothing. The emptiness test strips tags, decodes entities and then
trims `" \t\n\r\0\x0B\xc2\xa0"` - note the last one. Clearing a wysiwyg
usually leaves `<p>&nbsp;</p>`, and the decoded `&nbsp;` is U+00A0, which PHP's
default `trim()` does **not** strip. Without that byte pair in the list the check
passes and an empty grey panel renders. Verified against `<p>&nbsp;</p>`, `<p></p>`,
`""` and whitespace.

### Numbered step indicator

The Bootstrap progress bar is gone, replaced by a numbered `<ol class="cra_steps">`
- markers, labels and a connecting trail that fills in behind you. Styles live in
`_cra_tool.scss` with the rest of the tool.

Rendered per step section and **entirely server side**: each section knows its own
index, so `is-done` / `is-current` / `is-upcoming` is static markup and the JS was
not touched. `aria-current="step"` marks the current one, and done/current
markers carry a visually-hidden note so the state is not colour-only.

The step `<h2>` and the indicator labels say the same thing, so they swap at
576px: below it the labels are visually hidden and the `<h2>` names the step;
above it the `<h2>` is visually hidden and the labels do. Both use
`@include visually-hidden` rather than `display:none`, so either way the step is
still named for assistive tech. The `<h2>` rule is scoped to `[data-cra-step] > h2`
deliberately - `#form0` is a `.stepCard` too and holds the page's own intro
content, headings included.

Completed steps are not clickable. Could be, but it needs thought about what
jumping backwards does to validation, so it was left out.

**Still to do:** delete `group_6494183e38c8d.json` and the legacy fallbacks in
`cb_cra_question_steps()` / `cb_cra_lever_bands()`, once production has been
migrated and verified. Nothing else is outstanding.

### The denominator, still outstanding

`single-cra.php` divides by `CB_CRA_MAX_LEVER_SCORE` (30), which is only correct
because there are exactly three questions per lever across the three question
pages, each scored 1-10. The moment the number of questions per lever is
editable, 30 is wrong - and wrong retroactively, misreading every historical
result.

The fix is to store the denominator **with the submission** (`{lever: max}`,
computed at submit time) and have the results page use the stored value, falling
back to 30 when absent. Old results keep rendering exactly as they do now; new
ones become self-describing. This is the one irreversible-ish data decision in
the restructure, so it lands late and deliberately. **A migration script for
legacy results is not needed** as long as the fallback is kept - that was checked
against the fixtures below.

### Verification fixtures (dev only)

Six `cra` posts, slugs `fixture-zero`, `fixture-all-low`, `fixture-all-high`,
`fixture-mixed`, `fixture-boundary`, `fixture-legacy-title`. Scores chosen so
percentages land on the real band edges (`raw 12 -> 40%`, `18 -> 60%`,
`27 -> 90%`), and one carries the legacy `Company | email` title to exercise the
scrubbing filters. The seed script is in the session scratchpad, not the repo.

How phase 1 was proved, which is the pattern to repeat:

1. Render all six fixtures to HTML **before** the change.
2. Migrate, wire the template up, render again, diff. Every fixture was
   byte-identical apart from one comment line deliberately added.
3. Byte-identical is *not* enough on its own, because the page-field fallback
   could have been doing all the work. So: patch a band's text in term meta to a
   sentinel, confirm it appears in the rendered page, then restore and re-diff.
   That is what actually proves the new source is live.
4. Re-run the migration to confirm it skips rather than duplicating.
5. Re-run the CRA attack matrix, since `cb_cra_clean_scores()` changed. Confirmed
   `40 -> 30`, `-5 -> 0`, and an unknown `Bogus` lever dropped.

### Not started

- Anchor review across all blocks - every anchor currently lands under the fixed
  navbar. `:root:has(.nav_buttons)` sets `scroll-padding-top` only on pages
  using the nav buttons block; the general case is unsolved.
- Scoped fix for the block container quirk (see below). The ask was explicitly
  **scoped, not "properly"** - hundreds of live pages rely on the current
  behaviour. Goal: let `alignwide`/`alignfull` work on a group containing
  columns, and stop core blocks inside columns getting their own container.
  Everything outside a columns/aligned-group context must render unchanged.

### Open questions I raised and never got an answer to

None are blocking; all are one-line changes. Ask rather than assume.

- **CB Nav Buttons** uses `btn btn--purple`. A guess - there was no design.
- **Sticky nav buttons** pin at 68px, below the navbar, set via
  `--cb-navbar-height` in `_cb_nav_buttons.scss`. Now unambiguous since the
  navbar no longer hides on scroll.
- **Links on coloured panels** stay white on hover, because the rule outranks
  Bootstrap's `a:hover`. Bootstrap's underline is the only affordance left.
- **`mailto:`/`tel:` links** with `target="_blank"` still get the off-site icon.
  Defensible (they are not on the domain) but possibly not wanted.
- **CB CRA Hero renders `#step0` anywhere.** Put the block on a page without the
  CRA Tool template and you get a dead button - exactly the reported symptom. It
  could render a link to the tool page instead when the template is not active.
- **`single-cra.php` Related Insights now shows 6 cards, not 3.** The third
  query was dead (`$remaining` computed backwards) so it had only ever shown 3.
  Set `$max_cards = 3` to revert.
- **Historic CRA data** may be worth auditing: how many results have the
  `CRA Result` fallback title or an unusable email, from before validation
  existed. Run that against production, not locally.

## Block editor styles

`inc/cb-editor.php` calls `add_editor_style()` with the compiled frontend
stylesheet **and** the editor-only one, in that order:

```php
add_editor_style( array( 'css/child-theme.min.css', 'css/custom-editor-style.min.css' ) );
```

`src/sass/custom-editor-style.scss` has existed since the theme was created and
`npm run css` has always compiled it to `css/custom-editor-style{,.min}.css` - but
**nothing ever called `add_editor_style()`**, so none of it had ever loaded. Ported
from `cb-global42026`, which does the same thing.

Order matters: the frontend stylesheet defines the `--col-*` properties on
`:root`, and the editor stylesheet's colour rules need them resolved in the same
document. `add_theme_support( 'editor-styles' )` is added in the same function -
without it `add_editor_style()` enqueues nothing and the whole thing is a silent
no-op.

The width rules at the end of `custom-editor-style.scss` contain top-level blocks
to a page-width centred column. On the frontend `inc/cb-blocks.php` wraps core
blocks in `.container-xl` and ACF blocks render their own container; neither
happens in the editor, so without this every block runs full-bleed. The widths
come from Bootstrap's own `$container-max-widths` map (1140px from xl, 1320px from
xxl) rather than a hand-picked number, so the editor tracks `.container-xl`.

Two things to know if you touch it:

- Not scoped to `.is-root-container > .wp-block`. The post title sits in a sibling
  section above the root container, so the stricter selector would leave the title
  full-bleed while the content was constrained.
- **This install renders the editor un-iframed**, because meta boxes are present.
  In that mode WordPress prefixes editor-stylesheet selectors with
  `.editor-styles-wrapper` itself, so an already-prefixed selector could in
  principle end up doubled and never match. It does match here - verified by
  reading the computed style, `max-width: 1320px` with auto margins - but check
  the computed value rather than the source if these rules ever appear to stop
  working.

### Fullscreen mode, and what the plugin already covers

`cb_editor_windowed_by_default()` stops the block editor opening fullscreen.
Ported from `cb-global42026` but **rewritten**, for two reasons:

- That version used `jQuery( window ).load()` (removed in jQuery 3, only still
  working here because jQuery Migrate patches it) and `core/edit-post`'s
  `isFeatureActive()` / `toggleFeature()` (deprecated in favour of
  `core/preferences`). Checked against this install - jQuery 3.7.1, WP 7.0.4 -
  before changing.
- It toggled fullscreen off on **every load**, so anyone who deliberately turned
  fullscreen on found it off again next time and could never make it stick. This
  uses `setDefaults()`, which a persisted user preference still overrides. Verified
  by writing `fullscreenMode: true` into `wp_persisted_preferences` and confirming
  it survived a reload. The docblock has the one-liner if a hard override is ever
  wanted instead.

**The ACF Visual/Text focus workaround from that file was deliberately not
ported.** `cbp-blog-options` already fixes that, and better: it forces
`delay: true` on WYSIWYG fields so TinyMCE never initialises until clicked, and
guards scroll position across ACF DOM mutations. The theme version monkey-patched
`switchEditors.go`, which would have been a second competing fix for the same
problem. Check the plugin before porting anything else editor-related from a
sibling theme - it also forces ACF blocks into edit mode, and handles
comments/tags/emoji site-wide.

## Gotchas worth knowing

**Bootstrap utilities are `!important`.** `d-flex` is `display:flex!important`,
so neither a stylesheet rule nor an inline `style.display` can hide such an
element. Bit twice on the CRA hero. Set display in CSS instead of using `d-flex`
on anything JS toggles.

**Colour palette single source.** `theme.json` defines the seven palette
colours; `_props.scss` aliases `--col-*` to `var(--wp--preset--color--*)`. They
used to be two independent lists that had drifted apart on all six colours.
`$col-*` Sass variables must stay literal for `lighten()` and have to be kept in
step by hand. `inc/cb-posttypes.php` mirrors theme.json into
`add_theme_support()` because ACF's Editor Palette field reads
`get_theme_support()` directly, and because WP strips palette entries whose slug
collides with a core default (which is what happened to `white`).

**Do not "fix" the global styles removal.** `inc/cb-theme.php` calls
`remove_action('wp_enqueue_scripts','wp_enqueue_global_styles')`, which is a
no-op (WP also registers it on `wp_footer`). It must stay a no-op: `--col-*`
aliases `--wp--preset--color--*`, which global styles define.

**Core block containers.** `inc/cb-blocks.php` wraps every core paragraph,
heading, list and columns block in `.container-xl` with no depth check, so a
paragraph inside a column gets its own container. Hundreds of live pages depend
on this quirk, so any fix must be scoped rather than removed. `render_block_data`
fires for inner blocks with a `$parent_block` reference, and `render_callback`
receives the `WP_Block` as its third argument - that is the hook for a scoped fix.

**CRA results pages carry PII.** `single-cra.php` used to dump the whole
submission into an HTML comment, and `post_title` was `"Company | email"`, which
Yoast put in `og:title` and the JSON-LD graph. Titles are now company-only,
`show_in_rest` is off for the `cra` post type, and filters in
`inc/cb-posttypes.php` scrub legacy titles. Keep emails out of `post_title`.

## Local test fixtures (dev only)

- CRA result post id 20, `Test Org Ltd | test.person@example.com` - deliberately
  the legacy title format, to exercise the scrubbing filters.
- Posts `Engagement insight A/B`, `Drivers insight C`, `Generic insight D/E/F`
  for the Related Insights logic.
- Two analysis score bands seeded on each of page 5's six `*_analysis`
  repeaters, and `cra_tool_page_id` set to 5. Both were empty beforehand.

## Where the CRA submission code lives

| file | role |
|---|---|
| `inc/cb-cra-submit.php` | the whole validated pipeline; `admin_post_{,nopriv_}cb_cra_submit` |
| `cra.php` | thin shim into the above, kept for visitors on cached HTML |
| `js/cra.js` | markup-driven stepper; hand-edited, **not** compiled from `src/` |
| `page-templates/cra-tool.php` | the template; renders steps from the question set |
| `inc/cb-cra-levers.php` | levers, question steps, maxima, tool page resolver |
| `src/sass/theme/_cra_tool.scss` | tool styles, scoped to `body.page-template-cra-tool` |
| `inc/cb-cra-migrate.php` | the migrations, and `Tools > CRA Migration` that runs them |
| `bin/migrate-*.php` | wp-cli wrappers over the same functions; WPE has no wp-cli |
| `acf-json/group_cra_questions.json` | the global question set |
| `acf-json/group_cra_lever_analysis.json` | per-lever analysis bands, on the taxonomy |
| `acf-json/group_63c67dca8bc3c.json` | Site-Wide Settings, incl. the CRA Tool tab |
| `acf-json/group_6494183e38c8d.json` | legacy fields, detached from the edit screen |

## Verification habits that paid off here

The theme has no test suite, so changes get checked by driving the real thing.
Several bugs in this work were only caught this way, and two of my own
conclusions were wrong until I tested them properly:

- Compile SCSS/JS and grep the **compiled** output to confirm a rule shipped.
- `curl` a page and diff before/after, and watch `wp-content/debug.log` for new
  warnings on that request only.
- For anything cascade or JS related, read computed styles in a browser rather
  than reasoning from source. I twice concluded something worked or was broken
  from the source alone and was wrong both times - once on `position: sticky`
  (bad test: the probe was below max scroll), once on `.scrolled` (the page had
  not finished loading when the test ran).
- Browser tests can silently read a **cached** bundle. If behaviour contradicts
  the file on disk, fetch the asset with `cache: 'no-store'` and compare.
- For the CRA endpoint, seed a fixture with `wp eval` and re-run the actual
  attack requests (GET, no token, bogus token, honeypot, no orgName, bad email,
  then four valid posts to trip the per-IP limit)
  after each change - that is how the misfire cause was confirmed rather than
  guessed at.
