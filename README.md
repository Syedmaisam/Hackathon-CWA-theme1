# The City Around You

When something breaks in Karachi, a burst main, an uncollected heap of
rubbish, a dead street light, most people know it is broken and have no idea
who is supposed to fix it. The city is split between a dozen authorities whose
boundaries do not match the ones residents use, and the wrong complaint sent to
the wrong desk is the same as no complaint at all.

This app takes a complaint in plain Urdu or English, works out who actually
owns the problem, and writes the letter for you to send.

---

## What it does, end to end

A citizen opens the app and does three things: picks where they are, describes
what is wrong, and sends it. Everything after that is automatic.

**1. They pick a location.** A search box over 27 town municipal corporations,
157 union councils, and 73 named landmarks. Typing `fb area` finds Gulberg,
`disco` finds Disco Bakery in Gulshan-e-Iqbal, `boat basin` finds the one in
Saddar. This is a deliberate design decision: the picked place, not the text,
decides which authority gets the complaint. A citizen who mistypes a street
name still reaches the right desk.

**2. They describe the problem.** By typing, by speaking, or by attaching a
photo. Urdu, English and Roman Urdu all work. A voice note is transcribed in
the browser and appended to the same text field a typed report uses, so every
later stage is identical no matter how the complaint arrived.

**3. The pipeline runs**, inline, in about seven seconds:

| Stage | What happens |
|---|---|
| Classify | The model reads the text and returns an issue type, severity, hazards, detected language and a plain summary |
| Resolve location | The picked place is pinned to the gazetteer; free text is only used for reports that arrive without a pick |
| Apply routing rule | The issue type is looked up against the routing table to find the owning authority |
| Score confidence | High, medium, low, or needs a human |
| Draft | The model writes the complaint in both English and Urdu, addressed to the authority, with a request for a reference number |

**4. They get a letter they can send.** English and Urdu side by side, with
buttons for WhatsApp, copy, and email. The Urdu is right-to-left in Nastaliq,
not a Latin-font approximation.

---

## The parts that make it trustworthy

**It asks instead of guessing.** When the routing is low confidence, the app
stops and asks a single clarifying question. "Water standing in the street" is
genuinely ambiguous, rain water is the storm drain's problem and sewage is the
water corporation's, so the app asks which it is rather than picking one.

**It escalates when an authority is unreachable.** Twenty-seven of the
thirty-four seeded authorities publish no verified phone or email. Those
complaints carry a second recipient, the city's 1339 helpline, which logs and
forwards them.

**It says when it does not know.** A vague complaint routes to the federal
citizen portal rather than being forced into a category. If the model fails
entirely, the citizen gets a category picker and a template-generated draft, so
a network failure still produces a sendable letter.

**It flags unverified contacts.** Town corporation numbers came from a scraped
directory and are labelled as unverified wherever they appear.

---

## Scope

Karachi's twenty-seven town municipal corporations. Cantonment boards and DHA
are deliberately out of scope, they are separate jurisdictions with their own
complaint channels, and a confident wrong answer there is worse than no answer.
The override mechanism for special zones is still in the code, only the data
was removed.

Union council lists exist for sixteen of the twenty-seven towns. The remaining
eleven route at town level.

---

## Running it

**You need** PHP 8.3, Composer, Node, and a running MySQL server.

```
git clone <repo> && cd CWA-Hackathon
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Now edit `.env`. Two things must change, because the shipped example will not
work as-is:

```
DB_CONNECTION=mysql        # the example still says sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hackathon      # create this database first
DB_USERNAME=root
DB_PASSWORD=

DEEPSEEK_API_KEY=          # required, the pipeline calls it on every submit
```

Then create the schema and start the app:

```
php artisan migrate:fresh --seed
composer run dev
```

`composer run dev` starts the PHP server, the queue listener, the log tailer
and Vite together, so the whole app boots with one command.

**Where things are:** the citizen app is at `/`, the admin at `/admin`, signing
in as `admin@cityaround.pk` / `password`.

**What gets seeded:**

| | count |
|---|---|
| Demo reports | 14 |
| Gazetteer places | 264 |
| Authorities | 34 |
| Routing rules | 13 |

The 14 reports deliberately cover every visual state, so every screen has
something real to show without submitting anything.

### If something is not working

- **A blank page or a Vite manifest error** means the frontend is not built.
  Run `npm run build`, or use `composer run dev` which watches instead.
- **Reports all land in the AI-failed state.** The DeepSeek key is missing or
  wrong. The app is designed to survive this: you still get a
  template-generated draft and a category picker.
- **The other developer's machine looks different after a pull.** Re-run
  `php artisan migrate:fresh --seed`. Seed data changes often and the app
  assumes the current set.
- **Voice input does nothing.** It uses the browser speech API, which is
  Chrome and Edge only. iOS Safari has no support and the app says so.

---

## The admin side

A Filament panel for the people who maintain the routing, gated behind an
`is_admin` flag so a citizen account cannot reach it.

- **Reports** open as a triage queue. Anything still needing a human floats to
  the top, newest first inside that. Opening one shows the complaint, the
  authority it reached with contact details and the rule that chose it, and
  both drafts.
- **Authorities, routing rules and the gazetteer** are all editable, so a
  wrong routing decision is a data fix rather than a deploy.
- **Two dashboard widgets**: what is breaking this week by area, and routing
  health, including how many authorities lack a citizen-facing channel.

---

## How it is built

Laravel 13 and PHP 8.3 on MySQL, with Inertia and React for the citizen app and
Filament 5 for the admin. Classification and drafting run against DeepSeek.
There is no queue: the pipeline runs inline on submit, which keeps the whole
thing to one command to boot.

It is an installable progressive web app, because most of this traffic will be
on a phone. App shell, bottom navigation, offline shell, and a real install
prompt.

Four tables carry everything:

| Table | Holds |
|---|---|
| `reports` | Every complaint and its pipeline state |
| `authorities` | Who can be written to, and how reachable they are |
| `routing_rules` | Which issue type belongs to which authority |
| `gazetteer_nodes` | The place graph: districts, towns, union councils, landmarks |

---

## Other documents

- `TESTING.md` — prompts to exercise every issue type, language and edge case
- `MILESTONE.md` — what is done, what is left, and who owns it
- `PWA.md` — the progressive web app work and what still needs a real phone
- `CLAUDE.md` — working rules for this build
