# Testing prompts

Every place named here is seeded and was verified present. Scope is the TMC
towns only: cantonments and DHA were removed on purpose, so do not test them.

**How to run one:** open `/`, pick the location in the search box first, then
type or speak the complaint. The picked place decides the authority; the text
only decides the issue type.

Two things make a prompt route well: **a plain description of the physical
problem**, and **a picked location**. The model does not need you to name the
department, and it should not be told one.

---

## 1. The thirteen issue types

One prompt each, in plain citizen language. Expected authority is what the
seeded routing rule sends it to.

| # | Pick this place | Type this | Should route to |
|---|---|---|---|
| 1 | Disco Bakery | Garbage has been piling up at the corner for a week and nobody has collected it. | Sindh Solid Waste Management Board |
| 2 | Saddar | Sewage is overflowing from a manhole onto the street and the smell is unbearable. | Karachi Water & Sewerage Corporation |
| 3 | Azizabad | There has been no water in our line for four days. | Karachi Water & Sewerage Corporation |
| 4 | Chakiwara | We need a water tanker, the supply has been dry all week. | Karachi Water & Sewerage Corporation |
| 5 | Tariq Road | There is a deep pothole in the middle of the road and cars keep hitting it. | Karachi Metropolitan Corporation |
| 6 | Buffer Zone | The street light outside our lane has not worked for two weeks and it is pitch dark at night. | Karachi Metropolitan Corporation |
| 7 | Korangi | A live wire is hanging low over the footpath and sparking when it rains. | K-Electric |
| 8 | Sakhi Hassan | Shopkeepers have taken over the whole footpath and pedestrians have to walk on the road. | Karachi Metropolitan Corporation |
| 9 | Samanabad | Someone is building an extra floor on a house with no approval sign anywhere. | Sindh Building Control Authority |
| 10 | Gujjar Nala | The storm drain is completely choked and the lane floods every time it rains. | Karachi Metropolitan Corporation |
| 11 | Yaseenabad | Builders dumped a pile of rubble and broken bricks on the roadside and left it there. | Karachi Metropolitan Corporation |
| 12 | Machar Colony | The park swings are broken and the whole ground is covered in litter. | Karachi Metropolitan Corporation |
| 13 | Boat Basin | Something is wrong outside but I cannot tell what it is. | Pakistan Citizen Portal |

Row 13 is the deliberate `unknown` case. A vague sentence should fall through
to the fallback rather than guess.

---

## 2. Urdu and Roman Urdu

The app detects the language from the text and drafts in both. Pick the place
the same way.

| Pick this place | Type this |
|---|---|
| Waterpump | گلی میں گٹر کا پانی کھڑا ہے اور بدبو پھیل رہی ہے۔ |
| Ayesha Manzil | کئی دنوں سے کوڑا نہیں اٹھایا گیا، بدبو بہت ہے۔ |
| Moosa Colony | gali mein pani khara hai 2 din se |
| Qayyumabad | sarak par bara gaddha hai, gari nikalna mushkil hai |
| Saeedabad | street light kaam nahi kar rahi, raat ko andhera hota hai |

Check on the report page that the Urdu draft reads right-to-left in Nastaliq.

---

## 3. The clarifying-question path

These are deliberately vague on the one detail that decides the routing. The
app should ask a question instead of guessing.

| Pick this place | Type this | Then answer |
|---|---|---|
| Moosa Colony | gali mein pani khara hai 2 din se | gutter ka pani hai |
| Sharifabad | There is water standing in our street since yesterday. | It is sewage water, not rain water. |
| Nazimabad | Something is leaking near the corner and it smells bad. | It is a burst sewerage line. |

The second answer should move it to the sewerage corporation. Answering "it is
rain water" instead should move it toward the storm drain rule.

---

## 4. The place picker

Search behaviour, no submission needed.

| Type in the picker | Should offer |
|---|---|
| fb area | Gulberg (this is the F.B. Area fix) |
| f.b. area | Gulberg |
| ایف بی | Gulberg |
| clifton | Clifton first, then its numbered blocks |
| boat basin | Boat Basin, under Saddar |
| tariq road | Tariq Road, under Jamshed |
| disco | Disco Bakery, UC-04 Gulshan-e-Iqbal |
| lyari | Lyari the town first, then Chakiwara and Kalri |

The picker holds 257 places: 27 towns, 157 union councils, 73 landmarks.
Districts are deliberately not offered, because they route to nobody.

---

## 5. Edge cases worth trying once

- **Submit with no location picked.** Should refuse and ask you to pick one.
- **A wrong-city address with a Karachi place picked.** Type a Lahore street
  name but pick Disco Bakery. It should route to Gulshan-e-Iqbal, because the
  picker is the authority and free text never overrides it.
- **A voice note in Urdu.** Needs Chrome. iOS Safari has no Web Speech support
  and the app will tell you to use the keyboard microphone instead.
- **A photo with no text.** Attach an image of a pothole and send it with no
  description.
- **The same complaint twice.** The second run should come back instantly,
  because the classification is cached for seven days.

---

## 6. Admin checks

Sign in at `/admin` as `admin@cityaround.pk` / `password`.

- The reports table opens with anything needing a human at the top, newest
  first inside that. Clicking **Received** sorts newest first on the first
  click, oldest on the second.
- Open any report. There should be no raw JSON anywhere on the page, the
  routed authority should appear by full name with its phone number, and the
  Urdu draft should read right-to-left in Nastaliq.
- Open the AI-failed report. Routing should say "Not routed to anyone yet"
  rather than showing an empty table.
- The dashboard should show your two widgets and no Filament promo card.

---

## Before the demo

Run the prompts you actually plan to show, once each, on the demo machine.
The classification is cached for seven days, so a rehearsed prompt comes back
instantly on the day regardless of the wifi.
