# Karachi Civic Complaint Routing — Working Source of Truth

**Purpose:** A human-reviewable reference for an AI that classifies Karachi civic reports and routes them to the most likely responsible authority.

**Status:** Working dataset for hackathon use, not a legal jurisdiction register.

**Last reviewed:** 12 September 2026
**Revision:** v2. Cantonments and DHA added. Streetlight, debris, and SSWMB channel corrections applied. Five missing towns added.

## Data-use rules

- Use the authority named in this file as the **primary routing target**, not as a guarantee that it has legal jurisdiction in every location.
- Preserve the original report, photo, voice note, exact location, nearest landmark, and timestamp in every complaint.
- Do not invent missing emails, phone numbers, websites, or town boundaries.
- Fields marked `UNVERIFIED` or `TODO` must not be shown to a citizen as confirmed contact information.
- A town/TMC contact is a supplementary destination. For water, sewerage, garbage, electricity, and cantonment cases, sector authority/location takes precedence.
- **Special-zone overrides beat district hierarchy.** If a location resolves inside a cantonment, that cantonment board is the primary route regardless of which district it sits in.
- Before production use, re-check all contacts against the authority's official website or an official call centre.

## Entity names

- Use **Karachi Water & Sewerage Corporation (KWSC)** as the current name. `KWSB` is a historic/legacy name still used by residents and still live on some official infrastructure, including the legacy `kwsb.gos.pk` domain.
- Use **Sindh Solid Waste Management Board (SSWMB)** for municipal solid-waste complaints.
- Use **Karachi Metropolitan Corporation (KMC)** for metropolitan municipal functions.
- Use **Town Municipal Corporation (TMC)** for a town-level municipal body.
- Use **Cantonment Board** plus the board name, e.g. "Cantonment Board Clifton (CBC)".

## City-wide authority directory

| Authority | Primary responsibility for routing | Official/known website | Email | Phone / complaint channel | Verification status | Notes |
|---|---|---|---|---|---|---|
| Karachi Metropolitan Corporation (KMC) | Major/arterial roads, KMC assets, parks/amenities, major storm-water drains/nullahs, main-road encroachments, **street lights and street-light poles**, construction debris (malba) removal | https://kmc.gos.pk | mayor@kmc.gos.pk **UNVERIFIED** | **1339** (Citizens Complaints Information System); office +92 21 992 1511-7 | 1339 and office number verified from KMC site and public reporting; email needs confirmation | Head office: 1st Floor, M.A. Jinnah Road, Karachi. E-KMC: https://kmc.gos.pk/e-kmc/. 1339 is publicly documented as covering sewage complaints, road potholes and non-functional street lights. |
| Karachi Water & Sewerage Corporation (KWSC) | Water supply, burst mains, leaks, sewer overflow, sewer blockage, sewer manholes, official tanker service | https://www.kwsc.gos.pk | info@kwsb.gos.pk (legacy domain, **UNVERIFIED**) | **1334** (24/7 complaints); HQ **(+92) 021 111 597 200**; tanker booking via KWSC Unified app | 1334, HQ number and address verified from official KWSC site and public reporting; email needs confirmation | HQ: 9th Mile Karsaz, Main Shahrah-e-Faisal, Karachi-75350. "KWSC Unified" single-app system went live 1 Feb 2026. Do not guess a `@kwsc.gos.pk` address; the published one is on the legacy `kwsb` domain. |
| Sindh Solid Waste Management Board (SSWMB) | Municipal garbage, missed collection, overflowing waste, street sweeping, illegal dumping | https://sswmb.gos.pk | TODO | **1128** (24/7, app-integrated) | 1128 verified: inaugurated with enhanced 24/7 service, 32 lines and app integration in June 2025, still in active public use through 2026 | HQ: 3rd Floor, DMC (South) Building, opposite Aram Bagh Police Station, near Haqqani Chowk, District South, Karachi-74200. Office +92 21 99333710-03. **Does not handle construction debris or building material, only garbage.** Do not route storm-water drain desilting here. |
| K-Electric (KE) | Electricity faults, outages, transformers, sparking, dangerous or exposed wires | https://www.ke.com.pk | customer.care@ke.com.pk | **118** | Publicly listed customer channels; re-check before production | **Street lights are KMC, not KE.** Route a dead streetlight to 1339 and route live/sparking wires to 118. Cantonments and managed estates may run their own supply. |
| Sindh Building Control Authority (SBCA) | Illegal or unauthorised construction, building-plan violations, parking areas converted to godowns/shops, builder debris on public land | TODO: official URL to verify | TODO | TODO | Mandate verified from public reporting; contact channel not yet verified | Repeatedly named by the Sindh CM as the authority for builder undertakings, illegal commercial conversion, and plaza parking restoration. Co-route with KMC/DC for builder-generated encroachment and debris. |
| Deputy Commissioner / Assistant Commissioner (district administration) | Encroachment enforcement support, anti-encroachment operations, escalation when local body does not act | TODO | TODO | TODO | Role verified from public reporting; per-district contacts not verified | Assistant commissioners were tasked with compiling encroachment data on footpaths, roads and green belts. Name the DC office as a co-recipient on encroachment complaints, never as sole recipient. |
| Pakistan Citizen Portal (PMDU) | Escalation/fallback when ownership is unclear or local authority does not respond | Government mobile application / portal | N/A | App/portal submission | Fallback channel | Federal grievance system routing to federal and provincial organisations. Do not claim it is a direct emergency response service. |
| Relevant Cantonment Board | **All** municipal problems inside a cantonment: roads, sanitation, waste, parks, water, electricity, building control | See cantonment table below | Per-board, TODO | Per-board complaint cell, TODO | Boards and websites verified; complaint channels not yet verified | Cantonments maintain their own water supply and electricity and are outside KMC jurisdiction. Route by verified cantonment location, not by neighbourhood name alone. |
| DHA Karachi | Estate/allotment matters, internal scheme development | See DHA rule below | TODO | TODO | Municipal services are CBC, not DHA | **Municipal complaints in DHA go to Cantonment Board Clifton.** See the DHA rule below before routing anything here. |

