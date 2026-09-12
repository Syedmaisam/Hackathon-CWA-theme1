# HEADSTART.md

**4-hour hackathon. Single Laravel app. MySQL. Two devs: Maisam and Sumair.**

This file lives in the repo root. Read it once tonight, follow the clock tomorrow.

> **If you are Claude Code reading this:** `CLAUDE.md` holds the rules you must follow on every turn, including the "Hackathon Mode" section, which supersedes any conflicting Laravel Boost guideline above it. This file is context, not a rulebook. Read it once at the start of a session if asked to, then work from `CLAUDE.md`. The two developers are **Maisam** and **Sumair**. Route files, seeders, and view folders are split by their names. Never write into the other person's lane without being told to.

---

## 0. What actually decides the outcome

Judges score a **working demo on one screen**. They do not score your architecture, your test coverage, or your auth flow. Every decision below is biased toward: fewer moving parts, seeded data that looks real, one polished screen.

Single Laravel app is the right call, and it buys more than convenience. No API contract to freeze, no CORS, no second dev server, no integration window. Protect that by not reintroducing the split through the back door: **do not build JSON endpoints that your own Blade or Livewire pages then consume.** Render server-side and pass data straight to the view.

The remaining risks are scope, merge conflicts, and migration drift between two MySQL instances. Sections 2, 7, and 8 handle those.

---

## 1. Tonight (60 minutes, do not let this eat tomorrow)

### MySQL, on both machines, identical

You picked MySQL because reading the data during the build matters, and that is a fair trade. The cost is that you now have two separate database instances that must stay in step. Set them up identically tonight so no time goes into it tomorrow.

```sql
CREATE DATABASE hackathon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hackathon
DB_USERNAME=root
DB_PASSWORD=
```

Same database name, same user, on both laptops. Commit a `.env.example` with these values so Sumair copies it and nothing drifts. Then both of you run `php artisan migrate:fresh --seed` tonight and confirm it completes clean on both machines.

Keep a GUI open all day on a second window (TablePlus, Workbench, whatever you use). Being able to eyeball a table in two seconds is the reason you chose MySQL. Use it.

### If the app needs AI or vector search, decide at T+15, not later

If the build involves embeddings, semantic search, or RAG, switch to **Postgres + pgvector at T+15**. Do not start on MySQL and migrate at T+90. That is an hour you will not have.

```env
DB_CONNECTION=pgsql
DB_PORT=5432
```

Have Postgres installed and a database created **tonight** so the switch is a one-line `.env` change plus `migrate:fresh --seed`. The vector column needs a raw statement in the migration rather than a Blueprint method, so let Claude generate it with `DB::statement` and move on.

Pick one database at T+15. Never run both.

### Laravel Boost

```bash
composer require laravel/boost --dev
php artisan boost:install        # select Claude Code when prompted
```

Boost is an MCP server plus Laravel-specific guidelines and agent skills. It gives Claude Code direct access to your schema, routes, models, config, and logs, plus a documentation API with semantic search over the Laravel ecosystem. Without it, Claude greps the codebase and reads migration files to answer "what columns does `orders` have." With it, one tool call. This is the single largest token saving available to you, and it is what makes the T+180 documentation step take five minutes instead of forty.

```bash
claude mcp list
# if laravel-boost is missing:
claude mcp add -s local -t stdio laravel-boost php artisan boost:mcp
```

Then verify it actually responds: open Claude Code and ask it to run `database-schema` and list your tables. If that comes back empty, fix it tonight.

### Install the hackathon guideline (this is the important one)

Boost **generates** your `CLAUDE.md`. Anything you paste into it is destroyed the next time anyone runs `boost:install` or `boost:update`. Worse, the generated file actively contradicts how you need to work tomorrow: it instructs the agent to write PHPUnit tests, to use Eloquent API Resources for APIs, and not to create new base folders without approval. Left alone, Claude will fight you on all three.

The supported way to change this is `.ai/guidelines/`. Files there are merged into the generated guidelines, and they survive regeneration.

