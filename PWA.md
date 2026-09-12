# PWA and mobile UI — working document

Requested explicitly by Sumair, same footing as `MILESTONE.md`. CLAUDE.md pre-approves
only `FLOW.md`, so this file exists by direct request.

**Scope: this piece of work only.** `MILESTONE.md` stays the overall status file for the
build. This links to it rather than repeating it.

**If you are a new session, start here.** Read this file, then continue from the first
unticked box. Do not re-plan and do not re-ask the decisions below.

---

## Why

Around 70% of traffic will be on a phone and nobody will open this on a laptop. Opened on
a phone today it is a website: browser chrome, an address bar, no navigation, and a back
gesture that leaves the app. The goal is that a Karachi resident installs it to their home
screen and cannot tell it from a native app.

## Decisions already made — do not re-ask

1. **No auth in this work.** No login, no registration, no accounts, no account tab. The
   Phase 2 accounts plan in `MILESTONE.md` stays untouched and unstarted.
2. **Two tabs only: Report and Help.** No history tab, because without accounts there is
   nothing reliable to list.
3. **Installable plus offline shell.** Manifest, maskable icons, standalone display, and a
   service worker caching the app shell. **Not** offline report queuing.
4. **`vite-plugin-pwa` is approved to install.** It is the only package install here, and
   the only `package.json` / `vite.config.js` change.
5. **Every emoji in the UI becomes a real icon.** Twelve of them, table below.

## Starting state, as verified on 12 September 2026

- Two citizen pages: the chat composer at `/` and the report page at `/reports/{id}`.
- No navigation of any kind anywhere in the app.
- No auth at all. No Breeze, no Fortify, no login route. Only Filament's own admin login.
- No manifest, no service worker, nothing PWA-related installed.
- `public/favicon.ico` is **zero bytes** and there is no logo, SVG or PNG anywhere in the
  repo. Icons have to be generated. PHP has GD available, Imagick is not.
- `reports` has no `user_id` column.

---

## Steps

### Step 0 — this file
- [x] Write `PWA.md`

### Step 1 — PWA foundation
- [x] `npm install -D vite-plugin-pwa`
- [x] Configure `VitePWA` in `vite.config.js`
- [x] Manifest served from the site root, linked from `app.blade.php`
- [x] Icons generated: 192 and 512 in both any and maskable, 180px apple-touch, plus a
      real `favicon.ico` and `favicon.svg` replacing the zero-byte placeholder
- [x] Head tags in `app.blade.php`
- [x] `InstallPrompt` component

**Three real traps hit here. Do not undo these.**

1. **The manifest cannot be plugin-generated.** Laravel's Vite plugin pins `outDir` to
   `public/build`, and the manifest is emitted as a build asset, so it lands at
   `/build/manifest.webmanifest` whatever `outDir` the PWA plugin is given. It is a
   committed static file at `public/manifest.webmanifest` with `manifest: false` on the
   plugin, which now only generates the worker.
2. **The service worker is registered by hand.** The plugin's own `registerSW.js`
   registered `/build/sw.js` with scope `/build/`, and a worker scoped there controls no
   page a citizen ever visits. `injectRegister` is `null` and `app.blade.php` registers
   `/sw.js` at root scope inside `@production`.
3. **`navigateFallback` must be an explicit `undefined`.** Omitting it is not enough: the
   plugin hard-defaults it to `index.html` (see its `defaultWorkbox`), and the generated
   handler calls `createHandlerBoundToURL`, which throws `non-precached-url` on activation
   and takes the entire worker down. Every page here is server-rendered so there is no
   document to precache as a fallback. Setting only `navigateFallbackDenylist` re-triggers
   it. Navigations are handled by a NetworkFirst runtime rule instead.

Verified: manifest, worker and all five icon files serve 200; the built worker contains
two runtime routes, no `NavigationRoute`, no `createHandlerBoundToURL`, and parses under
`node --check`. Generated worker files are gitignored alongside `public/build`.

