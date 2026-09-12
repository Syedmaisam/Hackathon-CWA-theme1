# MILESTONE — who has what, what is done, what is next

Team: **Maisam** and **Sumair**. Last verified against a full
`php artisan migrate:fresh --seed` on **12 September 2026**.

**Status: the app is demoable end to end right now.** Both lanes have landed and merged.
Everything below marked "next" is improvement on a working build, not a gap in it.

## One-command check

```
php artisan migrate:fresh --seed
composer run dev
```

`DatabaseSeeder` now calls both lane seeders, so this is the only command anyone needs.
Citizen flow is at `/`, admin is at `/admin` (`admin@cityaround.pk` / `password`).

Current seeded state:

| | count |
|---|---|
| Demo reports | 14 |
| Gazetteer nodes | 123 |
| Authorities | 41 |
| Routing rules | 13 |

---

## Done and verified

### Shared foundation

- Schema for all four tables, plus models. Schema is frozen; additive changes only.
- Routing pipeline on `App\Models\Report`: resolve location, apply special-zone override,
  apply routing rule, score confidence, plus a no-AI template draft fallback.
- Two DeepSeek agents in `app/Ai/Agents/`, spiked against the live API.
- Inertia and React scaffold, teal accent, Instrument Sans plus Noto Nastaliq Urdu.
- `DatabaseSeeder` calls `SumairSeeder` then `MaisamSeeder`, in that order. The order
  matters: Maisam's reports reference Sumair's authorities and gazetteer nodes.

### Maisam — citizen-facing flow

- `Maisam\ReportController` with create, store, show, clarify and confirm-category.
  Pipeline runs inline on submit, no queue.
- `routes/maisam.php`, five routes.
- `Pages/Maisam/Report/Create.jsx` — textarea, photo drop zone, submit, processing stepper.
- `Pages/Maisam/Report/Show.jsx` — routing explanation, English and Urdu draft tabs,
  copy / WhatsApp / mailto, clarifying-question form, AI-failed category picker.
- `MaisamSeeder` — 14 demo reports. Every visual state is represented and confirmed
  present: 11 drafted, 1 awaiting answer, 1 needs review, 1 AI failed.

### Voice notes — built by Sumair, inside Maisam's lane

**Maisam: read this before you touch `Create.jsx`.** Sumair edited one file in your lane
while you were away, with Sumair's explicit go-ahead, because the brief asks for "photo,
voice note or typed description" and voice was the one of the three that was missing.

- **New file, Sumair's:** `resources/js/Components/VoiceNoteInput.jsx`. Self-contained.
- **Your files, presentation only:** `Pages/Maisam/Report/Create.jsx`, `Show.jsx` and
  `Layouts/AppLayout.jsx`. Voice wiring on Create, and a mobile pass across all three.
  No logic, no data flow and no props were changed on any of them.
- **No backend change at all.** No PHP was touched, no migration, no controller edit. Your
  `store()` already validated `input_mode` against `text,voice,photo` and passed it
  through, and the column already allowed it. Nothing was sending it until now.

The approach is the browser Web Speech API, which is the option the original plan document
picked for this exact risk. It needs no key and no provider. DeepSeek has no speech-to-text
and only `DEEPSEEK_API_KEY` is set, so the transcription-provider route was blocked anyway.

The transcript is appended into the same `raw_text` a typed report uses, so every pipeline
stage runs unchanged and the AI still detects `input_language` from the text. Verified: an
Urdu voice note resolves to Lyari and routes to the waste board, scoring high confidence.
Your `Show.jsx` already renders a voice chip that could never appear before; it can now.

**Built for phone and desktop both.** The mobile pass covered more than the recorder:

- **Recognition restarts itself.** Mobile browsers end a session at every natural pause
  regardless of `continuous`, so without this the recorder stops mid-sentence. A flag
  distinguishes a pause from a real stop, and ordinary silence no longer counts as an error.
- **Touch targets** on the record button and language chips meet the 44px minimum.
- **No iOS zoom-on-focus.** The contact inputs were 14px, and iOS Safari zooms any focused
  input under 16px, which jerks the page mid-form. Both are 16px now.