## Cantonment boards (special-zone overrides)

There are **six** cantonment boards in Karachi. Each maintains its own water supply and electricity infrastructure and sits outside the jurisdiction of KMC and the city district government. A location resolving inside any of these overrides the normal district/town route.

| Cantonment Board | Official website | Area / notes | Verification |
|---|---|---|---|
| Cantonment Board Clifton (CBC) | https://cbc.gov.pk | Created by Notification SRO No. 207(1)/(83) dated 27-02-1983 to provide municipal cover to **8 DHA phases**, **12 katchi abadis** in the periphery, and **Blocks 8-9 of Clifton**. Area 51.327 km². Bounded by Karachi Cantonment north and west, city district east, Arabian Sea south. | Website and jurisdiction statement verified from CBC official site |
| Karachi Cantonment Board | https://www.cbkarachi.gov.pk | Established August 1942. Covers Saddar-area cantonment lands. Population 86,338 (2023). | Website from Wikipedia infobox; **confirm the live site before citizen-facing use** |
| Cantonment Board Faisal | https://cbfaisal.gov.pk | Formerly Drigh Road Cantonment, established 4 Nov 1925. Class I since 2002. Population 292,196 (2017). Postal code 74350. Runs its own Water Supply Branch, Electric Branch and Building Control Cell. | Website from Wikipedia infobox; **confirm live site** |
| Cantonment Board Malir | https://www.cbmalir.gov.pk | Established 11 Oct 1948. Area 42 km². Population 217,489 (2023). Postal code 75070. Class I; provides its own waste management, water supply, healthcare and education. Governed under Cantonment Act 1924, president is the Station Commander. | Website from Wikipedia infobox; **confirm live site** |
| Cantonment Board Korangi Creek (CBKC) | https://korangi.cantonment.gov.pk | Notified Dec 1968. Current area 6 sq miles (reduced from 9 in 1973). Autonomous body under Military Lands and Cantonment Department, Ministry of Defence. Sits within Korangi Town. Population 57,745 (2017). | Website and constitutional status verified from CBKC official site |
| Cantonment Board Manora | https://www.cbmanora.gov.pk | Manora Island, south of Karachi harbour. Class II, naval. Restricted area, high naval presence. | Website from Wikipedia infobox; **confirm live site** |

**Note on domain patterns:** Korangi Creek uses `korangi.cantonment.gov.pk` while the others use `cb<name>.gov.pk`. Do not construct a URL for any board by pattern. Only use a URL after loading it.

## DHA rule (do not get this wrong)

DHA Karachi is **not** the municipal authority for its own residential phases.

