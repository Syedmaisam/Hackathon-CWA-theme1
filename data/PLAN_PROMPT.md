# PLAN_PROMPT.md

Paste the block below into Claude Code **in plan mode**, with `/model opusplan` and `/effort high`, as the first message of a fresh session. Do not let it write code from this prompt. The output is a plan you approve, then execute in a new session on Sonnet at `medium`.

---

```
You are planning a 4-hour hackathon build. Two developers, Maisam and Sumair.
Read CLAUDE.md first: the Hackathon Mode section supersedes conflicting Boost
guidance. Read karachi_civic_authorities_source_of_truth.md in full before
planning anything. Do not write any code in this session. Produce a plan.

## The brief

"The City Around You." When something breaks in Karachi (a burst main, an
uncollected pile of rubbish, sewage on the road, an encroached footpath), the
hardest part for a resident is knowing who is responsible. KMC, KWSC, SSWMB,
town administrations and cantonment boards each own different pieces, and
nothing tells a citizen which one to approach or how to word the complaint so
it gets acted on. Overlapping mandates: routing is the hard part.

Build an AI product that takes a citizen's raw report (photo, voice note or
typed description), determines what kind of civic issue it is, identifies the
responsible authority, and produces a properly formatted complaint ready to
send, in Urdu or English.

The AI must be load-bearing: classification from image and free text, routing
judgement across overlapping authorities, and formal document generation.

## Stack (fixed, do not propose alternatives)

- Laravel, MySQL, server-rendered. No API routes, no JSON endpoints, no SPA.
- Livewire 3 + Tailwind for the citizen-facing screens.
- Filament for the admin/operator panel.
- laravel/ai (the official Laravel AI SDK) with DeepSeek as the provider.
- Laravel Boost MCP is installed. Use database-schema, database-query and
  search-docs rather than reading files.

## Resolve these three risks before you plan anything else

These change the shape of the product. Answer each with evidence, not
assumption, and state the fallback if the answer is no.

1. Does the installed laravel/ai version support image input to DeepSeek?
   Run `composer show laravel/ai` for the installed version, then use
   search-docs for the vision and structured-output APIs of THAT version.
   Do not assume an API from memory. If DeepSeek cannot accept images,
   the fallback is: user uploads the photo, it is stored and displayed in
   the complaint and the admin panel, and classification runs on the typed
   or spoken description with the user confirming a suggested category.
   Say clearly which path the plan assumes.

2. Voice notes. DeepSeek does not provide speech-to-text. Options are the
   browser Web Speech API (free, no provider, supports ur-PK in Chrome),
   a second provider for transcription through laravel/ai, or dropping
   voice input. Pick one and justify it on 4-hour cost, not capability.

3. DeepSeek model names. `deepseek-chat` and `deepseek-reasoner` were
   retired on 24 July 2026. Current names are `deepseek-v4-flash` and
   `deepseek-v4-pro`, and both default to thinking mode ON. Confirm what
   the installed SDK sends and ensure thinking is disabled for latency.

## The one architectural rule that must survive the plan

**The LLM classifies and drafts. PHP routes.**

The model never names an authority. It returns a structured result against
closed enums. Deterministic PHP then looks up the authority from the
source-of-truth data. Reasons:

- A hallucinated helpline number shown to a citizen is the worst possible
  failure for this product, and this makes it structurally impossible.
- Routing becomes auditable and explainable on stage, which is the single
  most impressive thing you can show a judge for this brief.
- It is faster to build and cheaper to run than making the model reason
  over the whole authority table on every request.

Pipeline stages, and which is LLM and which is code:

  1. Input capture (photo / voice / text)               code
  2. Transcription if voice                             per risk 2
  3. Classification: issue_type + severity + hazards    LLM, closed enum
  4. Location extraction: landmark, block, road, area   LLM, free text out
  5. Location resolution: text -> gazetteer node        code
  6. Special-zone override check                        code, runs FIRST
  7. Authority lookup from routing table                code
  8. Confidence scoring                                 code
  9. Complaint drafting in EN and UR                    LLM, given 1-8
 10. Delivery: copy, WhatsApp link, mailto, portal      code

Stage 6 runs before stage 7 and before any district hierarchy walk. A
location inside a cantonment routes to that cantonment board regardless of
its district. DHA Phases 1-8 route to Cantonment Board Clifton.

## Source-of-truth rules (non-negotiable)

- Every authority, phone number, email and URL comes from
  karachi_civic_authorities_source_of_truth.md. Invent nothing.
- Anything marked UNVERIFIED or TODO must never be shown to a citizen as a
  confirmed contact. It may appear in the Filament admin, clearly labelled.
- Where the file says a case is genuinely ambiguous (encroachment, KMC vs
  TMC road ownership), the product returns two or three named recipients
  with a reason, not one confident guess. This is a feature. Surface it.
- Every routed result carries routing_confidence: high, medium, low, or
  needs_human_review. Low confidence asks the citizen ONE clarifying
  question rather than guessing.

## Scope

In scope, the demo spine:
  A. Citizen submits a report and gets a classified, routed, drafted
     complaint they can actually send. This is the whole product. It must
     work end to end before anything else is started.
  B. Filament admin showing reports grouped by area and issue type: the
     "what is actually broken in this neighbourhood" view. Use SQL grouping
     on (issue_type, resolved_area, time window). Do not use embeddings or
     vector similarity for deduplication; it is not worth the time and
     grouping demos identically.

Stretch, only if A and B are finished before T+150:
  C. Water tanker price check. The notified rates are already in the source
     of truth. One Livewire page: size, category, distance, and it shows the
     official rate against what the market typically charges. Nearly free
     because the data exists.

Explicitly out of scope. Do not plan any of it:
  - User accounts, login, registration, roles. Seed two demo users.
  - Real submission to any government system. Draft plus copy or share.
  - Embeddings, pgvector, RAG, semantic search.
  - GIS polygons or map boundary data. It does not exist publicly for the
    current delimitation; the gazetteer is a name graph.
  - Postal-code-based routing. Postal codes cut across districts.
  - SMS or email sending infrastructure.
  - Tests of any kind.

## Demo resilience

The demo must not depend on a live API call at showtime.
  - Cache every LLM response keyed on a hash of the input. CACHE_STORE=file,
    not array. A rehearsal run warms the cache so the real demo hits memory.
  - Seed at least two complete reports that already hold AI output, so a
    dead network still shows a working product.
  - Every AI call has a timeout and a graceful failure path that still
    produces a complaint from the deterministic layer alone.

## What the plan must contain

1. Answers to the three risks, with the chosen path and its fallback.
2. Data model: five tables maximum, with columns. Name the owner of each
   table (Maisam or Sumair). Say which table holds the gazetteer.
3. How the gazetteer and routing table get loaded: seeder from the markdown,
   a PHP config array, or a database table. Justify on speed to build and
   speed to change during the hackathon.
4. Screen list. For each: route, Livewire component or Filament resource,
   owner, and whether it is on the critical demo path.
5. The exact structured-output schema the LLM returns at stage 3 and 4,
   with the closed enum values for issue_type, severity and hazards. Take
   the issue types from the routing table in the source of truth.
6. The prompt strategy: one call or two, what goes in the system prompt,
   and roughly how many tokens the gazetteer adds if it is passed inline.
7. Seeder plan: how many reports, which areas, which issue types, and which
   visual states. Include at least one cantonment case, one DHA case, and
   one deliberately ambiguous encroachment case, because those three are
   what prove the routing is real.
8. A timeline against the 4-hour clock with a schema freeze at T+90 and a
   feature freeze at T+180.
9. A cut list, ordered: what gets dropped first, second, third if you are
   behind at T+120.

## What not to do in the plan

- Do not propose a service layer, repositories, actions or DTOs.
- Do not propose queues unless a stage genuinely needs the request to
  return early; say which and why. Default is QUEUE_CONNECTION=sync.
- Do not propose any test strategy.
- Do not design a schema beyond five tables.
- Do not plan authentication.

Ask me up to three questions if something is genuinely ambiguous. Otherwise
produce the plan.
```

---

## After the plan comes back

Check three things before approving:

1. **Did it actually run `composer show` and `search-docs`, or did it guess the SDK API?** The Laravel AI SDK is at 0.x and its API is not in any model's training data reliably. A plan built on a guessed API is a plan built on sand.
2. **Is stage 6 before stage 7?** If the override check is not evaluated first, DHA and every cantonment routes wrong, and those are the cases that prove the product.
3. **Does the cut list start with C, then B, then the voice input?** If it proposes cutting anything in A, the scope was not understood.

Then start a fresh session, `/model` to Sonnet, `/effort medium`, and execute one slice at a time with `/clear` between them.