```bash
mkdir -p .ai/guidelines
# copy the provided hackathon.md into .ai/guidelines/hackathon.md
php artisan boost:install
```

Then **open `CLAUDE.md` and confirm the "Hackathon Mode" section is actually in it**, and that it appears after the PHPUnit and API sections. It opens by stating it supersedes conflicting guidance, which is what resolves the contradictions. If it is missing, the merge did not happen and you need to know that tonight, not at T+30.

Commit `.ai/guidelines/hackathon.md`. Gitignore `CLAUDE.md`, `.mcp.json`, and `boost.json`, since Boost regenerates all three and a two-dev repo does not need merge conflicts in generated files.

### Background processing: default off, switch on deliberately

You may need queues, events, observers, jobs, or broadcasting. The rule is not "never," it is **sync by default, and every async piece must be justified and visible.**

Start here:
```env
QUEUE_CONNECTION=sync
CACHE_STORE=array
SESSION_DRIVER=file
MAIL_MAILER=log
BROADCAST_CONNECTION=null
```

`sync` means dispatched jobs run inline, immediately, in the request. Your `dispatch()` calls work, your job classes are real, and there is no worker process to forget about. For most demos this is the correct final state: you keep the architecture and lose the failure mode.

Switch to a real queue only when the demo needs the request to return before the work finishes (long AI call, file processing, an external API you do not want to block on):

```env
QUEUE_CONNECTION=database
```
```bash
php artisan queue:table && php artisan migrate
php artisan queue:listen          # listen, not work
```

Use `queue:listen`, not `queue:work`. `work` caches your code and keeps running the old version after every edit, which produces the single most confusing class of hackathon bug: you fix the job, nothing changes, and you lose twenty minutes. `listen` reloads on every job.

**Broadcasting**: if the demo genuinely needs live updates on screen, use Laravel Reverb (first-party, one command). If "it updates when you refresh" is acceptable, or Livewire polling gets you there, do that instead. Reverb is a third process to keep alive on stage.

**Observers and model events**: fine to use, but they hide behaviour, and hidden behaviour at T+200 with an unexplained side effect is expensive. Prefer an explicit method call unless the same thing genuinely has to fire from three places.

**The rule that makes this safe:** every background process must start from one command, so the demo boots in one step. Laravel's `composer run dev` already runs the server, queue listener, logs, and Vite concurrently. Use it, and confirm tonight that it works on both machines.

```bash
composer run dev
```

### Claude Code plugins

```
/plugin marketplace add anthropics/claude-code
/plugin install laravel-boost
/plugin install frontend-design
```

- **laravel-boost** wires the MCP server in cleanly.
- **frontend-design** (Anthropic verified) exists to produce polished frontends that avoid the generic AI aesthetic. Your direct answer to "must have brilliant UI." Works on Blade and Tailwind, not only React.
- If Filament is in the plan, `rizaleow/filament-boost` adds Filament v5 fluency and commands like `/filament:resource`. Install tonight only if you are sure.
- Skip `superpowers` and `code-review`. Both are good, both add ceremony you do not have time for.

### Turn off every connector you do not need

Gmail, Google Calendar, Google Drive, and Canva are connected. Every enabled MCP server injects its tool definitions into **every single request** for the whole session. Disable all four tomorrow. Keep only `laravel-boost`. Free context.

### Git access for Sumair

Push the repo, add Sumair as a collaborator, have him clone and push one trivial commit **tonight**. A permissions problem at T+30 is a stupid way to lose twenty minutes. Both of you should have `composer run dev` working locally before you sleep.

### Deployment escape hatch

```bash
npx cloudflared tunnel --url http://localhost:8000
```

Test it once tonight and confirm the URL loads on your phone. Demo from localhost on your own screen if the rules allow it; the tunnel is the backup. If you already have Laravel Cloud or Forge configured, use it. Do not start learning a deploy pipeline at T+200.

### Check your usage window