- **Municipal complaints inside DHA Phases 1-8** (water, sewerage, roads, sanitation, street lights, parks, building control) route to **Cantonment Board Clifton**, which was constituted specifically to provide municipal cover to those phases.
- **DHA itself** handles estate matters: allotment, transfer, membership, scheme development. Not civic faults.
- **Clifton Blocks 8 and 9** also fall under CBC. Other Clifton blocks may fall under the city district. Do not assume all of "Clifton" is one jurisdiction.
- **DHA City Karachi** on the Super Highway is a separate scheme from DHA Phases 1-8 and is **not** covered by this rule. Flag as `needs_human_review`.
- Postal code 75500 is commonly listed across all DHA phases and can be used as a **weak supporting signal** for the CBC override. Never as the sole basis.

## High-confidence routing rules

| Resident issue | Primary route | Secondary route / decision rule |
|---|---|---|
| Garbage not collected, overflowing bin, litter pile, illegal solid-waste dumping | SSWMB — **1128** | If inside a cantonment, route to that board instead. |
| **Construction debris, malba, building material dumped on road or footpath** | KMC — 1339, or mapped TMC for an internal street | **Never SSWMB.** SSWMB has publicly stated debris and construction material are not its job. If a builder or under-construction building is the source, co-route to SBCA. |
| Sewage overflow, choked sewer, sewer smell, open/broken sewer manhole | KWSC — **1334** | Include clear photo and exact map pin. If it is visibly a storm-water drain rather than a sewer, route to KMC/TMC instead. Inside a cantonment, route to that board. |
| Burst water main, water leak, no water, low pressure, dirty water | KWSC — **1334** | Ask for consumer account number if available; never require it to submit an emergency complaint. Cantonments run their own supply: route there. |
| Official water tanker booking / official tanker tariff | KWSC Unified app or 1334 | Clearly label official KWSC rates separately from private-market quotes. See tanker tariff section. |
| Pothole, broken local street, damaged local footpath | Mapped TMC | If it is a KMC arterial road/flyover or a cantonment road, route accordingly. |
| Major road, flyover, bridge, underpass, KMC-controlled footpath | KMC — 1339 | Ask for road name and nearest landmark; KMC versus TMC road ownership is genuinely ambiguous and there is no public register. Log as `road_ownership_uncertain`. |
| **Streetlight not working, dark street** | **KMC — 1339** | 1339 is publicly documented as covering non-functional street lights. Only route to K-Electric if the fault is supply-side. Cantonments and managed estates run their own lighting. |
| **Sparking wire, exposed cable, leaning pole, transformer fault** | K-Electric — **118** | Mark `severity: emergency` and `hazards: exposed_wire`. |
| Main-road encroachment | KMC — 1339, **co-route to DC office** | Internal street encroachment goes first to mapped TMC. Cantonment locations go to that board. Encroachment has no single owner: the Sindh CM has publicly stated that no authority was ready to remove them, and directed KMC, TMCs, police, district administration and development authorities jointly. Always return `multi_agency_possible` and name two or three recipients. |
| Illegal construction, unauthorised floors, parking converted to shops/godowns | SBCA | Co-route to KMC/TMC if it also obstructs a public road or footpath. |
| Blocked storm-water drain, nullah flooding, desilting | KMC first | Some major drains involve provincial/irrigation bodies; log as `multi_agency_possible`. Do not route as ordinary garbage to SSWMB. |
| KMC park, beach, zoo, safari park, KMC amenity | KMC | Verify whether the specific park/amenity is actually KMC-managed; some have been transferred between KDA and KMC. |
| **Any municipal problem inside a confirmed cantonment** | **Relevant Cantonment Board** | This overrides every row above. Cantonments run their own water, electricity, sanitation and building control. |
| **Any municipal problem inside DHA Phases 1-8** | **Cantonment Board Clifton** | See DHA rule. |
| Unknown or mixed report | Ask one clarifying question; then use Pakistan Citizen Portal as a fallback | Never silently discard a report merely because jurisdiction is uncertain. |

## Water tanker tariff reference

Last publicly notified rates. **Treat as "last notified", not "current"**: KWSC constituted a committee in mid-2026 to revise tanker tariffs before the next hydrant contract round, citing diesel costs.