- **Header and page gutters** tighten on small screens, and the redundant tagline hides.
- **Photo copy** no longer says "drag and drop", which means nothing on a phone. The input
  already offered the camera on mobile; only the wording was desktop-only.

Then a UI pass over the report page, which is the screen a judge reads longest:

- **Authority contacts stack and are tappable.** They were a dot-separated run that
  overflowed a phone. Phone numbers are now `tel:` links and emails `mailto:`, which is
  the actual point of this product on a phone.
- **A real empty state for missing contacts.** 23 of the 41 seeded authorities publish no
  phone or email, so this is the common path, not an edge case. It used to render a silent
  blank gap that read as a broken page. It now says so and points at the escalation route.
- **The draft sits on its own surface** with wrapping that survives long Urdu lines, and
  WhatsApp is first and full-width on mobile because that is how this actually gets sent.
- **Clarify and category forms stack** instead of forcing an input and button into one
  non-wrapping row, and their inputs are 16px so iOS does not zoom.
- **Urdu renders right-to-left** in the report text and the clarify input, matching the
  draft pane. The Nastaliq font was already wired to `[dir='rtl']` and now actually applies.

Then the compose screen at `/` was rebuilt as a **WhatsApp-style chat**, because a stack of
grey boxes on white does not read as something you talk to:

- **Full-height chat layout.** `AppLayout` takes a `bare` prop so this one page manages its
  own height and the composer pins to the bottom of the viewport. The report page passes
  nothing and is completely unaffected.
- **The prompt is an incoming bubble**, your report is an outgoing accent bubble, and the
  pipeline wait is a typing indicator whose caption advances through the three stages.
- **Composer bar**: paperclip, auto-growing input, round send button. Enter sends on
  desktop, Shift+Enter makes a newline, and the phone keyboard's Enter still makes a
  newline as people expect.
- **Voice is inline now**, a small mic pill beside the language toggle rather than a
  full-width block. Same component, new `compact` variant.
- **Photo previews as a sent image bubble** with a remove button, instead of a dashed
  desktop drop zone.
- **Four tappable starter prompts** so the empty state suggests what to say.
- Safe-area padding at the bottom so the composer clears the iPhone home indicator.

Verified by serving the app and loading all four report states, then submitting a real
report through the endpoint: it created a voice-mode report, resolved it to Lyari and
drafted at high confidence. The test rows were deleted, so the database is back to the
seeded 14.

Caveat for the demo: Web Speech is Chrome and Edge, including Android Chrome. iOS Safari
does not support it. The component then tells the citizen to use their keyboard's own
microphone key, which dictates into the same box and reaches the same pipeline. The
textarea always works, so a failed mic costs nothing but the voice flourish.

### Sumair — reference data and admin

- **Filament forms fixed.** The routing rule form was broken: it omitted `issue_type`,
  the primary key, so no rule could be created at all. The authority form had the same
  defect with its `id` slug. Both fixed and creation verified end to end. JSON array
  columns now use multi-selects and a tags input instead of plain text boxes, across
  all four resources.
- **DHA City routing bug found and fixed.** A DHA City report was resolving to
  Cantonment Board Clifton with high confidence. The source document warns about exactly
  this: DHA City is a separate scheme and must flag for human review. Cause was that the
  cantonment carries the aliases "DHA" and "DHA Karachi", which substring-match inside
  "DHA City", and the resolver breaks ties on longest name. Fixed in seed data by
  renaming the node so it wins the tiebreak, keeping the old spellings as aliases.
  Seeded report 10 now correctly comes through as `needs_human_review`.
- **Gazetteer 94 nodes to 123.** Lyari went 2 nodes to 12 and Keamari 1 to 13, the two
  towns the source document names as top priority.
- **Urdu and Roman-Urdu aliases across all 28 towns.** The seed previously had one Urdu
  alias in total, so Urdu reports mostly failed to resolve despite the app accepting
  Urdu input. Verified: an Urdu garbage complaint now resolves to Keamari and routes to
  the waste board.
- **Two dashboard widgets**, both populating with real seeded data. "Broken this week by
  area" clusters by resolved area and flags low-confidence counts. "Routing health"
  shows totals, reports needing a human, and authorities lacking a citizen channel.
  Auto-discovered, so `AdminPanelProvider` was never edited and cannot conflict.