Claude Code runs on a rolling 5-hour session window. A 4-hour hackathon fits inside almost exactly one window, so **do not burn quota tonight**. Do the setup above with light prompts, then stop. Check Settings > Usage before you sleep. Ideally your window resets shortly before the event starts.

---

## 2. The UI decision, at T+15

Three minutes, out loud, written on paper. Do not revisit it.

### Default: Livewire 3 + Tailwind for the hero screen, Filament for the back office

This fits your stack and your speed better than anything else, and avoids a React context switch.

- **Livewire** gives interactivity without writing JavaScript. `wire:model.live.debounce.300ms` for live search, `wire:loading` for skeletons, built-in pagination, inline validation. The ratio of visible interactivity to lines written is the best available in the Laravel world.
- **Filament** gives complete, professional CRUD from your schema in minutes: `php artisan make:filament-resource Order --generate`.

**The split that works:** Filament at `/admin` as the operator view, one custom Livewire page as the hero screen judges actually look at. You get a whole working back office nearly free and spend real design time on the one screen that scores.

### When to pick something else

- **Bare Blade + Tailwind + Alpine**: mostly static or read-only, no forms, no filtering. Fastest start, nothing to install.
- **Inertia + React + shadcn/ui**: only if the hero screen needs something Livewire genuinely cannot do (canvas, complex drag and drop, heavy client state). Higher ceiling, but a build step, a second mental model, and TypeScript. In four hours this needs a real reason.
- **Filament alone**: acceptable if the product *is* an admin tool. Judges have seen the Filament look before and it reads as "admin panel," not "product." If you go this route, change the primary colour and font at T+20.

Install your pick at T+15 and never fight it. Swapping frontend layers at T+90 is unrecoverable.

---

## 3. Models and effort in Claude Code

Two dials now, not one: which model, and how hard it thinks. Stop toggling thinking on for everything. That is exactly what drains a session.

| Phase | Model | Effort | Why |
|---|---|---|---|
| Scoping and data model (T+0 to T+20) | Opus 5, plan mode | `high` | One decision, high leverage |
| Migrations, models, Livewire components, Filament resources, Blade | Sonnet 5 | `medium` | This is typing, not thinking |
| The one hard part (the clever bit, an integration, an AI layer) | Opus 5 | `xhigh` | Deeper reasoning without max's token cost |
| Real blocker you have been stuck on 10+ minutes | Opus 5 | `max` | Per-task only, then drop back |
| Renames, formatting, copy changes, boilerplate | Sonnet 5 or Haiku 4.5 | `low` | Do not pay to think about a rename |
| Generating `FLOW.md` at T+180 | Sonnet 5 | `medium` | Boost does the lookup work, not the model |

```
/model            # interactive selector, switches mid-session, no restart
/effort medium    # low | medium | high | xhigh | max | auto
/effort           # check current level
```

**The setting to actually use:** `/model opusplan`. Opus handles plan mode, then automatically drops to Sonnet for implementation. The expensive model decides, the cheap model types. For a hackathon where you plan once and generate a lot of code, that is close to optimal. Set it at T+0 and leave it.

Availability in `/model` varies by plan and rollout, so treat your actual selector as the final answer.

**On thinking specifically:** thinking and effort are separate settings. Effort controls how thorough Claude is on every response; thinking controls whether it reasons in an expandable block first. Running both high on every prompt is how you hit a limit at hour two with a half-built demo. Default to `medium`, raise to `xhigh` for the two or three genuinely hard prompts, drop straight back after.

---

## 4. The architecture shape

Ship this. Do not improve it tomorrow.