| Item | Rate |
|---|---|
| General Public Service (GPS) tanker, 1,000 gallons | Rs 1,560 |
| Commercial tanker, 1,000 gallons | Rs 3,120 |
| GPS delivery beyond 10 km | + Rs 67.28 per km |
| What the hydrant contractor pays KWSC per 1,000-gallon GPS tanker | Rs 600 |

Notes for the price-transparency feature:
- Residents widely report paying several times the notified rate, and there are far more unauthorised hydrants than authorised ones. The gap between notified and market price is the product insight, not a data error.
- KWSC categories are **General Public Service**, **Commercial**, and **Free Tanker Service** (mosques, public institutions).
- Citizens should obtain a computerised **e-slip** for every official tanker, and the absence of an e-slip is itself a complaint ground.
- Do not present a market quote as an official rate, or vice versa. Label both.

## Required input fields for AI routing

```yaml
report_id: string
input_language: ur | en | roman_urdu | mixed
raw_text: string
media: [] # photo, video, voice note
issue_labels: [] # e.g. garbage, sewer_overflow, pothole, malba_debris, streetlight
address_text: string
latitude: number|null
longitude: number|null
nearest_landmark: string|null
road_name: string|null
town_name_claimed_by_user: string|null
cantonment_or_estate_claimed_by_user: string|null
resolved_special_zone: string|null   # cantonment/DHA/SITE/port, set by the override layer
date_time_observed: string|null
severity: low | medium | high | emergency
hazards: [] # child_risk, traffic_blocked, exposed_wire, flooding, etc.
contact_preference: call | whatsapp | email | portal
```

## Complaint generation minimums

Every generated complaint must contain:

1. Recipient authority and channel.
2. Specific issue classification.
3. Full available location, map pin, nearest landmark, and road name where possible.
4. Start date/time or `unknown`.
5. Harm/urgency: health risk, traffic obstruction, flooding, child safety, exposed electrical hazard, etc.
6. Requested remedy: collect waste, clear sewer, cover manhole, repair pipe, repair road, remove encroachment, repair streetlight.
7. List of attachments.
8. Citizen phone/contact only with consent.
9. A request for complaint/reference number and expected resolution time.

## Town Municipal Corporations directory

> **Count status:** Sources disagree on whether Karachi has 25 or 26 TMCs, and UC counts per town also differ between sources. The list below is names only. Do not present it as a complete legal register.

> **Unresolved:** `TMC Jamshed` and `TMC Jinnah` may be the same town under two names. Public sources describe Jinnah Town as drawn from the Ferozabad and Jamshed Quarters subdivisions, while other reporting names Jamshed Town in the same District East slot. Resolve before shipping; do not route to both.