- **Filament panel gated behind `is_admin`.** The panel had no `canAccessPanel` check, so
  authorisation rested entirely on there being exactly one user. The seeded citizen account
  proved the hole: it could sign into `/admin` and edit routing rules or delete reports.
  Verified fixed, admin 200 and citizen 403. This had to land before any citizen
  registration, which is what would have turned a latent hole into an open door.
- **The report screen reads as a conversation.** It was a stack of grey cards with no
  sentence anywhere saying who the complaint was routed to, which is the entire product
  promise. The report now renders as the message you sent and the routing as the reply,
  matching the compose screen, with an outcome headline above it and a way back to file
  another. The needs-review state got the same treatment since it is the DHA City case.
- **The reports table reads as a triage screen.** The photo column was 14 empty squares,
  because no report has a photo; it hides itself until one does. The two legitimately
  blank areas now say "Not yet resolved" instead of rendering as missing data. Raw enum
  columns are relabelled and the received time is a relative age with the exact timestamp
  on hover. Sorting needed two attempts: plain newest-first pushed the DHA City case and
  the AI failure onto page two, which is backwards for triage, so anything still needing
  a human now floats to the top with newest first inside that.

### PWA and mobile UI — built by Sumair, crossing into Maisam's lane again

**Maisam: three of your files changed again. Presentation only, no props, no data flow,
no PHP.** Full detail in `PWA.md`; the short version is here.

The app is now an installable progressive web app with an app shell and bottom navigation,
because roughly 70% of traffic will be on a phone and the site read as a website on one.

- **Installable.** Manifest, service worker with an offline shell, generated icons in
  maskable and standard variants, apple-touch-icon, and a real favicon replacing the
  zero-byte placeholder. An install prompt appears once and remembers being dismissed.
- **App shell.** `AppLayout` is a fixed-height flex column where only the content region
  scrolls, which removes rubber-band overscroll and address-bar jank. It takes `header`,
  `footer` and `padded` props. **The `bare` prop is gone** — every screen uses the shell.
- **Bottom navigation.** Two tabs, Report and Help. A report page keeps the Report tab lit.
- **Every emoji is now an inline SVG icon.** Twelve of them, in a new `Icon.jsx`. Emoji
  render as a different picture per platform and ignore `currentColor`, so they could
  never take the active or disabled state of the control holding them.
- **New Help screen** at `/help`, Sumair's lane, one closure route in `routes/sumair.php`.
  `routes/web.php` untouched.
- **The report screen is a single column** with grouped sections, a back arrow in a real
  app header, and the WhatsApp action pinned above the tab bar instead of below a scroll.

**Two of your open items are now closed, both fixed as part of this pass:**

- **Item 2, the `pending` blank page.** `Show.jsx` now has a branch for it, reusing the
  compose screen's typing indicator, and an unknown future status falls through to the
  same branch. Verified by creating a pending report in a transaction and reading the
  Inertia props back.
- **Item 3, loading feedback and the null-draft guard.** Both follow-up forms disable
  their input and change their button label while the DeepSeek call runs. The send actions
  hide when a `drafted` report has no draft text, so nobody can copy nothing or send an
  empty WhatsApp message.

**Two latent bugs were found and fixed while in there.** Neither was visible as a failure:

- `env(safe-area-inset-bottom)` was already used by the composer but the viewport meta
  lacked `viewport-fit=cover`, so it resolved to zero on every notched iPhone.
- The `pulse` keyframe was never emitted into the built CSS. The built stylesheet had zero
  `@keyframes` rules, so the typing indicator and the voice recording dot had never
  animated. Defined explicitly in `app.css` now.

Shared files touched: `app.blade.php`, `package.json`, `vite.config.js`, `app.css`,
`.gitignore`. One package installed with Sumair's approval, `vite-plugin-pwa`.

**Not verified:** installing to a real Android phone, offline behaviour, and the install
prompt firing. Standalone mode, safe-area insets and the theme-coloured status bar only
appear on real hardware. That is the one check left, and it is in `PWA.md`.

---

## Priority order from here

Work top down. Each item says who owns it and why it is worth the time.