```
app/
  Models/                        Eloquent models. Fillable, casts, relations. Nothing else.
  Livewire/
    Maisam/                      Maisam's components
    Sumair/                      Sumair's components
  Filament/Resources/            Generated with --generate. Light edits only.
  Jobs/                          Only if async is genuinely needed. Sync by default.
  Http/Controllers/              Only for the few plain pages Livewire does not own.
routes/
  web.php                        Loads the two lane files. Then never touched again.
  maisam.php                     Maisam's routes only.
  sumair.php                     Sumair's routes only.
database/
  migrations/                    Additive only after T+90. One owner per table.
  seeders/
    DatabaseSeeder.php           Calls MaisamSeeder and SumairSeeder. Frozen after T+20.
    MaisamSeeder.php
    SumairSeeder.php
resources/views/
  layouts/app.blade.php          One owner only.
  livewire/maisam/
  livewire/sumair/
FLOW.md                          Written at T+180. See Section 9.
.ai/guidelines/hackathon.md      Your rules. Committed. Merged into CLAUDE.md.
CLAUDE.md                        Generated by Boost. Gitignored. Never hand-edit.
```

**Rules that are not negotiable tomorrow:**

- **No service layer, no repositories, no actions, no DTOs.** Eloquent goes in the Livewire component or the controller. You are writing a few hundred lines total.
- **No API routes, no JSON responses, no API Resources.** If you catch yourself writing an endpoint your own page will fetch, stop.
- **Validation inline.** Livewire rules as a property, or `$request->validate([...])`. No FormRequest classes.
- **No auth unless auth is the product.** Seed two users, `Auth::loginUsingId(1)` on a dev route, or install Breeze if you truly need a login screen on camera. Building auth properly is 30 to 40 minutes nobody will click through.
- **Four tables maximum.** If the data model needs a fifth, cut a feature instead.
- **Async is opt-in and must be visible.** Jobs stay on `sync` unless the demo needs the request to return early. If you switch to `database`, `queue:listen` runs under `composer run dev` and never as a separate forgotten terminal.
- **The seeder is a first-class deliverable.** Real names, plausible amounts, dates spread across the last two weeks, at least one record in every visual state (pending, active, failed, empty). Write it at T+30, not at T+200. Empty tables and "Test Item 1" read as broken to judges.

---

## 5. Guardrails: where they live and what they override

Do not hand-edit `CLAUDE.md`. It is generated. Your rules live in `.ai/guidelines/hackathon.md`, which Boost merges in and which survives regeneration. The file is provided alongside this one.

Three of Boost's own guidelines conflict with a hackathon and are explicitly overridden:

| Boost says | Hackathon Mode says | Why |
|---|---|---|
| Write PHPUnit feature and unit tests; tests matter more than verification scripts | No tests at all; verify by loading the page and using `database-query` | Tests are the single largest time sink available to you and no judge sees them |
| For APIs, default to Eloquent API Resources and API versioning | There is no API; no `routes/api.php`, no JSON endpoints, no Resources | The whole speed advantage of one Laravel app is not building an interface between two halves of it |
| Do not create new base folders without approval | The Maisam and Sumair lane folders are pre-approved | Otherwise Claude stops and asks every time it scaffolds into a lane |

Two Boost guidelines are worth keeping and are reinforced rather than overridden:

- **Pint before finalizing.** `vendor/bin/pint --dirty --format agent`. With two developers pushing to `main` every ten minutes, inconsistent formatting generates conflicts that are pure waste. This one earns its keep.
- **Only create documentation when explicitly requested.** `FLOW.md` is named as pre-approved so the T+180 step works, and nothing else gets created.

The rest of the Hackathon Mode file covers scope discipline, MySQL rules, background processing, and UI. Keep it to roughly the length it ships at. It is re-read on every turn, so every line is a tax you pay on every prompt for four hours. That is exactly why the long context lives in this file and only the enforceable rules live there.

---

## 6. Prompting for speed and low tokens

**Name the files.** "Add a `status` column to `orders` and show it as a badge in `app/Livewire/Maisam/Orders/Index.php` and its view" beats "add order statuses" by a wide margin. The vague version makes Claude read half your app first.

**Name the Boost tool.** Boost's tools are `database-schema` (table structure), `database-query` (read-only queries), `browser-logs` (browser errors and exceptions), `get-absolute-url`, and `search-docs`. Saying "use `database-schema` to check `orders`" is faster and cheaper than letting Claude read through your migrations. `browser-logs` in particular will save you from guessing at a broken Livewire interaction.