| TMC / town | District (claimed) | Website | Email | Phone | Contact confidence | Notes |
|---|---|---|---|---|---|---|
| TMC Saddar | South | TODO | TODO | TODO | Missing | |
| **TMC Lyari** | **South** | TODO | TODO | TODO | **Missing — newly added** | Was absent from v1. High complaint density, 11-13 UCs. Priority for gazetteer coverage. |
| TMC Jamshed | East | TODO | TODO | TODO | Missing | See Jamshed/Jinnah note above. |
| TMC Jinnah | East | TODO | TODO | 021-992131355-59 **UNVERIFIED** | Unverified | Number from a public document snippet; confirm from an official TMC source. See Jamshed/Jinnah note. |
| TMC Gulshan-e-Iqbal | East | No stable official site confirmed; public X account: https://x.com/tmc_gulshan | TODO | TODO | Social presence only | Do not treat social media as an official complaint channel. |
| **TMC Chanesar** | **East** | TODO | TODO | TODO | **Missing — newly added** | Was absent from v1. |
| **TMC Sohrab Goth** | **East** | TODO | TODO | TODO | **Missing — newly added** | Was absent from v1. |
| **TMC Safoora** | **East** | TODO | TODO | TODO | **Missing — newly added** | Was absent from v1. |
| TMC Nazimabad | Central | https://tmcnazimabad.gos.pk | tmcnazimabad25@gmail.com | 021-99260342; 0312-1117088 | Official TMC site | Office near Gujjar Nala, Shahrah-e-Ibn-e-Sina Road, Nazimabad No. 2. |
| TMC North Nazimabad | Central | https://tmcnorthnazimabad.gos.pk | info@tmc-nn.gos.pk | 0213-99260366 | Official TMC site | Head office: ST-04, Nazim Street, Block-A, North Nazimabad, opposite Sindh Rangers Hospital. |
| TMC Liaquatabad | Central | TODO | TODO | TODO | Missing | |
| TMC Gulberg | Central | TODO | TODO | TODO | Missing | |
| TMC New Karachi | Central | https://tmcnewkarachi.gos.pk | tmcnewkarachi@gmail.com | 0300-8995331; 0300-7001714 | Official TMC site | Site also displays 0313-9299666; verify role before showing as complaint number. |
| TMC Orangi | West | TODO | TODO | TODO | Missing | |
| TMC Mominabad | West | TODO | TODO | +92 345 2154417 **UNVERIFIED** | Unverified | Confirm directly with the TMC. |
| TMC Manghopir | West | https://www.tmcmangopir.gos.pk | tmc@gmail.com **UNVERIFIED / generic** | 0300-9231641 | Weak | Do not use a generic gmail address for automated submissions. |
| **TMC Keamari** | **Keamari** | TODO | TODO | TODO | **Missing — newly added** | Was absent from v1. |
| TMC SITE | Keamari | TODO | TODO | TODO | Missing | Do not confuse with SITE industrial association contacts. |
| TMC Baldia | Keamari | https://tmcbaldia.gos.pk (observed) | TODO | TODO | Website only | Validate site is current and extract its contact page. |
| TMC Korangi | Korangi | TODO | TODO | TODO | Missing | |
| TMC Landhi | Korangi | TODO | TODO | TODO | Missing | |
| TMC Shah Faisal | Korangi | TODO | TODO | TODO | Missing | |
| TMC Model Colony | Korangi | TODO | TODO | TODO | Missing | |
| TMC Malir | Malir | TODO | TODO | TODO | Missing | Some sources render this as "Malik Town"; treat as the same entity pending confirmation. |
| TMC Gadap | Malir | https://tmcgadap.gos.pk | TODO | TODO | Website reported | Validate site and obtain contact page. |
| TMC Ibrahim Hyderi | Malir | TODO | TODO | TODO | Missing | |
| TMC Bin Qasim | Malir | TODO | TODO | TODO | Missing | |

## Geography approach: gazetteer, not polygons

**Do not attempt to source GIS polygons for TMC or UC boundaries.** The only public UC boundary dataset was compiled in 2017 from data mining of public sources, and its own publisher describes it as the only public version until a more authentic one appears. It predates the 2022 delimitation that created the current towns and 246 union committees, so it is structurally wrong for this build. Other circulating Karachi shapefiles cover the pre-2011 31-town system.

**Do not use postal codes as a routing key.** They are Pakistan Post delivery catchments and they cut across districts. Machar Colony in Keamari and Gulshan-e-Iqbal in District East are both listed as 75300. Sources also disagree with each other on the same area, for example Malir appearing variously as 75050, 75080 and 75000. Postal code is a **weak supporting signal only**, useful mainly for the DHA 75500 override.

**Build a name graph instead.** Residents type landmarks, blocks and chowrangis, not coordinates.

```
District (7, stable)
  └── Town / TMC (25-26, names mostly agree, count contested)
       └── UC (~246, names usable, boundaries unavailable)
            └── Landmark / neighbourhood / block / sector  ← what users actually type

SpecialZone (6 cantonments, DHA phases, SITE, Port, Steel Town, highways)
  └── overrides_jurisdiction, evaluated BEFORE the district hierarchy
```

Edges: `contains`, `alias_of`, `overrides_jurisdiction`, optionally `adjacent_to` for "near X" inputs.

Priority order for gazetteer coverage: the six cantonments and DHA first, since they are the highest-impact overrides, then Lyari and Keamari, which were missing entirely from v1.

## Data gaps: do not fabricate

Intentionally incomplete because no reliable public source was found:

- Official website, email, public complaint number, and office address for most TMCs.
- A single authoritative, current mapping of every Karachi locality/UC to a TMC.
- A verified current count and district assignment for all TMCs (25 vs 26 unresolved).
- Whether Jamshed Town and Jinnah Town are one entity or two.
- Per-board complaint cell numbers and email addresses for all six cantonment boards.
- A canonical list of KMC arterial roads versus roads maintained by each TMC. This is the largest single gap and the main source of routing ambiguity for potholes.
- A verified KMC anti-encroachment complaint channel.
- A verified SSWMB email address and official web contact page.
- Verified SBCA and DC/AC complaint channels.
- Current tanker tariff following the mid-2026 revision committee.