Sumair's admin lane is finished. **Items 2 and 3 below are now done**, fixed during the
PWA pass described above; they are kept here with their original wording so the history
is legible. Nothing in the build is known to be broken.

### 1. Rehearse the demo — both, together, before anything else

Nothing below matters more than this. The build works; the risk now is presenting it
badly. Walk the actual path a judge will see: submit a report, watch it route, open the
admin, show the DHA City trap firing. Decide who talks and who drives.

The DHA City case is the strongest thing in the build. It is a real trap from the source
document, the pipeline catches it, and it is a thirty-second story: most systems would
confidently send this to the wrong cantonment. Lead with it.

Voice is now the natural opener: speak an Urdu complaint, watch it transcribe, route and
draft. Rehearse it in Chrome with the microphone actually permitted, and agree a fallback
line in case the room's audio defeats it. Typing the same sentence loses nothing but the
flourish.

### 2. ~~The `pending` status renders a blank page~~ — DONE, fixed in the PWA pass

**This is the only actual defect left in the build.** Verified by creating a report with
status `pending` and loading its page: it returns HTTP 200 and renders the header, the
chips and the raw text, then nothing at all. No draft, no routing, no explanation of what
is happening. `Show.jsx` branches on four statuses — `awaiting_answer`, `ai_failed`,
`needs_review`, `drafted` — and `pending` is not one of them, yet it is the column default
and the value every report is created with in `store()`.

Today the pipeline runs inline, so the window is milliseconds and the seeded data never
sits in this state. It becomes reachable the moment any of these happens: the citizen
refreshes during the AI call, the model save succeeds and the pipeline then throws before
the catch writes `ai_failed`, or the queue stops being `sync`. A fallback branch saying the
report is still being processed is a few lines, and it removes the only way to land a
citizen on a dead page.

### 3. ~~Loading feedback on the two follow-up forms~~ — DONE, fixed in the PWA pass

The clarify form and the AI-failure category picker both re-run the pipeline, which makes
a live DeepSeek call. Neither shows anything while that is in flight, so the citizen sees a
frozen button for as long as the model takes. Both already expose Inertia's `processing`
flag and neither uses it. The compose screen's typing indicator is the pattern to copy.

Also worth a guard while in there: `draft_en` and `draft_ur` can be null on a report whose
status is `drafted`. Copy and WhatsApp coalesce to an empty string, so a citizen can
currently copy nothing and send an empty WhatsApp message.

### 4. Watch a real AI failure once — both

The AI-failure path is seeded and demos correctly, but nobody has watched what happens
when DeepSeek actually times out live. The catch block sets `ai_failed`, so it should be
graceful. One deliberate test with the network cut is worth more than reading the code.

### 5. Gazetteer depth beyond Lyari and Keamari — Sumair

Diminishing returns now that the priority towns are covered. The structural gap is the
union council tier, which the name graph skips entirely. Genuinely the biggest hole in the
data and genuinely invisible in a demo. Leave it cut unless everything above is finished.

---

## Phase 2 — citizen accounts and complaint history

Requested by Sumair. **Maisam leads this after pulling**, because three of the four pieces
are in his lane. Read the dependency note before starting.

### Read this first

**Do not start this before the demo.** There is no auth scaffolding in the app at all: no
Breeze, no Fortify, no login route. The users table exists only because Laravel ships it.
Registration, login, logout, a history screen, a status update, plus a schema change and
matching seeder, is realistically two to three hours. It also adds a login wall in front of
the one story that wins this brief, which is a complaint being routed correctly.

**The security prerequisite is already done.** The Filament panel had no `canAccessPanel`
check, so authorisation rested entirely on there being one user. The seeded citizen account
could sign straight into `/admin` and edit routing rules. That is fixed and verified: admin
gets 200, citizen gets 403. Had registration shipped first, every citizen would have had
admin. Do not remove the `is_admin` gate.

### Milestone A — auth scaffolding (Sumair's lane, ~45 min)

Install Laravel Breeze with the React stack, matching the existing Inertia setup. **Needs
Sumair's approval first, since the house rules forbid installing a Composer package without
asking.** It generates register, login, logout and password reset into a new `Pages/Auth`
directory that collides with nothing either developer owns.