### Step 2 — App shell and bottom navigation
- [x] `AppLayout.jsx` rewritten as a fixed-height shell
- [x] `BottomNav.jsx`, two tabs, centred rather than stretched
- [x] Tab bar inside the flex column, never `position: fixed`

The shell takes `header`, `footer` and `padded` props. Both are rendered outside the
scroll region and stay put: the compose screen passes its composer bar as `footer`, the
report screen passes its sticky send actions. The old `bare` prop is gone — every screen
now uses the same shell. When a `footer` is present the scroll region gets extra bottom
padding, or the last section reads as cut off underneath it.

### Step 3 — Replace every emoji with a real icon
- [x] `Icon.jsx`, one component with an internal path map, plus an `AppMark` export
- [x] All twelve replaced, table below
- [x] The send-arrow SVG folded into the map

Verified in a headless render of the report page: ten stroked icons and one AppMark, each
with real path data, no console errors.

`currentColor` is the entire point. An emoji cannot dim with a disabled button or turn
teal on an active tab; an inline SVG does both for free.

| File | Line | Emoji | Icon |
|---|---|---|---|
| `Create.jsx` | 133 | 🏙 | app mark in the chat header avatar |
| `Create.jsx` | 182 | ✕ | `x-mark` on the remove-photo button |
| `Create.jsx` | 237 | 📎 | `paper-clip` on the attach button |
| `Show.jsx` | 164 | 📍 | `map-pin` in the resolved-area chip |
| `Show.jsx` | 165 | 🎙 | `microphone` in the voice chip |
| `Show.jsx` | 178 | 🔍 | `magnifying-glass` on the needs-review panel |
| `Show.jsx` | 200 | ✅ | `check-circle` on the routed headline |
| `Show.jsx` | 269 | ☎ | `phone` on the tel link |
| `Show.jsx` | 278 | ✉ | `envelope` on the mailto link |
| `Show.jsx` | 289 | 🔗 | `link` on the website link |
| `Show.jsx` | 355 | ✓ | `check` in the copied state |
| `VoiceNoteInput.jsx` | 190 | 🎙 | `microphone` in the compact mic pill |

Line numbers are from before any edits and will drift. Use the sweep in Step 5 as truth.

The 🏙 in the chat header is standing in for a logo the app does not have. Use the same
mark as the PWA icon so the home-screen icon and the in-app avatar match.

Do not sweep `MILESTONE.md` or code comments. Documentation emoji are not the problem.

### Step 4 — UI pass
- [x] `Show.jsx` single vertical flow, grouped sections, app header with a back arrow
- [x] Sticky action bar above the tab bar
- [x] `Create.jsx` shell fit, no double padding
- [x] `Pages/Sumair/Help.jsx` plus one Inertia closure route in `routes/sumair.php`
- [x] `app.css` resets and the `pulse` keyframe

**Two defects fixed while in there**, both in Maisam's lane and both presentation-level:

- **The `pending` blank page.** This was item 2 in `MILESTONE.md`, the only real defect
  left in the build. `Show.jsx` branched on four statuses and `pending` — the column
  default and the value `store()` creates every report with — was not one of them, so the
  page rendered the chips and then nothing. It now shows a processing state reusing the
  compose screen's typing indicator. An unknown future status falls through to the same
  branch. Verified by creating a pending report inside a transaction and reading the
  Inertia props back.
- **The null-draft guard.** `draft_en` and `draft_ur` can both be null on a `drafted`
  report; copy and WhatsApp coalesced to an empty string, so a citizen could copy nothing
  and send an empty message. The action bar now hides when there is no draft, and the
  draft pane says which language is missing.

Loading feedback was also added to the clarify and category forms, item 3: both now
disable their input and swap the button label while the DeepSeek call is in flight,
instead of showing a frozen button.

### Step 5 — Verify
- [x] `vendor/bin/pint --dirty --format agent` passes, `npm run build` succeeds
- [x] Manifest, worker and all five icon files serve 200
- [x] Worker parses under `node --check`, contains two runtime routes and no
      `createHandlerBoundToURL`