## Recommended data model

Keep this Markdown as a review document, but make the app read from a structured file such as `authorities.json` or a database table.

```yaml
authority:
  id: kwsc
  name: Karachi Water & Sewerage Corporation
  aliases: [KWSC, KWSB, Karachi Water Board, واٹر بورڈ]
  issue_types: [water_supply, water_leak, burst_main, sewer_overflow, open_sewer_manhole, tanker]
  contacts:
    - channel: phone
      value: '1334'
      verified: true
      last_verified: '2026-09-12'
      source_url: 'https://www.kwsc.gos.pk/'
  jurisdiction_rule: citywide_except_special_zone_overrides
  confidence: high

special_zone:
  id: cb_clifton
  name: Cantonment Board Clifton
  aliases: [CBC, Clifton Cantonment, DHA Karachi, Defence]
  covers: [dha_phase_1..8, clifton_block_8, clifton_block_9, periphery_katchi_abadis]
  overrides: [kmc, kwsc, sswmb, ke]
  overrides_note: 'Cantonment runs its own water, sewerage, sanitation and electricity'
  confidence: high
```

## Recommended verification workflow

1. Add `source_url`, `source_type`, `last_verified`, and `verified_by` to every contact.
2. Verify every phone number by calling it and recording the date/result; verify emails via an official contact page.
3. Treat a `.gov.pk` or `.gos.pk` domain as stronger evidence than social media, directories, news items, or user-submitted lists. Note that cantonment boards are federal (`.gov.pk`) while Sindh local bodies are provincial (`.gos.pk`).
4. Resolve jurisdiction through the special-zone override layer **before** the district hierarchy.
5. Keep a `routing_confidence` output: `high`, `medium`, `low`, or `needs_human_review`.
6. Let the user send to confirmed public channels only. Present unverified contacts to an internal reviewer, clearly labelled.
7. Keep a change log; contact details, contracts, and jurisdictional arrangements change.

## Change log

**v2 — 12 Sep 2026**
- Added all six cantonment boards with official websites and the jurisdictional override rule.
- Added the DHA rule: municipal complaints in DHA Phases 1-8 route to Cantonment Board Clifton, not DHA and not KMC.
- **Corrected:** street lights route to KMC 1339, not K-Electric. K-Electric retained for supply faults and exposed wires.
- **Corrected:** construction debris/malba is explicitly not SSWMB. New routing row added.
- **Corrected:** SSWMB 1128 confirmed as the current 24/7 channel. The 2021-era SMS number 0318-1030851 and landline 021-99333702 demoted to historical and removed from the primary route.
- **Corrected:** KWSC email moved to the legacy `kwsb.gos.pk` domain, which has public support, rather than a guessed `kwsc.gos.pk` address. Verified HQ number added.
- Added SBCA and the DC/AC district administration as routing entities.
- Added five missing towns: Lyari, Keamari, Chanesar, Sohrab Goth, Safoora.
- Flagged the Jamshed/Jinnah possible duplicate.
- Added the water tanker tariff reference.
- **Replaced** the GIS-polygon recommendation with the gazetteer approach, and documented why polygons and postal codes do not work.

## Sources consulted

- KMC: https://kmc.gos.pk ; https://kmc.gos.pk/e-kmc/ ; public reporting on the 1339 Citizens Complaints Information System
- KWSC: https://www.kwsc.gos.pk ; https://www.kwsc.gos.pk/contact ; https://www.kwsc.gos.pk/faqs ; legacy https://www.kwsb.gos.pk
- SSWMB: https://sswmb.gos.pk/contact-us/ ; public reporting on helpline 1128 inauguration and on the debris/garbage scope distinction
- Cantonment Board Clifton: https://cbc.gov.pk
- Cantonment Board Korangi Creek: https://korangi.cantonment.gov.pk
- Cantonment Boards Karachi, Faisal, Malir, Manora: website addresses taken from encyclopedia infoboxes, **not yet loaded and confirmed**
- Tanker tariffs: Dawn and ProPakistani reporting, mid-2026
- Encroachment mandate: Dawn, Express Tribune, Business Recorder reporting on Sindh CM directives, 2024-2025
- Local government structure: Dawn and Geo reporting on the three-tier KMC/TMC/UC setup
- UC boundary dataset: Humanitarian Data Exchange, Alhasan Systems, August 2017

Re-validate any operational detail before production deployment.