**Be selective with `search-docs`.** It is genuinely useful for Livewire and Filament version-specific syntax, where guessing the API costs a debugging cycle. It is not free, and for routine Laravel CRUD, Eloquent, and Blade it buys nothing. The Hackathon Mode guideline says this, but say it in the prompt too when it matters.

**One feature per session.** Finish a vertical slice, commit, `/clear`. A session carrying four features of history pays for all four on every prompt. `/clear` is the cheapest performance win you have.

**Use `/rewind` instead of arguing.** If a generation goes wrong, do not spend three turns correcting it. `Esc Esc` or `/rewind`, reword, regenerate. Correcting usually costs more tokens and lands worse.

**Batch the boring.** "Generate the migration, model, Livewire component, view, and route for `Order` with fields x, y, z, in the Maisam lane, following CLAUDE.md" as one prompt on Sonnet at `medium`. Not five prompts.

**Plan mode once, then execute.** Use it at T+0 for the data model and screen list. After that the path is clear and plan mode is overhead.

**Let Claude run artisan.** `make:livewire`, `make:model -m`, `make:filament-resource --generate`, `migrate:fresh --seed`. Generated scaffolding costs almost no output tokens compared to Claude writing the same files by hand.

---

## 7. Two devs, one Laravel repo

### Rule 1: every shared file gets exactly one owner

Conflicts only ever happen in a handful of files. List them at T+15, before writing any code, and write a name next to each. The non-owner never edits them; they ask out loud and the owner makes the change.

| File | Owner |
|---|---|
| `routes/web.php` | set up at T+20, then frozen for both |
| `database/seeders/DatabaseSeeder.php` | set up at T+20, then frozen for both |
| `resources/views/layouts/app.blade.php` | one name |
| `tailwind.config.js` / `resources/css/app.css` | one name |
| `composer.json`, `package.json`, `.env` | one name |
| each database table | one name per table |

Two people writing migrations against the same table is the one Laravel conflict that genuinely hurts. Table ownership is the fix.

### Rule 2: the lane files trick

At T+20, create `routes/maisam.php` and `routes/sumair.php`, load both from `web.php`, and never open `web.php` again:

```php
// routes/web.php
require __DIR__.'/maisam.php';
require __DIR__.'/sumair.php';
```

Same for seeders: `DatabaseSeeder` calls `MaisamSeeder::class` and `SumairSeeder::class`, and each of you only ever edits your own. Two minutes of setup that removes your two most likely conflicts for the whole day.

### Rule 3: migrate after every pull

This is the MySQL-specific one, and it is the failure mode that will actually bite you. Two separate database instances mean a pulled migration that nobody ran produces a column-not-found error that looks like a code bug. Make this one command your reflex:

```bash
git pull --rebase origin main && php artisan migrate
```

If it errors or the data looks wrong: `php artisan migrate:fresh --seed`. Losing your local data costs nothing because the seeder is the source of truth. That is the whole reason the seeder gets written at T+30.

### Rule 4: split by vertical feature, never by layer

Do **not** do "Maisam writes backend, Sumair writes frontend." In one Laravel app that puts both of you inside every feature, blocking each other at every step. Each person owns features end to end: migration, model, Livewire component, view.

With two people, the stronger split is usually **critical path vs everything else**:

- **Maisam**: the one flow the demo proves, end to end. Nothing else.
- **Sumair**: layout, Tailwind theme, nav, Filament admin panel, seed data, the second screen, the auth stub, the tunnel, and `FLOW.md` at T+180.

This guarantees the critical path is never waiting on the other person, which is the failure mode that wastes the most time with two devs.

Whoever owns the layout and the CSS also owns the visual direction: accent colour, font, spacing. One person making all the style calls is why the result looks coherent instead of like two apps stapled together.

### Rule 5: push straight to `main`, every 10 to 15 minutes

No PRs, no review, no feature branches. PR ceremony costs 20 to 30 minutes over four hours and buys nothing when you are sitting next to each other.