- [x] Every page renders a full React tree with no console errors, checked headlessly
- [x] All four report states plus the new pending branch render
- [x] Emoji sweep clean
- [x] `/admin` still loads. Filament renders its own layout, so the blade edits do not
      reach it, and the worker excludes `/admin` from caching.
- [x] Screenshots at 390x844 for the composer, report, help, needs-review and AI-failure
      screens
- [ ] **Install to a real Android phone.** Not done — standalone mode, safe-area insets and
      the theme-coloured status bar only appear on real hardware, and emulation cannot
      confirm them. This is the one remaining check.

Not verified: offline behaviour and the install prompt firing. Both need a real browser
session over HTTPS or localhost with the worker actually activated.

Help screen content, real only, four sections: what this does; why routing in Karachi is
genuinely hard (a road can belong to a cantonment, a DHA scheme, a town municipal
corporation or the water corporation, and DHA City is a different scheme from DHA Phases
1 to 8); what happens after you send (the app drafts and hands it to you, it does not file
on your behalf); and the honest limit that 23 of the 41 authorities publish no phone or
email. Seed numbers: 41 authorities, 123 gazetteer nodes, 28 towns.

Every behaviour on `Show.jsx` is preserved exactly: language toggle, copy, WhatsApp,
mailto, clarify form, category picker, needs-review panel, contact empty state, RTL.

### Re-running the checks

```bash
php artisan serve
curl -sI http://localhost:8000/manifest.webmanifest | head -1   # expect 200
curl -sI http://localhost:8000/sw.js | head -1                  # expect 200
curl -s http://localhost:8000/ | grep -iE 'manifest|theme-color|apple-touch'
```

```bash
php artisan tinker --execute 'App\Models\Report::query()
    ->select("id","status")->get()->groupBy("status")
    ->each(fn($g,$s) => print($s.": ".$g->pluck("id")->implode(", ")."\n"));'
```

```bash
grep -rnP "[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{FE0F}]" resources/js
```

Standalone mode, safe-area insets and the theme-coloured status bar only show up on real
hardware. Emulation will not catch them, and they are exactly what this work is for.

---

## Two traps found while planning, both confirmed real

**`viewport-fit=cover` was missing.** `Create.jsx` already used
`env(safe-area-inset-bottom)` for its composer padding, but without `viewport-fit=cover`
on the viewport meta that resolves to zero on a notched iPhone. The safe-area code that
was already written did nothing. Fixed in `app.blade.php`.

**The `pulse` keyframe did not exist.** `VoiceNoteInput.jsx` and the typing indicator in
`Create.jsx` both set a raw `animation: 'pulse ...'` shorthand so they can stagger a
per-dot delay. Tailwind 4 defines the keyframe but only emits it when the `animate-pulse`
utility is used, and nothing used it. Confirmed by grepping the built stylesheet: **zero
`@keyframes` rules of any kind**. Both indicators had never animated. The keyframe is now
defined explicitly in `app.css` and is present in the build.

## Lane note

`Create.jsx`, `Show.jsx` and `AppLayout.jsx` are Maisam's files. This crosses lanes the
same way the voice-note work did, with Sumair's go-ahead. **Everything here is
presentation: no props change, no data flow changes, no controller, model, migration or
seeder is touched.** Record the crossing in `MILESTONE.md` when the work lands, so Maisam
is never surprised by a diff in his own files.

Shared files needing a heads-up: `app.blade.php`, `package.json`, `vite.config.js`,
`app.css`.

## Out of scope

- Auth, accounts, a history tab. Ruled out for this work.
- Offline report queuing. Submitting still needs a connection, because the AI pipeline runs
  inline on submit.
- Any controller, model, AI agent, routing pipeline, schema, seeder or Filament change.
- The open items in `MILESTONE.md`, including the `pending` blank-page defect. That is
  Maisam's and unrelated.

## Known environment issue

The `laravel-boost` MCP server fails to connect, so `search-docs` and `database-query` are
unavailable. Use `php artisan tinker --execute` for data checks.