Guests must keep being able to file a complaint. Anonymous reporting is the product; an
account is an optional upgrade for people who want their history.

### Milestone B — report ownership (Maisam's lane, ~20 min) — BLOCKS C, D, E

One nullable `user_id` on `reports`, and `store()` attaches `auth()->id()` when signed in.

Nullable is the important part. All 14 seeded reports predate accounts and must stay valid,
and guests must keep filing. A non-nullable column breaks both. Per the house rules the
seeder update ships in the same commit, since the other developer recovers with
`migrate:fresh --seed`.

### Milestone C — complaint history (Maisam's lane, ~40 min)

A "My complaints" screen listing the signed-in user's reports with status, area and date,
each linking into the existing detail page. This is the feature Sumair actually asked for.
The report screen is already a chat surface, so a list of past conversations fits the shape.

### Milestone D — citizen status update (Maisam's lane, ~30 min)

Let the citizen say whether the routing worked: resolved, no response yet, wrong authority.

**This needs a product decision before any code.** The existing `status` column tracks
pipeline state (`pending`, `drafted`, `ai_failed`), not real-world outcome. Overloading it
would corrupt the admin triage sort and both dashboard widgets, which read that column. Add
a separate `citizen_outcome` column instead.

The "wrong authority" answer is the valuable one: it is real feedback on routing quality,
and it is exactly what the gazetteer and routing rules need to improve.

### Milestone E — admin view of accounts (Sumair's lane, ~20 min)

A Filament users resource with a relation manager showing each account's reports. Easy once
B exists, and worth nothing before it.

### Dependency summary

| Milestone | Lane | Blocked by |
|---|---|---|
| A — auth scaffolding | Sumair | approval to install Breeze |
| B — `user_id` on reports | **Maisam** | A |
| C — history screen | **Maisam** | B |
| D — citizen outcome | **Maisam** | B, plus the column decision |
| E — admin users resource | Sumair | B |

Everything except A and E is Maisam's. This cannot be built from Sumair's side alone.

---

## Cut

- **`/tanker` page.** Stretch C, deliberately dropped. `routes/sumair.php` is still an
  empty placeholder and there is no tanker controller or page. The tariff data sits in
  the source document if anyone wants to revive it, and a `tanker` routing rule is
  already seeded pointing at the water corporation.
- **Union council tier** in the gazetteer. Names are usable but boundaries are not
  public, and nobody has sourced the roughly 246 names.
- **SITE, Port and Steel Town as special zones.** Modelled as landmarks instead. A
  special zone with no authority row overrides nothing, and there is no authority record
  for a port trust or a steel-mill township. Comment in the seeder explains this so
  nobody "fixes" it later.

---

## Ground rules that still apply

- **Lane discipline.** Maisam owns `reports`, `app/Ai/`, `app/Http/Controllers/Maisam/`,
  `routes/maisam.php`, `Pages/Maisam/`, `MaisamSeeder`. Sumair owns `authorities`,
  `routing_rules`, `gazetteer_nodes`, `SumairSeeder`, and `app/Filament/`. Do not edit
  across the line. **One deliberate exception so far:** the voice-note edit to
  `Pages/Maisam/Report/Create.jsx`, described above. If you cross a lane again under time
  pressure, record it here the same way so the other developer is never surprised by a
  diff in their own files.
- **Shared files need a heads-up first:** `routes/web.php`, `DatabaseSeeder.php`,
  `AdminPanelProvider.php`, `layouts/app.blade.php`, `.env`, `config/*`, `composer.json`,
  `package.json`.
- **Schema is frozen.** New nullable columns and new tables only. Any schema change ships
  with its seeder update in the same commit, because the other developer recovers by
  running `migrate:fresh --seed`.
- **No tests this build.** Verify by exercising the app and by
  `php artisan tinker --execute`.
- **Run `vendor/bin/pint --dirty --format agent`** before every commit that touched PHP.

## Known environment issue

The `laravel-boost` MCP server fails to connect, so the `database-query` and `search-docs`
tools are unavailable. Use `php artisan tinker --execute` for data checks instead. Worth
restarting the editor if someone needs those tools.
