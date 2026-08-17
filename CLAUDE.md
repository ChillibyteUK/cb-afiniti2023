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
- Cache busting is via `Version:` in `style.css` (currently **0.4**, bumped from
  0.3 which had never changed since the theme was created). Bump it whenever
  shipping CSS/JS, or returning visitors keep the stale files.

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

## In-flight work: CRA tool hardening

### Done

`inc/cb-cra-submit.php` (new) holds the whole validated submission pipeline.
`cra.php` is now a 10-line shim delegating to it.

The old `cra.php` was a bare web-callable file with no `ABSPATH` guard that read
`$_REQUEST` and created a published post plus an email with no validation. A
plain `GET` was enough to mint a submission - which is where results with no
company name came from - and it let anyone send mail apparently from
`enquiries@afiniti.co.uk` to any address. Now closed: POST only, honeypot,
reCAPTCHA verification, per-IP rate limiting, `is_email()`, required `orgName`
and `contactName`, sanitised fields, scores clamped to known levers.

`js/cra.js`: failed validation now calls `stopImmediatePropagation()` so the
template's submit handler cannot fire anyway (it used to submit regardless, with
empty hidden fields). Email is validated with `checkValidity()` and values are
trimmed, so `abc` and `" "` no longer pass.

Settings live under **Site-Wide Settings > CRA Tool**: reCAPTCHA site key,
secret key, minimum score, submissions per hour per IP. `cra_tool_page_id` moved
into that tab. A matching constant (`CB_RECAPTCHA_SECRET` etc.) overrides the
field if defined.

### Parked

`page-templates/cra-tool.php` has been **rolled back** to the earlier version.
The phase 1 template work is preserved as
`page-templates/cra-tool-working.php`, registered as "CRA Tool (working)".

> **Do not set the reCAPTCHA secret while the rolled-back template is live.**
> That template does not send a `g-recaptcha-response` token, so the moment a
> secret exists every submission is rejected with `?cra_error=captcha` - and the
> rolled-back template does not render that message either, so users just land
> back at the top of the tool. Either finish the working template first, or
> leave the secret blank. With no secret the check fails open and logs
> `CRA: no reCAPTCHA secret set (...)` per submission.

The secret is **not** in the codebase or the database - only the public site key
(`6LeKUsAp…`) ever was, because the old code never verified anything. Get it
from the Google reCAPTCHA console, or register a fresh v3 pair and paste both.

### Not started

- Phase 2 of `cra-tool.php`: steps 3/4/5 are the same ~60 line block three
  times, 92 lines of inline `<style>` to move to SCSS, `question_header` fetched
  three times, inconsistent escaping between steps.
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
| `js/cra.js` | step validation; hand-edited, **not** compiled from `src/` |
| `page-templates/cra-tool.php` | live template, on the pre-hardening version |
| `page-templates/cra-tool-working.php` | parked phase 1 template, "CRA Tool (working)" |
| `acf-json/group_63c67dca8bc3c.json` | Site-Wide Settings, incl. the CRA Tool tab |

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
  attack requests (GET, no orgName, bad email, honeypot, bogus captcha token)
  after each change - that is how the misfire cause was confirmed rather than
  guessed at.
