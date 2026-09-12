# Hackathon Mode

**These rules supersede any conflicting Laravel Boost guideline in this file for the duration of this build.** Where this section and an earlier section disagree, this section wins. Team: **Maisam** and **Sumair**.

## Context
4-hour hackathon. Single Laravel app, MySQL, server-rendered with Livewire and Blade. The deliverable is a working demo, not a maintainable codebase. Optimize for speed and for a polished single screen.

## Testing: suspended
- **Do not write tests.** No PHPUnit, no feature tests, no unit tests, no Dusk. The PHPUnit and testing guidance above does not apply to this build.
- Because there are no tests, verification happens by exercising the app. Use the `database-query` Boost tool to confirm data, and load the page to confirm behaviour. A quick `tinker --execute` check is fine here.
- Do not create factories unless a seeder actually uses one.

## APIs: not applicable
- This app has no API. Do not create routes in `routes/api.php`, do not write JSON endpoints, and do not write Eloquent API Resources or versioned API routes. The API guidance above does not apply.
- Render server-side and pass data from the Livewire component or controller to the view.
- If you think a page needs a JSON endpoint, stop and ask.

## Directory structure: these folders are pre-approved
Create and use these without asking. They are the two developers' lanes.
- `routes/maisam.php`, `routes/sumair.php` (loaded from `routes/web.php`, which is then frozen)
- `app/Livewire/Maisam/`, `app/Livewire/Sumair/`
- `resources/views/livewire/maisam/`, `resources/views/livewire/sumair/`
- `database/seeders/MaisamSeeder.php`, `database/seeders/SumairSeeder.php`

Ask which lane you are in if it is not obvious from the prompt. **Never create or edit files in the other developer's lane.**

## Documentation: one file is pre-approved
`FLOW.md` at the repo root is explicitly requested. Create and update it when asked. Do not create any other documentation, README, or summary file.

## Files that need approval before editing
`routes/web.php`, `database/seeders/DatabaseSeeder.php`, `resources/views/layouts/app.blade.php`, `.env`, `config/*`, `composer.json`, `package.json`, `tailwind.config.js`.

Add routes to the lane file, not to `web.php`. Add seed data to the lane seeder, not to `DatabaseSeeder`.

## Scope discipline
- Edit only the files named in the prompt. Do not touch adjacent files "for consistency."
- Do not refactor working code. Do not clean up while you are in there.
- No abstraction until the third repetition. Duplicate twice, extract on the third.
- No service layer, no repositories, no action classes, no DTOs. Eloquent goes in the Livewire component or controller.
- Validate inline with Livewire `$rules` or `$request->validate([...])`. No FormRequest classes.
- Do not install a Composer or npm package without asking.
- If the request is ambiguous, ask one question. Do not invent scope.
- Keep diffs small. One feature per response.

## Database (MySQL)
- After the schema freeze, migrations are additive only: new nullable columns and new tables. Never modify or drop an existing column.
- One developer owns each table. Do not write a migration against a table that was not named in the prompt.
- **Every schema change must ship with the matching seeder update in the same response.** The other developer recovers by running `migrate:fresh --seed`, so a schema change without seed data breaks their machine.
- The seeder is the demo: real names, plausible amounts, dates spread across the last two weeks, and at least one record in every visual state (pending, active, failed, empty).

## Background processing
Queues, jobs, events, observers, and broadcasting are allowed when a feature genuinely needs them, with these conditions:
- Ask before introducing one. State what it buys and what it costs.
- Default to `QUEUE_CONNECTION=sync` so dispatched jobs run inline with no worker process.
- If a real queue is needed, use the `database` driver and `queue:listen`, never `queue:work`.
- Anything needing a long-running process must be added to the `composer run dev` script so the app still boots with one command.
- Prefer an explicit method call over an observer unless the behaviour must fire from three or more places.

## Documentation lookups
- Use `search-docs` for Livewire and Filament version-specific syntax, where getting the API wrong costs a debugging cycle.
- Skip `search-docs` for routine Laravel CRUD, migrations, Eloquent, and Blade. The guidance you already have is sufficient and the lookup is not free.

## Formatting
Run `vendor/bin/pint --dirty --format agent` before finalizing any change that touched PHP. Two developers with inconsistent formatting generate merge conflicts that are pure waste.

## UI
- Tailwind. One accent colour, one font family, generous whitespace. No multi-stop gradients.
- Every list needs an empty state and a `wire:loading` skeleton, not a spinner.
- Never use lorem ipsum, "Test Item 1", or placeholder names in any view, seeder, or example. Real content only.
- Build the product screen, not a landing page.