```bash
git add -A && git commit -m "feat: orders list"
git pull --rebase origin main && php artisan migrate
git push origin main
```

`--rebase` is not optional. In a shared codebase, merge commits across two lanes make the history unreadable at exactly the moment you need to find what broke.

Also: `.env` stays out of git, `.env.example` stays in.

---

## 8. The clock

| Time | What |
|---|---|
| **T+0 to T+12** | Theme drops. Both read it. Write down the **one sentence** the demo proves. Cut everything that does not serve that sentence. Tape it to the table. |
| **T+12 to T+15** | Two decisions: UI layer (Section 2), and MySQL vs Postgres if AI or vector search is involved. Written on paper. No revisiting. |
| **T+15 to T+20** | Data model on paper, four tables maximum. List the screens. **List every shared file and write an owner next to each.** Assign table ownership. Decide now whether anything genuinely needs a real queue. |
| **T+20 to T+30** | Scaffold: lane route files, split seeders, migrations, models, Tailwind theme, layout blade, Filament install if used. Create the `FLOW.md` stub (Section 9). Both lanes in parallel. |
| **T+30 to T+90** | Slice 1: the core action, end to end. Seed data written here, not later. |
| **T+90 to T+150** | Slices 2 and 3. **Schema freeze at T+90.** Additive migrations only past this point. |
| **T+150 to T+180** | Wire the screens together, fix what is broken, delete dead routes and half-built pages. Nothing new gets built. |
| **T+180** | **FEATURE FREEZE. Hard stop.** Anything unfinished gets hidden, not finished. Sumair runs the `FLOW.md` generation prompt while Maisam starts polish. |
| **T+180 to T+210** | Polish: empty states, loading states, seed data quality, the one screen judges will look at. |
| **T+210 to T+230** | Rehearse the demo **twice**, end to end, on the machine you will present from. `migrate:fresh --seed` between runs. |
| **T+230 to T+240** | Buffer. Something will need it. |

**T+180 is the rule most teams break and most teams regret.** A feature that is 80% done at T+200 is worth zero and costs you the polish window.

---

## 9. FLOW.md: the code-review document

Code review happens after the hackathon, so the reviewer needs the flow of the application, not a tour of your commits. Two moves.

Note that Boost instructs Claude not to create documentation files unless explicitly requested. `FLOW.md` is named as pre-approved in the Hackathon Mode guideline, so the prompt below works without an argument. Do not ask for any other doc.

### At T+20: create the stub (two minutes)

```markdown
# FLOW.md

## What this is
<one sentence: the problem>
<one sentence: what the app does about it>

## Data model
<tables and how they relate, as you agreed on paper>

## Screens
<route → what it does>

## Shortcuts taken
<filled in at T+180>
```

Two minutes, and it anchors the T+180 generation so Claude is correcting a draft instead of inventing one.

### At T+180: generate the rest from the actual code

Do not write this by hand during the build. It costs time you do not have and goes stale within twenty minutes. With Boost installed, one prompt reads the real routes, models, and schema and produces an accurate document in about five minutes. Run it on Sonnet at `medium` while the other person polishes.

The prompt:

```
Read FLOW.md. Using the Laravel Boost tools, inspect the actual routes,
models, database schema, Livewire components, and any jobs or events in
this codebase, then rewrite FLOW.md so it accurately documents what was
built. Include:

1. What the app does, in two sentences.
2. The data model: each table, its key columns, and its relationships.
3. The route map: route → controller or Livewire component → view.
4. The main flow, step by step, for the core user action, naming the
   real files involved at each step.
5. Any background processing: jobs, events, observers, queue driver, and
   whether they run sync or async.
6. How to run it locally from a clean clone.
7. "Shortcuts taken": read CLAUDE.md and list, in plain language, the
   engineering practices we deliberately traded away for speed and what
   the production version would do instead.

No marketing language. No invented features. If something is not in the
code, do not document it. Keep it under two pages.
```

