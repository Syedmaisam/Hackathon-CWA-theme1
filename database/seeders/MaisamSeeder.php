<?php

namespace Database\Seeders;

use App\Models\GazetteerNode;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MaisamSeeder extends Seeder
{
    /**
     * 14 demo reports with AI fields pre-filled, so the hero screens and the
     * Filament admin have real data to show even with the network down.
     * Every reachable visual state is represented: drafted, awaiting_answer,
     * and ai_failed. Every row carries a picked gazetteer node, as the
     * compose screen now requires.
     */
    public function run(): void
    {
        $this->report(2, [
            'raw_text' => 'Sewage overflowing on Frere Road near Empress Market, Saddar, since Sunday.',
            'classification' => [
                'issue_type' => 'sewer_overflow', 'issue_confidence' => 'high', 'road_scope' => 'arterial',
                'severity' => 'high', 'hazards' => ['health_risk', 'traffic_blocked'], 'input_language' => 'en',
                'location' => ['landmark' => 'Empress Market', 'road' => 'Frere Road', 'block_or_sector' => null, 'area' => 'Saddar', 'cantonment_or_estate' => null],
                'observed_when' => 'since Sunday', 'photo_matches_text' => null,
                'summary_en' => 'Sewage is overflowing onto Frere Road near Empress Market in Saddar, reported since Sunday.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'sewer_overflow', 'severity' => 'high', 'hazards' => ['health_risk', 'traffic_blocked'],
            'input_language' => 'en', 'location_text' => 'Empress Market, Frere Road, Saddar',
            'gazetteer_node' => 'Saddar', 'resolved_area' => 'Saddar', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'kwsc', 'role' => 'primary', 'reason' => 'Sewage overflow, choked sewers, and open manholes go to KWSC 1334. Include a photo and exact location. A visibly storm-water drain (not sewage) routes to KMC/TMC instead.', 'rule' => 'sewer_overflow']],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'clear_sewer', 'status' => 'drafted',
            'draft_en' => "To: Karachi Water & Sewerage Corporation\n\nSubject: Sewage overflow on Frere Road near Empress Market, Saddar\n\nSewage has been overflowing onto Frere Road near Empress Market in Saddar since Sunday, creating a health hazard and partially blocking traffic on a busy market road.\n\nRequested action: clear the blocked sewer line and restore normal drainage.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: کراچی واٹر اینڈ سیوریج کارپوریشن\n\nموضوع: فریئر روڈ، ایمپریس مارکیٹ کے قریب، صدر میں گٹر کا پانی ابل رہا ہے\n\nاتوار سے فریئر روڈ پر ایمپریس مارکیٹ کے قریب گٹر کا پانی ابل رہا ہے جس سے صحت کے خطرات اور ایک مصروف بازار کی سڑک پر ٹریفک میں رکاوٹ پیدا ہو رہی ہے۔\n\nمطلوبہ کارروائی: بند گٹر لائن کو صاف کر کے نکاسی آب بحال کی جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(5, [
            'raw_text' => 'Street light dead on the main road in Saudabad, Malir, near the TMC office.',
            'classification' => [
                'issue_type' => 'streetlight', 'issue_confidence' => 'high', 'road_scope' => 'arterial',
                'severity' => 'medium', 'hazards' => [], 'input_language' => 'en',
                'location' => ['landmark' => 'TMC Malir office', 'road' => null, 'block_or_sector' => null, 'area' => 'Saudabad', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => 'A street light on the main road in Saudabad, Malir, near the TMC office is not working.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'streetlight', 'severity' => 'medium', 'hazards' => [],
            'input_language' => 'en', 'location_text' => 'TMC Malir office, Saudabad',
            'gazetteer_node' => 'Saudabad', 'resolved_area' => 'Malir Town', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'kmc', 'role' => 'primary', 'reason' => 'A dead street light is publicly documented as a KMC 1339 issue, not K-Electric. K-Electric only handles supply-side faults.', 'rule' => 'streetlight']],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'repair_streetlight', 'status' => 'drafted',
            'draft_en' => "To: Karachi Metropolitan Corporation\n\nSubject: Street light outage on the main road, Saudabad, Malir\n\nThe street light on the main road in Saudabad, Malir, near the TMC Malir office is not working, leaving the stretch dark at night.\n\nRequested action: repair or replace the street light.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: کراچی میٹروپولیٹن کارپوریشن\n\nموضوع: سعود آباد، ملیر کی مرکزی سڑک پر اسٹریٹ لائٹ خراب\n\nسعود آباد، ملیر میں ٹی ایم سی ملیر کے دفتر کے قریب مرکزی سڑک کی اسٹریٹ لائٹ کام نہیں کر رہی، جس کی وجہ سے رات کو یہ حصہ اندھیرے میں رہتا ہے۔\n\nمطلوبہ کارروائی: اسٹریٹ لائٹ کی مرمت یا تبدیلی کی جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(7, [
            'raw_text' => 'Shopkeepers have taken over the whole footpath on Tariq Road near Dolmen, nobody can walk anymore.',
            'classification' => [
                'issue_type' => 'encroachment', 'issue_confidence' => 'medium', 'road_scope' => 'internal_street',
                'severity' => 'medium', 'hazards' => ['traffic_blocked'], 'input_language' => 'en',
                'location' => ['landmark' => 'Dolmen', 'road' => 'Tariq Road', 'block_or_sector' => null, 'area' => 'Jamshed', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => 'Shopkeepers have occupied the footpath on Tariq Road near Dolmen.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'encroachment', 'severity' => 'medium', 'hazards' => ['traffic_blocked'],
            'input_language' => 'en', 'location_text' => 'Dolmen, Tariq Road, Jamshed',
            'gazetteer_node' => 'Tariq Road', 'resolved_area' => 'Jamshed', 'resolved_special_zone' => null,
            'routing' => [
                ['authority_id' => 'tmc_jamshed', 'role' => 'primary', 'reason' => 'Main-road encroachment goes to KMC 1339 with the district DC/AC office as a co-recipient; an internal street goes first to the mapped TMC.', 'rule' => 'encroachment'],
                ['authority_id' => 'kmc', 'role' => 'co_recipient', 'reason' => 'Main-road encroachment goes to KMC 1339 with the district DC/AC office as a co-recipient; an internal street goes first to the mapped TMC.', 'rule' => 'encroachment'],
                ['authority_id' => 'dc_office', 'role' => 'co_recipient', 'reason' => 'Main-road encroachment goes to KMC 1339 with the district DC/AC office as a co-recipient; an internal street goes first to the mapped TMC.', 'rule' => 'encroachment'],
            ],
            'routing_confidence' => 'medium', 'routing_flags' => ['road_ownership_uncertain', 'multi_agency_possible'],
            'requested_remedy' => 'remove_encroachment', 'status' => 'drafted',
            'draft_en' => "To: TMC Jamshed, Karachi Metropolitan Corporation, Deputy Commissioner / Assistant Commissioner Office\n\nSubject: Footpath encroachment on Tariq Road near Dolmen\n\nShopkeepers have occupied the entire footpath on Tariq Road near Dolmen, forcing pedestrians onto the road and blocking traffic flow. Encroachment enforcement on this street has no single owner, so this complaint is addressed to all three responsible offices jointly.\n\nRequested action: remove the encroachment and restore the footpath for pedestrian use.\n\nAttachments: none.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: ٹی ایم سی جمشید، کراچی میٹروپولیٹن کارپوریشن، ڈپٹی کمشنر/اسسٹنٹ کمشنر آفس\n\nموضوع: طارق روڈ، ڈولمن کے قریب فٹ پاتھ پر قبضہ\n\nدکانداروں نے طارق روڈ، ڈولمن کے قریب پورے فٹ پاتھ پر قبضہ کر لیا ہے جس کی وجہ سے پیدل چلنے والوں کو سڑک پر آنا پڑتا ہے اور ٹریفک میں رکاوٹ ہوتی ہے۔ اس سڑک پر قبضے کے خلاف کارروائی کا کوئی ایک ذمہ دار ادارہ نہیں، اس لیے یہ شکایت تینوں متعلقہ اداروں کو بھیجی جا رہی ہے۔\n\nمطلوبہ کارروائی: قبضہ ہٹا کر فٹ پاتھ پیدل چلنے والوں کے لیے بحال کیا جائے۔\n\nمنسلکات: کوئی نہیں۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(10, [
            'raw_text' => 'کچرا تین دن سے نہیں اٹھا، چاکیواڑہ لیاری',
            'classification' => [
                'issue_type' => 'garbage', 'issue_confidence' => 'high', 'road_scope' => 'not_applicable',
                'severity' => 'medium', 'hazards' => ['health_risk'], 'input_language' => 'ur',
                'location' => ['landmark' => 'Chakiwara', 'road' => null, 'block_or_sector' => null, 'area' => 'Lyari', 'cantonment_or_estate' => null],
                'observed_when' => 'three days', 'photo_matches_text' => null,
                'summary_en' => 'Garbage has not been collected for three days in Chakiwara, Lyari.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'garbage', 'severity' => 'medium', 'hazards' => ['health_risk'],
            'input_language' => 'ur', 'location_text' => 'Chakiwara, Lyari',
            'gazetteer_node' => 'Chakiwara', 'resolved_area' => 'Lyari', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'sswmb', 'role' => 'primary', 'reason' => "Municipal garbage, overflowing bins, and illegal dumping go to SSWMB's 24/7 helpline 1128.", 'rule' => 'garbage']],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'collect_waste', 'status' => 'drafted',
            'draft_en' => "To: Sindh Solid Waste Management Board\n\nSubject: Garbage uncollected for three days in Chakiwara, Lyari\n\nGarbage has not been collected for three days in Chakiwara, Lyari, creating a health hazard for residents.\n\nRequested action: collect the accumulated waste and resume the regular collection schedule.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: سندھ سالڈ ویسٹ مینجمنٹ بورڈ\n\nموضوع: چاکیواڑہ، لیاری میں تین دن سے کچرا نہیں اٹھایا گیا\n\nچاکیواڑہ، لیاری میں تین دن سے کچرا نہیں اٹھایا گیا جس سے رہائشیوں کی صحت کو خطرہ لاحق ہے۔\n\nمطلوبہ کارروائی: جمع شدہ کچرا اٹھایا جائے اور کچرا اٹھانے کا باقاعدہ شیڈول بحال کیا جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(1, [
            'raw_text' => "Builder dumped malba on the road outside our house, Gulshan-e-Iqbal Block 13-D. It's blocking half the street.",
            'classification' => [
                'issue_type' => 'construction_debris', 'issue_confidence' => 'high', 'road_scope' => 'internal_street',
                'severity' => 'medium', 'hazards' => ['traffic_blocked'], 'input_language' => 'en',
                'location' => ['landmark' => null, 'road' => null, 'block_or_sector' => 'Block 13-D', 'area' => 'Gulshan-e-Iqbal', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => 'A builder has dumped construction debris on the road in Gulshan-e-Iqbal Block 13-D, blocking half the street.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'construction_debris', 'severity' => 'medium', 'hazards' => ['traffic_blocked'],
            'input_language' => 'en', 'location_text' => 'Block 13-D, Gulshan-e-Iqbal',
            'gazetteer_node' => 'Gulshan-e-Iqbal Block 13-D', 'resolved_area' => 'Gulshan-e-Iqbal', 'resolved_special_zone' => null,
            'routing' => [
                ['authority_id' => 'kmc', 'role' => 'primary', 'reason' => 'Malba and building material dumped on a road or footpath goes to KMC 1339. Never SSWMB.', 'rule' => 'construction_debris'],
                ['authority_id' => 'sbca', 'role' => 'co_recipient', 'reason' => 'Co-route to SBCA when a builder or under-construction building is the source.', 'rule' => 'construction_debris'],
            ],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'remove_debris', 'status' => 'drafted',
            'draft_en' => "To: Karachi Metropolitan Corporation, Sindh Building Control Authority\n\nSubject: Construction debris dumped on the road, Gulshan-e-Iqbal Block 13-D\n\nA builder has dumped construction debris (malba) on the road outside our house in Gulshan-e-Iqbal Block 13-D, blocking half the street and obstructing traffic. As the source appears to be an active construction site, SBCA is copied alongside KMC.\n\nRequested action: remove the debris from the public road.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: کراچی میٹروپولیٹن کارپوریشن، سندھ بلڈنگ کنٹرول اتھارٹی\n\nموضوع: گلشن اقبال بلاک 13-D میں سڑک پر ملبہ\n\nایک بلڈر نے ہمارے گھر کے باہر گلشن اقبال بلاک 13-D کی سڑک پر ملبہ ڈال دیا ہے جس سے آدھی سڑک بند ہو گئی ہے اور ٹریفک میں رکاوٹ پیدا ہو رہی ہے۔ چونکہ یہ ملبہ ایک زیرِ تعمیر عمارت سے آیا ہے، اس لیے ایس بی سی اے کو بھی اس شکایت میں شامل کیا جا رہا ہے۔\n\nمطلوبہ کارروائی: سڑک سے ملبہ ہٹایا جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(4, [
            'raw_text' => 'Burst water main in North Nazimabad Block H, the whole road is flooded.',
            'classification' => [
                'issue_type' => 'water_supply', 'issue_confidence' => 'high', 'road_scope' => 'internal_street',
                'severity' => 'high', 'hazards' => ['flooding', 'traffic_blocked'], 'input_language' => 'en',
                'location' => ['landmark' => null, 'road' => null, 'block_or_sector' => 'Block H', 'area' => 'North Nazimabad', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => 'A burst water main has flooded the road in North Nazimabad Block H.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'water_supply', 'severity' => 'high', 'hazards' => ['flooding', 'traffic_blocked'],
            'input_language' => 'en', 'location_text' => 'Block H, North Nazimabad',
            'gazetteer_node' => 'North Nazimabad Block H', 'resolved_area' => 'North Nazimabad', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'kwsc', 'role' => 'primary', 'reason' => 'Burst mains, leaks, no water, and low or dirty water pressure go to KWSC 1334.', 'rule' => 'water_supply']],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'repair_pipe', 'status' => 'drafted',
            'draft_en' => "To: Karachi Water & Sewerage Corporation\n\nSubject: Burst water main flooding the road, North Nazimabad Block H\n\nA water main has burst in North Nazimabad Block H, flooding the entire road and obstructing traffic.\n\nRequested action: repair the burst pipe and stop the ongoing water loss.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: کراچی واٹر اینڈ سیوریج کارپوریشن\n\nموضوع: نارتھ ناظم آباد بلاک ایچ میں پانی کی مین لائن پھٹ گئی\n\nنارتھ ناظم آباد بلاک ایچ میں پانی کی مین لائن پھٹنے سے پوری سڑک زیرِ آب آ گئی ہے اور ٹریفک میں رکاوٹ پیدا ہو رہی ہے۔\n\nمطلوبہ کارروائی: پھٹی ہوئی پائپ لائن کی مرمت کر کے پانی کا ضیاع روکا جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(8, [
            'raw_text' => 'Deep potholes on our street, Nazimabad No. 2, right behind Gujjar Nala. Bikes keep falling.',
            'classification' => [
                'issue_type' => 'road_damage', 'issue_confidence' => 'high', 'road_scope' => 'internal_street',
                'severity' => 'medium', 'hazards' => ['traffic_blocked'], 'input_language' => 'en',
                'location' => ['landmark' => 'Gujjar Nala', 'road' => null, 'block_or_sector' => null, 'area' => 'Nazimabad No. 2', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => 'Deep potholes on a residential street behind Gujjar Nala in Nazimabad No. 2.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'road_damage', 'severity' => 'medium', 'hazards' => ['traffic_blocked'],
            'input_language' => 'en', 'location_text' => 'Gujjar Nala, Nazimabad No. 2',
            'gazetteer_node' => 'Nazimabad No.1 Near Agha Juice Center', 'resolved_area' => 'Nazimabad', 'resolved_special_zone' => null,
            'routing' => [
                ['authority_id' => 'tmc_nazimabad', 'role' => 'primary', 'reason' => 'Potholes and damaged local streets go to the mapped TMC.', 'rule' => 'road_damage'],
                ['authority_id' => 'kmc', 'role' => 'co_recipient', 'reason' => 'Potholes and damaged local streets go to the mapped TMC.', 'rule' => 'road_damage'],
            ],
            'routing_confidence' => 'medium', 'routing_flags' => ['road_ownership_uncertain'],
            'requested_remedy' => 'repair_road', 'status' => 'drafted',
            'draft_en' => "To: TMC Nazimabad, Karachi Metropolitan Corporation\n\nSubject: Deep potholes on our street, Nazimabad No. 2 behind Gujjar Nala\n\nDeep potholes have formed on our residential street in Nazimabad No. 2, right behind Gujjar Nala. Motorbike riders keep falling. Because ownership of this stretch is not clearly documented, KMC is copied alongside TMC Nazimabad.\n\nRequested action: repair the road surface.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: ٹی ایم سی ناظم آباد، کراچی میٹروپولیٹن کارپوریشن\n\nموضوع: ناظم آباد نمبر 2، گجر نالہ کے پیچھے گہرے گڑھے\n\nناظم آباد نمبر 2 میں گجر نالہ کے بالکل پیچھے ہماری گلی میں گہرے گڑھے بن گئے ہیں جس سے موٹر سائیکل سوار گر رہے ہیں۔ چونکہ اس سڑک کی ملکیت واضح نہیں، اس لیے ٹی ایم سی ناظم آباد کے ساتھ کے ایم سی کو بھی شامل کیا جا رہا ہے۔\n\nمطلوبہ کارروائی: سڑک کی مرمت کی جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(3, [
            'raw_text' => 'Sparking wire hanging low near the park in Korangi Sector 33, kids play right under it.',
            'classification' => [
                'issue_type' => 'electrical_hazard', 'issue_confidence' => 'high', 'road_scope' => 'not_applicable',
                'severity' => 'emergency', 'hazards' => ['exposed_wire', 'child_risk'], 'input_language' => 'en',
                'location' => ['landmark' => 'park', 'road' => null, 'block_or_sector' => 'Sector 33', 'area' => 'Korangi', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => 'A live wire is hanging low near a park in Korangi Sector 33 where children play.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'electrical_hazard', 'severity' => 'emergency', 'hazards' => ['exposed_wire', 'child_risk'],
            'input_language' => 'en', 'location_text' => 'park, Sector 33, Korangi',
            'gazetteer_node' => 'Korangi Sector 33', 'resolved_area' => 'Korangi', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'ke', 'role' => 'primary', 'reason' => 'Sparking wires, exposed cables, leaning poles, and transformer faults go to K-Electric 118.', 'rule' => 'electrical_hazard']],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'make_safe_electrical', 'status' => 'drafted',
            'draft_en' => "To: K-Electric\n\nSubject: EMERGENCY — sparking live wire near a park, Korangi Sector 33\n\nA sparking wire is hanging low near the park in Korangi Sector 33, directly above where children play. This is an immediate safety emergency.\n\nRequested action: make the wire safe immediately.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: کے الیکٹرک\n\nموضوع: ہنگامی — کورنگی سیکٹر 33 میں پارک کے قریب اسپارکنگ تار\n\nکورنگی سیکٹر 33 میں پارک کے قریب ایک تار نیچی لٹک رہی ہے اور اس میں چنگاریاں نکل رہی ہیں، بالکل اس جگہ جہاں بچے کھیلتے ہیں۔ یہ فوری حفاظتی ہنگامی صورتحال ہے۔\n\nمطلوبہ کارروائی: تار کو فوری طور پر محفوظ بنایا جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(6, [
            'raw_text' => 'gali mein pani khara hai 2 din se, pata nahi kis se bolein',
            'classification' => [
                'issue_type' => 'unknown', 'issue_confidence' => 'low', 'road_scope' => 'internal_street',
                'severity' => 'medium', 'hazards' => [], 'input_language' => 'roman_urdu',
                'location' => ['landmark' => null, 'road' => null, 'block_or_sector' => null, 'area' => 'Gulshan-e-Ghazi', 'cantonment_or_estate' => null],
                'observed_when' => 'two days', 'photo_matches_text' => null,
                'summary_en' => 'Standing water in a street in Gulshan-e-Ghazi, Baldia, for two days; source of the water not stated.',
                'clarifying_question' => 'Yeh pani gutter ka hai, pipeline leak ka, ya barish ka?',
            ],
            'issue_type' => 'unknown', 'severity' => 'medium', 'hazards' => [],
            'input_language' => 'roman_urdu', 'location_text' => 'Gulshan-e-Ghazi',
            'gazetteer_node' => 'Gulshan-e-Ghazi', 'resolved_area' => 'Baldia', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'pmdu', 'role' => 'primary', 'reason' => "When the report is unresolvable, ask one clarifying question first. If it's still unclear, fall back to the Pakistan Citizen Portal rather than silently dropping the report.", 'rule' => 'unknown']],
            'routing_confidence' => 'low', 'routing_flags' => [],
            'clarifying_question' => 'Yeh pani gutter ka hai, pipeline leak ka, ya barish ka?',
            'requested_remedy' => null, 'status' => 'awaiting_answer', 'draft_en' => null, 'draft_ur' => null,
        ]);

        $this->report(9, [
            'raw_text' => 'No water for a week now in Khuda Ki Basti, Manghopir. Tankers are charging double.',
            'classification' => [
                'issue_type' => 'water_supply', 'issue_confidence' => 'high', 'road_scope' => 'not_applicable',
                'severity' => 'high', 'hazards' => ['health_risk'], 'input_language' => 'en',
                'location' => ['landmark' => null, 'road' => null, 'block_or_sector' => null, 'area' => 'Khuda Ki Basti', 'cantonment_or_estate' => null],
                'observed_when' => 'a week', 'photo_matches_text' => null,
                'summary_en' => 'No piped water supply for a week in Khuda Ki Basti, Manghopir.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'water_supply', 'severity' => 'high', 'hazards' => ['health_risk'],
            'input_language' => 'en', 'location_text' => 'Khuda Ki Basti',
            'gazetteer_node' => 'Khuda Ki Basti', 'resolved_area' => 'Manghopir', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'kwsc', 'role' => 'primary', 'reason' => 'Burst mains, leaks, no water, and low or dirty water pressure go to KWSC 1334.', 'rule' => 'water_supply']],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'restore_supply', 'status' => 'drafted',
            'draft_en' => "To: Karachi Water & Sewerage Corporation\n\nSubject: No water supply for a week, Khuda Ki Basti (UC-09), Manghopir\n\nThere has been no piped water in Khuda Ki Basti, Manghopir, for a week. Households are relying on private tankers, which are charging double the usual rate.\n\nRequested action: restore the piped supply and check the line feeding this UC.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: کراچی واٹر اینڈ سیوریج کارپوریشن\n\nموضوع: خدا کی بستی (یو سی 9)، منگھوپیر میں ایک ہفتے سے پانی بند\n\nخدا کی بستی، منگھوپیر میں ایک ہفتے سے لائن کا پانی نہیں آ رہا۔ گھرانے نجی ٹینکروں پر انحصار کر رہے ہیں جو دگنی قیمت وصول کر رہے ہیں۔\n\nمطلوبہ کارروائی: لائن کا پانی بحال کیا جائے اور اس یو سی کو پانی دینے والی لائن کی جانچ کی جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(12, [
            'raw_text' => 'Garbage piling up badly in Orangi Town Sector 11½, nobody has collected it in over a week.',
            'classification' => null, 'issue_type' => null, 'severity' => null, 'hazards' => null,
            'input_language' => null, 'location_text' => null,
            'gazetteer_node' => 'Orangi Town Sector 11', 'resolved_area' => 'Orangi Town', 'resolved_special_zone' => null,
            'routing' => null, 'routing_confidence' => null, 'routing_flags' => null,
            'requested_remedy' => 'other', 'status' => 'ai_failed', 'ai_failed' => true,
            'draft_en' => "To: Sindh Solid Waste Management Board\n\nIssue: garbage\n\nLocation: Orangi Town Sector 11½\n\nObserved since: unknown\n\nDetails: Garbage piling up badly in Orangi Town Sector 11½, nobody has collected it in over a week.\n\nPlease provide a complaint reference number and expected resolution time.",
            'draft_ur' => "موصول کنندہ: سندھ سالڈ ویسٹ مینجمنٹ بورڈ\n\nمسئلہ: کچرا\n\nمقام: اورنگی ٹاؤن سیکٹر گیارہ نیم\n\nتاریخ: نامعلوم\n\nتفصیل: اورنگی ٹاؤن سیکٹر گیارہ نیم میں کچرے کے ڈھیر لگ گئے ہیں، ایک ہفتے سے زیادہ عرصے سے کچرا نہیں اٹھایا گیا۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(2, [
            'raw_text' => 'Storm drain choked near Boat Basin, Clifton Block 2. Water does not drain out at all now.',
            'classification' => [
                'issue_type' => 'storm_drain', 'issue_confidence' => 'high', 'road_scope' => 'internal_street',
                'severity' => 'medium', 'hazards' => ['flooding'], 'input_language' => 'en',
                'location' => ['landmark' => 'Boat Basin', 'road' => null, 'block_or_sector' => 'Block 2', 'area' => 'Clifton', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => 'A storm-water drain near Boat Basin in Clifton Block 2 is completely choked.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'storm_drain', 'severity' => 'medium', 'hazards' => ['flooding'],
            'input_language' => 'en', 'location_text' => 'Boat Basin, Block 2, Clifton',
            'gazetteer_node' => 'Clifton Block 2', 'resolved_area' => 'Saddar', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'kmc', 'role' => 'primary', 'reason' => 'Blocked storm-water drains and nullah flooding go to KMC first.', 'rule' => 'storm_drain']],
            'routing_confidence' => 'high', 'routing_flags' => ['multi_agency_possible'],
            'requested_remedy' => 'clear_drain', 'status' => 'drafted',
            'draft_en' => "To: Karachi Metropolitan Corporation\n\nSubject: Storm drain completely choked near Boat Basin, Clifton Block 2\n\nThe storm-water drain near Boat Basin in Clifton Block 2 is completely choked and no longer draining.\n\nRequested action: desilt and clear the drain.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: کراچی میٹروپولیٹن کارپوریشن\n\nموضوع: بوٹ بیسن، کلفٹن بلاک 2 کے قریب طوفانی نالہ مکمل بند\n\nبوٹ بیسن، کلفٹن بلاک 2 کے قریب طوفانی نالہ مکمل طور پر بند ہو چکا ہے اور پانی نکاسی نہیں ہو رہی۔\n\nمطلوبہ کارروائی: نالے کی صفائی کی جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(1, [
            'raw_text' => "Trash hasn't been picked up in our lane, Gulshan-e-Iqbal Block 13-D, for days now.",
            'classification' => [
                'issue_type' => 'garbage', 'issue_confidence' => 'high', 'road_scope' => 'not_applicable',
                'severity' => 'low', 'hazards' => [], 'input_language' => 'en',
                'location' => ['landmark' => null, 'road' => null, 'block_or_sector' => 'Block 13-D', 'area' => 'Gulshan-e-Iqbal', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => "Trash hasn't been collected for days in Gulshan-e-Iqbal Block 13-D.",
                'clarifying_question' => null,
            ],
            'issue_type' => 'garbage', 'severity' => 'low', 'hazards' => [],
            'input_language' => 'en', 'location_text' => 'Block 13-D, Gulshan-e-Iqbal',
            'gazetteer_node' => 'Gulshan-e-Iqbal Block 13-D', 'resolved_area' => 'Gulshan-e-Iqbal', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'sswmb', 'role' => 'primary', 'reason' => "Municipal garbage, overflowing bins, and illegal dumping go to SSWMB's 24/7 helpline 1128.", 'rule' => 'garbage']],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'collect_waste', 'status' => 'drafted',
            'draft_en' => "To: Sindh Solid Waste Management Board\n\nSubject: Trash uncollected for days, Gulshan-e-Iqbal Block 13-D\n\nTrash has not been picked up in our lane in Gulshan-e-Iqbal Block 13-D for several days now.\n\nRequested action: collect the accumulated waste and resume the regular schedule.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: سندھ سالڈ ویسٹ مینجمنٹ بورڈ\n\nموضوع: گلشن اقبال بلاک 13-D میں کئی دنوں سے کچرا نہیں اٹھایا گیا\n\nہماری گلی، گلشن اقبال بلاک 13-D میں کئی دنوں سے کچرا نہیں اٹھایا گیا۔\n\nمطلوبہ کارروائی: جمع شدہ کچرا اٹھایا جائے اور باقاعدہ شیڈول بحال کیا جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);

        $this->report(3, [
            'raw_text' => 'Same street, Gulshan-e-Iqbal Block 13-D — garbage bins are overflowing onto the road again.',
            'classification' => [
                'issue_type' => 'garbage', 'issue_confidence' => 'high', 'road_scope' => 'not_applicable',
                'severity' => 'medium', 'hazards' => [], 'input_language' => 'en',
                'location' => ['landmark' => null, 'road' => null, 'block_or_sector' => 'Block 13-D', 'area' => 'Gulshan-e-Iqbal', 'cantonment_or_estate' => null],
                'observed_when' => null, 'photo_matches_text' => null,
                'summary_en' => 'Garbage bins are overflowing onto the road again in Gulshan-e-Iqbal Block 13-D.',
                'clarifying_question' => null,
            ],
            'issue_type' => 'garbage', 'severity' => 'medium', 'hazards' => [],
            'input_language' => 'en', 'location_text' => 'Block 13-D, Gulshan-e-Iqbal',
            'gazetteer_node' => 'Gulshan-e-Iqbal Block 13-D', 'resolved_area' => 'Gulshan-e-Iqbal', 'resolved_special_zone' => null,
            'routing' => [['authority_id' => 'sswmb', 'role' => 'primary', 'reason' => "Municipal garbage, overflowing bins, and illegal dumping go to SSWMB's 24/7 helpline 1128.", 'rule' => 'garbage']],
            'routing_confidence' => 'high', 'routing_flags' => [],
            'requested_remedy' => 'collect_waste', 'status' => 'drafted',
            'draft_en' => "To: Sindh Solid Waste Management Board\n\nSubject: Garbage bins overflowing onto the road, Gulshan-e-Iqbal Block 13-D\n\nGarbage bins on our street in Gulshan-e-Iqbal Block 13-D are overflowing onto the road again, for at least the third time this fortnight.\n\nRequested action: collect the waste and review the collection frequency for this block.\n\nPlease provide a complaint reference number and the expected resolution time.",
            'draft_ur' => "بنام: سندھ سالڈ ویسٹ مینجمنٹ بورڈ\n\nموضوع: گلشن اقبال بلاک 13-D میں کچرے کے ڈبے سڑک پر بہہ رہے ہیں\n\nہماری گلی، گلشن اقبال بلاک 13-D میں کچرے کے ڈبے دوبارہ سڑک پر بہہ رہے ہیں، اس پندرہ دن میں کم از کم تیسری بار۔\n\nمطلوبہ کارروائی: کچرا اٹھایا جائے اور اس بلاک کے لیے اٹھانے کے شیڈول کا جائزہ لیا جائے۔\n\nبراہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔",
        ]);
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function report(int $daysAgo, array $fields): void
    {
        $gazetteerNodeId = isset($fields['gazetteer_node'])
            ? GazetteerNode::query()->where('name', $fields['gazetteer_node'])->value('id')
            : null;

        $report = Report::query()->create([
            'citizen_name' => $fields['citizen_name'] ?? null,
            'citizen_phone' => $fields['citizen_phone'] ?? null,
            'input_mode' => $fields['input_mode'] ?? 'text',
            'input_language' => $fields['input_language'] ?? null,
            'raw_text' => $fields['raw_text'],
            'photo_path' => null,
            'classification' => $fields['classification'] ?? null,
            'issue_type' => $fields['issue_type'] ?? null,
            'severity' => $fields['severity'] ?? null,
            'hazards' => $fields['hazards'] ?? null,
            'location_text' => $fields['location_text'] ?? null,
            'gazetteer_node_id' => $gazetteerNodeId,
            'resolved_area' => $fields['resolved_area'] ?? null,
            'resolved_special_zone' => $fields['resolved_special_zone'] ?? null,
            'routing' => $fields['routing'] ?? null,
            'routing_confidence' => $fields['routing_confidence'] ?? null,
            'routing_flags' => $fields['routing_flags'] ?? null,
            'clarifying_question' => $fields['clarifying_question'] ?? null,
            'clarifying_answer' => $fields['clarifying_answer'] ?? null,
            'draft_en' => $fields['draft_en'] ?? null,
            'draft_ur' => $fields['draft_ur'] ?? null,
            'requested_remedy' => $fields['requested_remedy'] ?? null,
            'status' => $fields['status'],
            'ai_failed_at' => ($fields['ai_failed'] ?? false) ? Carbon::now()->subDays($daysAgo) : null,
        ]);

        $when = Carbon::now()->subDays($daysAgo);

        $report->timestamps = false;
        $report->created_at = $when;
        $report->updated_at = $when;
        $report->save();
    }
}
