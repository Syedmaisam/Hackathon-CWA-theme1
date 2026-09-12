# FLOW — where things stand, who owns what next

Pulled fresh? Run `composer install && npm install`, copy `.env.example` to `.env`
if you don't have one, set `DEEPSEEK_API_KEY` (ask Maisam), then
`php artisan migrate:fresh --seed`.

## What's already built (shared — read before editing)

- **Schema**: `reports` (Maisam's table), `authorities` / `routing_rules` /
  `gazetteer_nodes` (Sumair's tables). All four migrations and models exist.
  Additive-only from here — no dropping/renaming columns.
- **Routing pipeline** (`app/Models/Report.php`): `resolveLocation()` →
  `applySpecialZoneOverride()` → `applyRoutingRule()` → `scoreConfidence()`,
  plus `templateDraft()` as the no-AI fallback.
- **AI agents** (`app/Ai/Agents/`): `ClassifyReport` and `DraftComplaint`,
  both on DeepSeek (`deepseek-flash`, thinking disabled). Spiked live against
  the real API — works.
- **`Maisam\ReportController`** + `routes/maisam.php`: create / store / show /
  clarify / confirm-category. Pipeline runs inline on submit, no queue.
- **`SumairSeeder`**: all authorities, routing rules, and a gazetteer name
  graph seeded from `data/karachi_civic_authorities_source_of_truth (1).md`.
  Coverage is solid but not exhaustive — see Sumair's list below.
- **Filament admin** (`app/Filament/`): resources for all four tables under
  a "Civic Routing" nav group, generated as a starting point, not yet
  polished or seen against real seed data.
- **Inertia + React scaffold**: `resources/views/app.blade.php`,
  `resources/js/app.jsx`, `resources/js/Layouts/AppLayout.jsx`. Teal accent,
  Instrument Sans + Noto Nastaliq Urdu already wired into `app.css`.

## Not built yet — this is the split

### Maisam (critical path, in progress now)

- `resources/js/Pages/Maisam/Report/Create.jsx` — textarea, photo drop zone,
  submit, processing stepper skeleton.
- `resources/js/Pages/Maisam/Report/Show.jsx` — routing explanation card,
  EN/UR draft tabs, copy/WhatsApp/mailto, clarifying-question form,
  ai_failed category picker.
- `database/seeders/MaisamSeeder.php` — the 14 demo reports from the plan,
  every visual state represented (drafted/awaiting_answer/needs_review/ai_failed).
- Wire both seeders into `database/seeders/DatabaseSeeder.php` (one-time edit).

### Sumair (once you've pulled `3780b99` — work these independently)

1. **Review the Filament admin** against real seed data once `MaisamSeeder`
   lands (pull again after Maisam pushes). Polish the Report list/view,
   check the `resolved_area` grouping actually clusters the Gulshan Block
   13-D reports, tag unverified authority contacts visibly.
2. **Expand gazetteer coverage** if there's time: the seed currently covers
   all 7 districts, all cantonments + DHA phases + Clifton blocks, and ~1-2
   landmarks per town. Priority order per the source doc: Lyari and Keamari
   need the most depth next (highest complaint density / were missing from
   v1). Don't touch `reports`, `app/Ai/`, `app/Http/Controllers/Maisam/`, or
   any `Pages/Maisam/*` file — that's Maisam's lane.
3. **Dashboard widget** ("broken this week by area") on the Filament admin
   — cut-list item, do it only if the two items above are solid.
4. **Stretch C — `/tanker` page** (`Sumair\TankerController` +
   `Pages/Sumair/Tanker.jsx`, wired into `routes/sumair.php`, currently a
   stub) — lowest priority, cut first if time runs out.

Ping Maisam before touching `routes/web.php`, `database/seeders/DatabaseSeeder.php`,
`config/*`, `composer.json`, or `package.json` — those need a heads-up per
the hackathon rules in `CLAUDE.md`.