Section 7 is the one that matters most for a code review. A reviewer who sees "we skipped tests, validation is inline, no service layer, queue is sync, here is what we would change" reads a team that made deliberate trade-offs. A reviewer who finds those things undocumented reads a team that did not know better. Same code, different conclusion.

### Then commit it and stop

`FLOW.md` is done at T+185. Do not keep editing it as you polish. If a late change makes it wrong in a small way, fix that line at T+230 in the buffer slot.

---

## 10. UI that actually reads as brilliant

- **Use the `frontend-design` plugin.** It exists to avoid the generic AI look judges will see forty times in one day. Applies to Blade and Tailwind, not only React.
- **One accent colour, one font, generous whitespace.** Pick all three at T+20 and never revisit. Not three gradients.
- **Do not build a landing page.** Nobody scores your hero section. Build the product screen.
- **Judges see one screen.** Decide at T+15 which screen that is and spend the entire polish window on it alone.
- **Empty states, loading states, one error state.** Fifteen minutes of work and the difference between "prototype" and "product." In Livewire, `wire:loading` plus a skeleton div reads far more finished than a spinner.
- **Seeded data quality is UI work.** Ten rows of believable data beat a beautiful table with three rows of placeholder text.
- **If you use Filament, change the primary colour and font at T+20.** Default Filament is instantly recognisable and reads as "admin panel."
- **Real content, no lorem ipsum.** Ever. Fastest way to look unfinished.

---

## 11. Demo, in 90 seconds

Write this at T+180, not at T+235.

1. One sentence: the problem.
2. One sentence: what this does about it.
3. Three clicks, on screen, that prove it.
4. One sentence: what is next.

Rehearse out loud twice. Run `php artisan migrate:fresh --seed` before the final rehearsal and before the actual demo so you present from a known state. Keep a screen recording of a successful run on your phone as insurance against the wifi.

---

## 12. Failure modes, ranked by how often they happen

1. **Scope not cut at T+15.** Everything downstream fails from this.
2. **Claude writing tests, or arguing about API Resources, because the Hackathon Mode guideline never merged.** Verify it is in `CLAUDE.md` tonight.
3. **A pulled migration nobody ran.** `git pull --rebase && php artisan migrate` as one reflex. This is the MySQL tax.
4. **Frontend layer or database re-litigated at T+60.** Decide at T+15, write it down, never reopen.
5. **Two people editing the same shared file.** Ownership list, lane route files, split seeders, 10-minute pull-rebase cadence.
6. **`queue:work` caching stale code.** Use `queue:listen`, or stay on `sync`.
7. **A queue worker or Reverb process nobody started before the demo.** One command to boot everything: `composer run dev`.
8. **Rebuilding the split you avoided.** No JSON endpoints your own pages consume.
9. **Auth eating an hour.** Skip it.
10. **Schema change at T+170.** Freeze at T+90.
11. **Features still being built at T+210.** Freeze at T+180.
12. **Claude Code usage limit at hour two.** Effort `medium` by default, `/clear` between features, `xhigh` only for the hard parts.
13. **Demoing from an unseeded or half-broken database.** Reset and rehearse twice.

---

## Appendix: commands you will actually use

```bash
# boot everything in one command
composer run dev

# reset the demo to a known state
php artisan migrate:fresh --seed

# after every pull
git pull --rebase origin main && php artisan migrate

# scaffolding
php artisan make:model Order -m
php artisan make:livewire Maisam/Orders/Index
php artisan make:filament-resource Order --generate
php artisan make:job ProcessOrder

# queues, only if you switched off sync
php artisan queue:table && php artisan migrate
php artisan queue:listen

# before every commit that touched PHP
vendor/bin/pint --dirty --format agent

# when something is wrong and you do not know why
php artisan about
php artisan route:list
tail -f storage/logs/laravel.log

# public URL
npx cloudflared tunnel --url http://localhost:8000
```

Claude Code:
```
/model opusplan       # set once at T+0
/effort medium        # default; xhigh for the hard parts only
/clear                # after every completed slice
/rewind               # instead of arguing with a bad generation
Esc Esc               # same thing, faster
```
