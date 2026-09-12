<?php

namespace Database\Seeders;

use App\Models\Authority;
use App\Models\GazetteerNode;
use App\Models\RoutingRule;
use App\Models\User;
use Illuminate\Database\Seeder;

class SumairSeeder extends Seeder
{
    /**
     * Local slug => created GazetteerNode id, so landmark rows below can
     * reference the district/town/special-zone row they belong under.
     *
     * @var array<string, int>
     */
    private array $nodeIds = [];

    public function run(): void
    {
        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@cityaround.pk',
            'password' => bcrypt('password'),
        ]);

        User::query()->create([
            'name' => 'Citizen',
            'email' => 'citizen@cityaround.pk',
            'password' => bcrypt('password'),
        ]);

        $this->seedAuthorities();
        $this->seedRoutingRules();
        $this->seedGazetteer();
    }

    private function seedAuthorities(): void
    {
        $authorities = [
            // Citywide, SBCA, DC office, PMDU fallback, DHA estate.
            ['id' => 'kmc', 'name' => 'Karachi Metropolitan Corporation', 'short_name' => 'KMC', 'kind' => 'citywide', 'website' => 'https://kmc.gos.pk', 'website_verified' => true, 'email' => 'mayor@kmc.gos.pk', 'email_verified' => false, 'phone' => '1339', 'phone_verified' => true, 'secondary_phone' => '+92 21 992 1511-7', 'address' => '1st Floor, M.A. Jinnah Road, Karachi', 'notes' => '1339 (Citizens Complaints Information System) publicly covers sewage complaints, road potholes and non-functional street lights. E-KMC: https://kmc.gos.pk/e-kmc/.', 'citizen_visible' => true],
            ['id' => 'kwsc', 'name' => 'Karachi Water & Sewerage Corporation', 'short_name' => 'KWSC', 'kind' => 'citywide', 'website' => 'https://www.kwsc.gos.pk', 'website_verified' => true, 'email' => 'info@kwsb.gos.pk', 'email_verified' => false, 'phone' => '1334', 'phone_verified' => true, 'secondary_phone' => '(+92) 021 111 597 200', 'address' => '9th Mile Karsaz, Main Shahrah-e-Faisal, Karachi-75350', 'notes' => 'Legacy name KWSB still used by residents; the legacy kwsb.gos.pk domain is still live. "KWSC Unified" single-app system for tanker booking went live 1 Feb 2026.', 'citizen_visible' => true],
            ['id' => 'sswmb', 'name' => 'Sindh Solid Waste Management Board', 'short_name' => 'SSWMB', 'kind' => 'citywide', 'website' => 'https://sswmb.gos.pk', 'website_verified' => true, 'email' => null, 'email_verified' => false, 'phone' => '1128', 'phone_verified' => true, 'secondary_phone' => '+92 21 99333710-03', 'address' => '3rd Floor, DMC (South) Building, opposite Aram Bagh Police Station, near Haqqani Chowk, District South, Karachi-74200', 'notes' => 'Does not handle construction debris or building material, only garbage. Do not route storm-water drain desilting here.', 'citizen_visible' => true],
            ['id' => 'ke', 'name' => 'K-Electric', 'short_name' => 'KE', 'kind' => 'citywide', 'website' => 'https://www.ke.com.pk', 'website_verified' => true, 'email' => 'customer.care@ke.com.pk', 'email_verified' => true, 'phone' => '118', 'phone_verified' => true, 'secondary_phone' => null, 'address' => null, 'notes' => 'Street lights are KMC, not KE. Route dead streetlights to 1339 and live/sparking wires to 118. Cantonments and managed estates may run their own supply.', 'citizen_visible' => true],
            ['id' => 'sbca', 'name' => 'Sindh Building Control Authority', 'short_name' => 'SBCA', 'kind' => 'citywide', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Repeatedly named by the Sindh CM as the authority for builder undertakings, illegal commercial conversion, and plaza-parking restoration. Co-route with KMC/DC for builder-generated encroachment and debris. No verified public complaint channel yet.', 'citizen_visible' => false],
            ['id' => 'dc_office', 'name' => 'Deputy Commissioner / Assistant Commissioner (district administration)', 'short_name' => 'DC/AC Office', 'kind' => 'citywide', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Named as a co-recipient on encroachment complaints, never as sole recipient. Per-district contacts not yet verified.', 'citizen_visible' => false],
            ['id' => 'pmdu', 'name' => 'Pakistan Citizen Portal', 'short_name' => 'PMDU', 'kind' => 'fallback', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Federal grievance system routing to federal and provincial organisations, submitted via a government mobile app/portal rather than phone or email. Escalation/fallback channel when ownership is unclear or a local authority has not responded. Not a direct emergency response service.', 'citizen_visible' => true],
            ['id' => 'dha', 'name' => 'DHA Karachi', 'short_name' => 'DHA', 'kind' => 'estate', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Handles estate matters only: allotment, transfer, membership, scheme development — not civic faults. Municipal complaints inside DHA Phases 1-8 go to Cantonment Board Clifton, which was constituted specifically to provide municipal cover to those phases.', 'citizen_visible' => false],

            // Six cantonment boards — special-zone overrides.
            ['id' => 'cb_clifton', 'name' => 'Cantonment Board Clifton', 'short_name' => 'CBC', 'kind' => 'cantonment', 'website' => 'https://cbc.gov.pk', 'website_verified' => true, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Notification SRO No. 207(1)/(83), 27-02-1983, to provide municipal cover to 8 DHA phases, 12 katchi abadis in the periphery, and Blocks 8-9 of Clifton. Area 51.327 km².', 'citizen_visible' => true],
            ['id' => 'cb_karachi', 'name' => 'Karachi Cantonment Board', 'short_name' => 'Karachi Cantonment Board', 'kind' => 'cantonment', 'website' => 'https://www.cbkarachi.gov.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Established August 1942, covers Saddar-area cantonment lands. Website sourced from an encyclopedia infobox — confirm the live site before citizen-facing use.', 'citizen_visible' => false],
            ['id' => 'cb_faisal', 'name' => 'Cantonment Board Faisal', 'short_name' => 'CB Faisal', 'kind' => 'cantonment', 'website' => 'https://cbfaisal.gov.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Formerly Drigh Road Cantonment, established 1925. Runs its own Water Supply Branch, Electric Branch and Building Control Cell. Confirm live site before citizen-facing use.', 'citizen_visible' => false],
            ['id' => 'cb_malir', 'name' => 'Cantonment Board Malir', 'short_name' => 'CB Malir', 'kind' => 'cantonment', 'website' => 'https://www.cbmalir.gov.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Established 1948, provides its own waste management, water supply, healthcare and education. Confirm live site before citizen-facing use.', 'citizen_visible' => false],
            ['id' => 'cb_korangi_creek', 'name' => 'Cantonment Board Korangi Creek', 'short_name' => 'CBKC', 'kind' => 'cantonment', 'website' => 'https://korangi.cantonment.gov.pk', 'website_verified' => true, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Notified Dec 1968. Sits within Korangi Town. Autonomous body under Military Lands and Cantonment Department.', 'citizen_visible' => true],
            ['id' => 'cb_manora', 'name' => 'Cantonment Board Manora', 'short_name' => 'CB Manora', 'kind' => 'cantonment', 'website' => 'https://www.cbmanora.gov.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Manora Island, south of Karachi harbour. Class II, naval, restricted area. Confirm live site before citizen-facing use.', 'citizen_visible' => false],

            // 27 Town Municipal Corporations.
            ['id' => 'tmc_saddar', 'name' => 'TMC Saddar', 'short_name' => 'TMC Saddar', 'kind' => 'tmc', 'district' => 'South', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_lyari', 'name' => 'TMC Lyari', 'short_name' => 'TMC Lyari', 'kind' => 'tmc', 'district' => 'South', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'High complaint density, 11-13 UCs. Priority for gazetteer coverage.', 'citizen_visible' => false],
            ['id' => 'tmc_jamshed', 'name' => 'TMC Jamshed', 'short_name' => 'TMC Jamshed', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'May be the same town as TMC Jinnah under two names — unresolved in the source data. Do not route to both.', 'citizen_visible' => false],
            ['id' => 'tmc_jinnah', 'name' => 'TMC Jinnah', 'short_name' => 'TMC Jinnah', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => '021-992131355-59', 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Phone number sourced from a public document snippet, unconfirmed. May be the same town as TMC Jamshed under two names — unresolved in the source data.', 'citizen_visible' => false],
            ['id' => 'tmc_gulshan_e_iqbal', 'name' => 'TMC Gulshan-e-Iqbal', 'short_name' => 'TMC Gulshan-e-Iqbal', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Only a public social-media account is known (X: @tmc_gulshan) — not treated as an official complaint channel.', 'citizen_visible' => false],
            ['id' => 'tmc_chanesar', 'name' => 'TMC Chanesar', 'short_name' => 'TMC Chanesar', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_sohrab_goth', 'name' => 'TMC Sohrab Goth', 'short_name' => 'TMC Sohrab Goth', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_safoora', 'name' => 'TMC Safoora', 'short_name' => 'TMC Safoora', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_nazimabad', 'name' => 'TMC Nazimabad', 'short_name' => 'TMC Nazimabad', 'kind' => 'tmc', 'district' => 'Central', 'website' => 'https://tmcnazimabad.gos.pk', 'website_verified' => true, 'email' => 'tmcnazimabad25@gmail.com', 'email_verified' => true, 'phone' => '021-99260342', 'phone_verified' => true, 'secondary_phone' => '0312-1117088', 'address' => 'Near Gujjar Nala, Shahrah-e-Ibn-e-Sina Road, Nazimabad No. 2', 'notes' => 'One of the few TMCs with a fully verified official complaint channel.', 'citizen_visible' => true],
            ['id' => 'tmc_north_nazimabad', 'name' => 'TMC North Nazimabad', 'short_name' => 'TMC North Nazimabad', 'kind' => 'tmc', 'district' => 'Central', 'website' => 'https://tmcnorthnazimabad.gos.pk', 'website_verified' => true, 'email' => 'info@tmc-nn.gos.pk', 'email_verified' => true, 'phone' => '0213-99260366', 'phone_verified' => true, 'secondary_phone' => null, 'address' => 'ST-04, Nazim Street, Block-A, North Nazimabad, opposite Sindh Rangers Hospital', 'notes' => null, 'citizen_visible' => true],
            ['id' => 'tmc_liaquatabad', 'name' => 'TMC Liaquatabad', 'short_name' => 'TMC Liaquatabad', 'kind' => 'tmc', 'district' => 'Central', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_gulberg', 'name' => 'TMC Gulberg', 'short_name' => 'TMC Gulberg', 'kind' => 'tmc', 'district' => 'Central', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_new_karachi', 'name' => 'TMC New Karachi', 'short_name' => 'TMC New Karachi', 'kind' => 'tmc', 'district' => 'Central', 'website' => 'https://tmcnewkarachi.gos.pk', 'website_verified' => true, 'email' => 'tmcnewkarachi@gmail.com', 'email_verified' => true, 'phone' => '0300-8995331', 'phone_verified' => true, 'secondary_phone' => '0300-7001714', 'address' => null, 'notes' => 'Site also displays 0313-9299666; role unverified before showing as a complaint number.', 'citizen_visible' => true],
            ['id' => 'tmc_orangi', 'name' => 'TMC Orangi', 'short_name' => 'TMC Orangi', 'kind' => 'tmc', 'district' => 'West', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_mominabad', 'name' => 'TMC Mominabad', 'short_name' => 'TMC Mominabad', 'kind' => 'tmc', 'district' => 'West', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => '+92 345 2154417', 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Confirm directly with the TMC before treating as a live complaint line.', 'citizen_visible' => false],
            ['id' => 'tmc_manghopir', 'name' => 'TMC Manghopir', 'short_name' => 'TMC Manghopir', 'kind' => 'tmc', 'district' => 'West', 'website' => 'https://www.tmcmangopir.gos.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => '0300-9231641', 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Listed email is a generic, unverified gmail address — not used for automated submissions.', 'citizen_visible' => false],
            ['id' => 'tmc_keamari', 'name' => 'TMC Keamari', 'short_name' => 'TMC Keamari', 'kind' => 'tmc', 'district' => 'Keamari', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Was absent from the v1 source; high priority for gazetteer coverage.', 'citizen_visible' => false],
            ['id' => 'tmc_site', 'name' => 'TMC SITE', 'short_name' => 'TMC SITE', 'kind' => 'tmc', 'district' => 'Keamari', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Not to be confused with the SITE industrial association.', 'citizen_visible' => false],
            ['id' => 'tmc_baldia', 'name' => 'TMC Baldia', 'short_name' => 'TMC Baldia', 'kind' => 'tmc', 'district' => 'Keamari', 'website' => 'https://tmcbaldia.gos.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Website observed but not yet validated as current.', 'citizen_visible' => false],
            ['id' => 'tmc_korangi', 'name' => 'TMC Korangi', 'short_name' => 'TMC Korangi', 'kind' => 'tmc', 'district' => 'Korangi', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_landhi', 'name' => 'TMC Landhi', 'short_name' => 'TMC Landhi', 'kind' => 'tmc', 'district' => 'Korangi', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_shah_faisal', 'name' => 'TMC Shah Faisal', 'short_name' => 'TMC Shah Faisal', 'kind' => 'tmc', 'district' => 'Korangi', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_model_colony', 'name' => 'TMC Model Colony', 'short_name' => 'TMC Model Colony', 'kind' => 'tmc', 'district' => 'Korangi', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_malir', 'name' => 'TMC Malir', 'short_name' => 'TMC Malir', 'kind' => 'tmc', 'district' => 'Malir', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Some sources render this as "Malik Town" — treated as the same entity pending confirmation.', 'citizen_visible' => false],
            ['id' => 'tmc_gadap', 'name' => 'TMC Gadap', 'short_name' => 'TMC Gadap', 'kind' => 'tmc', 'district' => 'Malir', 'website' => 'https://tmcgadap.gos.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Website reported but not yet validated.', 'citizen_visible' => false],
            ['id' => 'tmc_ibrahim_hyderi', 'name' => 'TMC Ibrahim Hyderi', 'short_name' => 'TMC Ibrahim Hyderi', 'kind' => 'tmc', 'district' => 'Malir', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_bin_qasim', 'name' => 'TMC Bin Qasim', 'short_name' => 'TMC Bin Qasim', 'kind' => 'tmc', 'district' => 'Malir', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
        ];

        foreach ($authorities as $authority) {
            Authority::query()->create($authority);
        }
    }

    private function seedRoutingRules(): void
    {
        $rules = [
            ['issue_type' => 'garbage', 'primary_authority_id' => 'sswmb', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => "Municipal garbage, overflowing bins, and illegal dumping go to SSWMB's 24/7 helpline 1128. If the location is inside a cantonment, that board's own sanitation service is the primary route instead."],
            ['issue_type' => 'construction_debris', 'primary_authority_id' => 'kmc', 'co_authority_ids' => ['sbca'], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Malba and building material dumped on a road or footpath goes to KMC 1339. Never SSWMB — it has publicly stated debris is not its job. Co-route to SBCA when a builder or under-construction building is the source.'],
            ['issue_type' => 'sewer_overflow', 'primary_authority_id' => 'kwsc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Sewage overflow, choked sewers, and open manholes go to KWSC 1334. Include a photo and exact location. A visibly storm-water drain (not sewage) routes to KMC/TMC instead. Cantonments run their own sewerage.'],
            ['issue_type' => 'water_supply', 'primary_authority_id' => 'kwsc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Burst mains, leaks, no water, and low or dirty water pressure go to KWSC 1334. Cantonments run their own water supply and are routed there instead.'],
            ['issue_type' => 'tanker', 'primary_authority_id' => 'kwsc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Official tanker booking and tariff questions go through the KWSC Unified app or 1334. Always label official KWSC rates separately from private-market quotes.'],
            ['issue_type' => 'road_damage', 'primary_authority_id' => 'kmc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => true, 'flags' => ['road_ownership_uncertain'], 'rule_note' => 'Potholes and damaged local streets go to the mapped TMC; major roads, flyovers, and KMC-controlled footpaths go to KMC 1339. KMC-versus-TMC road ownership has no public register, so this is logged as uncertain whenever an internal street resolves against a mapped TMC.'],
            ['issue_type' => 'streetlight', 'primary_authority_id' => 'kmc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'A dead street light is publicly documented as a KMC 1339 issue, not K-Electric. K-Electric only handles supply-side faults. Cantonments and managed estates run their own lighting.'],
            ['issue_type' => 'electrical_hazard', 'primary_authority_id' => 'ke', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Sparking wires, exposed cables, leaning poles, and transformer faults go to K-Electric 118 and are always marked as an emergency with an exposed_wire hazard.'],
            ['issue_type' => 'encroachment', 'primary_authority_id' => 'kmc', 'co_authority_ids' => ['dc_office'], 'internal_street_goes_to_tmc' => true, 'flags' => ['multi_agency_possible'], 'rule_note' => 'Main-road encroachment goes to KMC 1339 with the district DC/AC office as a co-recipient; an internal street goes first to the mapped TMC. Encroachment has no single owner — the Sindh CM has directed KMC, TMCs, police, and district administration to act jointly, so this always returns multiple recipients.'],
            ['issue_type' => 'illegal_construction', 'primary_authority_id' => 'sbca', 'co_authority_ids' => ['kmc'], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Unauthorised construction and illegal floor additions go to SBCA. Co-route to KMC (or the mapped TMC) when it also obstructs a public road or footpath.'],
            ['issue_type' => 'storm_drain', 'primary_authority_id' => 'kmc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => ['multi_agency_possible'], 'rule_note' => 'Blocked storm-water drains and nullah flooding go to KMC first. Some major drains involve provincial irrigation bodies, so this is logged as possibly needing more than one agency. Never route a storm drain to SSWMB as if it were garbage.'],
            ['issue_type' => 'park_amenity', 'primary_authority_id' => 'kmc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'KMC parks, beaches, and civic amenities go to KMC, though some have historically been transferred to KDA and should be verified.'],
            ['issue_type' => 'unknown', 'primary_authority_id' => 'pmdu', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => "When the report is unresolvable, ask one clarifying question first. If it's still unclear, fall back to the Pakistan Citizen Portal rather than silently dropping the report."],
        ];

        foreach ($rules as $rule) {
            RoutingRule::query()->create($rule);
        }
    }

    private function seedGazetteer(): void
    {
        // Districts (top of the normal hierarchy).
        $districts = ['South', 'East', 'Central', 'West', 'Korangi', 'Malir', 'Keamari'];

        foreach ($districts as $district) {
            $this->node($district, ['kind' => 'district', 'district' => $district]);
        }

        // Towns / TMCs — parented to their claimed district.
        //
        // Aliases carry the Urdu-script and Roman-Urdu spellings a resident is
        // likely to type. Report::resolveLocation() lowercases the report text
        // and substring-matches these, and input_language accepts ur and
        // roman_urdu, so a town without them simply will not resolve from an
        // Urdu report.
        $towns = [
            ['name' => 'Saddar', 'aliases' => ['صدر', 'Sadar'], 'authority' => 'tmc_saddar', 'district' => 'South'],
            ['name' => 'Lyari', 'aliases' => ['لیاری', 'Liyari', 'Lyari Town'], 'authority' => 'tmc_lyari', 'district' => 'South'],
            ['name' => 'Jamshed', 'aliases' => ['جمشید', 'Jamshed Town', 'Jamshed Quarters'], 'authority' => 'tmc_jamshed', 'district' => 'East'],
            ['name' => 'Jinnah', 'aliases' => ['جناح', 'Jinnah Town'], 'authority' => 'tmc_jinnah', 'district' => 'East'],
            ['name' => 'Gulshan-e-Iqbal', 'aliases' => ['Gulshan e Iqbal', 'Gulshan', 'گلشن اقبال', 'گلشن', 'Gulshan Iqbal'], 'authority' => 'tmc_gulshan_e_iqbal', 'district' => 'East'],
            ['name' => 'Chanesar', 'aliases' => ['چنیسر', 'Chanesar Town'], 'authority' => 'tmc_chanesar', 'district' => 'East'],
            ['name' => 'Sohrab Goth', 'aliases' => ['سہراب گوٹھ', 'Sorab Goth'], 'authority' => 'tmc_sohrab_goth', 'district' => 'East'],
            ['name' => 'Safoora', 'aliases' => ['صفورا', 'Safoora Goth', 'Safura'], 'authority' => 'tmc_safoora', 'district' => 'East'],
            ['name' => 'Nazimabad', 'aliases' => ['ناظم آباد', 'Nazimabad Town'], 'authority' => 'tmc_nazimabad', 'district' => 'Central'],
            ['name' => 'North Nazimabad', 'aliases' => ['NNB', 'North Nazimabad Town', 'نارتھ ناظم آباد', 'Nazimabad North'], 'authority' => 'tmc_north_nazimabad', 'district' => 'Central'],
            ['name' => 'Liaquatabad', 'aliases' => ['لیاقت آباد', 'Lalukhet', 'Liaqatabad'], 'authority' => 'tmc_liaquatabad', 'district' => 'Central'],
            ['name' => 'Gulberg', 'aliases' => ['گلبرگ', 'Gulberg Town'], 'authority' => 'tmc_gulberg', 'district' => 'Central'],
            ['name' => 'New Karachi', 'aliases' => ['نیو کراچی', 'New Karachi Town'], 'authority' => 'tmc_new_karachi', 'district' => 'Central'],
            ['name' => 'Orangi Town', 'aliases' => ['Orangi', 'اورنگی', 'Orangi Township'], 'authority' => 'tmc_orangi', 'district' => 'West'],
            ['name' => 'Mominabad', 'aliases' => ['مومن آباد', 'Momin Abad'], 'authority' => 'tmc_mominabad', 'district' => 'West'],
            ['name' => 'Manghopir', 'aliases' => ['منگھوپیر', 'Mangopir'], 'authority' => 'tmc_manghopir', 'district' => 'West'],
            ['name' => 'Keamari', 'aliases' => ['کیماڑی', 'Kemari', 'Keamari Town', 'Kiamari'], 'authority' => 'tmc_keamari', 'district' => 'Keamari'],
            ['name' => 'SITE', 'aliases' => ['سائٹ', 'SITE Town', 'Sind Industrial Trading Estate'], 'authority' => 'tmc_site', 'district' => 'Keamari'],
            ['name' => 'Baldia', 'aliases' => ['بلدیہ', 'Baldia Town'], 'authority' => 'tmc_baldia', 'district' => 'Keamari'],
            ['name' => 'Korangi', 'aliases' => ['کورنگی', 'Korangi Town'], 'authority' => 'tmc_korangi', 'district' => 'Korangi'],
            ['name' => 'Landhi', 'aliases' => ['لانڈھی', 'Landhi Town'], 'authority' => 'tmc_landhi', 'district' => 'Korangi'],
            ['name' => 'Shah Faisal', 'aliases' => ['شاہ فیصل', 'Shah Faisal Town'], 'authority' => 'tmc_shah_faisal', 'district' => 'Korangi'],
            ['name' => 'Model Colony', 'aliases' => ['ماڈل کالونی'], 'authority' => 'tmc_model_colony', 'district' => 'Korangi'],
            ['name' => 'Malir Town', 'aliases' => ['Malik Town', 'ملیر', 'Malir'], 'authority' => 'tmc_malir', 'district' => 'Malir'],
            ['name' => 'Gadap', 'aliases' => ['گڈاپ', 'Gadap Town'], 'authority' => 'tmc_gadap', 'district' => 'Malir'],
            ['name' => 'Ibrahim Hyderi', 'aliases' => ['ابراہیم حیدری', 'Ibrahim Haidari'], 'authority' => 'tmc_ibrahim_hyderi', 'district' => 'Malir'],
            ['name' => 'Bin Qasim', 'aliases' => ['بن قاسم', 'Bin Qasim Town', 'Port Qasim Town'], 'authority' => 'tmc_bin_qasim', 'district' => 'Malir'],
            // Clifton has no TMC of its own — non-override blocks fall through to KMC directly.
            ['name' => 'Clifton', 'aliases' => ['کلفٹن'], 'authority' => null, 'district' => 'South'],
        ];

        foreach ($towns as $town) {
            $this->node($town['name'], [
                'kind' => 'town',
                'aliases' => $town['aliases'] ?? [],
                'district' => $town['district'],
                'parent_id' => $this->nodeIds[$town['district']],
                'tmc_authority_id' => $town['authority'],
            ]);
        }

        // Six cantonment special zones — top-level, override the district hierarchy.
        $zones = [
            ['name' => 'Cantonment Board Clifton', 'aliases' => ['CBC', 'Clifton Cantonment', 'DHA', 'Defence', 'DHA Karachi'], 'authority' => 'cb_clifton'],
            ['name' => 'Karachi Cantonment', 'aliases' => ['Karachi Cantt', 'Saddar Cantonment'], 'authority' => 'cb_karachi'],
            ['name' => 'Cantonment Board Faisal', 'aliases' => ['Drigh Road Cantonment', 'PAF Faisal', 'Faisal Cantonment'], 'authority' => 'cb_faisal'],
            ['name' => 'Malir Cantonment', 'aliases' => ['Malir Cantt'], 'authority' => 'cb_malir'],
            ['name' => 'Korangi Creek Cantonment', 'aliases' => ['CBKC', 'Korangi Creek'], 'authority' => 'cb_korangi_creek'],
            ['name' => 'Manora Cantonment', 'aliases' => ['Manora Island', 'Manora'], 'authority' => 'cb_manora'],
        ];

        foreach ($zones as $zone) {
            $this->node($zone['name'], [
                'kind' => 'special_zone',
                'aliases' => $zone['aliases'],
                'special_zone_authority_id' => $zone['authority'],
            ]);
        }

        // DHA Phases 1-8 and Clifton Blocks 8-9 — children of the CBC override.
        for ($phase = 1; $phase <= 8; $phase++) {
            $this->node("DHA Phase {$phase}", [
                'kind' => 'landmark',
                'aliases' => ["DHA {$phase}", "Phase {$phase}"],
                'parent_id' => $this->nodeIds['Cantonment Board Clifton'],
            ]);
        }

        foreach ([8, 9] as $block) {
            $this->node("Clifton Block {$block}", [
                'kind' => 'landmark',
                'parent_id' => $this->nodeIds['Cantonment Board Clifton'],
            ]);
        }

        // Clifton Blocks 1-7 — not covered by CBC, fall through to the Clifton/KMC route.
        for ($block = 1; $block <= 7; $block++) {
            $this->node("Clifton Block {$block}", [
                'kind' => 'landmark',
                'parent_id' => $this->nodeIds['Clifton'],
            ]);
        }

        $this->node('Boat Basin', [
            'kind' => 'landmark',
            'parent_id' => $this->nodeIds['Clifton Block 2'],
        ]);

        // DHA City — a separate scheme from DHA Phases 1-8, flagged for human review.
        //
        // The node name is deliberately longer than 'Cantonment Board Clifton'.
        // CBC carries the aliases 'DHA' and 'DHA Karachi', both of which
        // substring-match inside 'DHA City Karachi'. Both nodes are
        // special_zone, so resolveLocation() breaks the tie on longest name —
        // a shorter name here would silently route DHA City to CBC with high
        // confidence, which is the exact mistake the source doc warns about.
        $this->node('DHA City Karachi Super Highway Scheme', [
            'kind' => 'special_zone',
            'aliases' => ['DHA City Karachi', 'DHA City'],
            'needs_human_review' => true,
        ]);

        $this->node('DHA City Karachi Sector 9', [
            'kind' => 'landmark',
            'aliases' => ['Sector 9 DHA City', 'DHA City Sector 9'],
            'parent_id' => $this->nodeIds['DHA City Karachi Super Highway Scheme'],
        ]);

        $this->node('Saudabad', [
            'kind' => 'landmark',
            'parent_id' => $this->nodeIds['Malir Cantonment'],
        ]);

        // Landmarks the demo seed reports resolve against, plus general
        // gazetteer breadth per town so the name graph is genuinely usable.
        $landmarks = [
            ['name' => 'Nazimabad No. 2', 'aliases' => ['Nazimabad Number 2'], 'parent' => 'Nazimabad'],
            ['name' => 'Gujjar Nala', 'parent' => 'Nazimabad'],
            ['name' => 'North Nazimabad Block H', 'aliases' => ['Block H North Nazimabad'], 'parent' => 'North Nazimabad'],
            ['name' => 'North Nazimabad Block L', 'parent' => 'North Nazimabad'],
            ['name' => 'Gulshan-e-Iqbal Block 13-D', 'aliases' => ['Block 13-D Gulshan', 'Gulshan Block 13 D'], 'parent' => 'Gulshan-e-Iqbal'],
            ['name' => 'Gulshan-e-Iqbal Block 2', 'parent' => 'Gulshan-e-Iqbal'],
            ['name' => 'Tariq Road', 'aliases' => ['Dolmen Tariq Road', 'طارق روڈ'], 'parent' => 'Jamshed'],

            // Lyari — the source doc flags this as the top coverage priority:
            // absent from v1, high complaint density, 11-13 UCs. These are the
            // neighbourhood names residents actually use.
            ['name' => 'Chakiwara', 'aliases' => ['چاکیواڑہ', 'Chakiwara Lyari'], 'parent' => 'Lyari'],
            ['name' => 'Baghdadi', 'aliases' => ['بغدادی'], 'parent' => 'Lyari'],
            ['name' => 'Kalri', 'aliases' => ['کھارادر کلری', 'Kalri Lyari'], 'parent' => 'Lyari'],
            ['name' => 'Agra Taj Colony', 'aliases' => ['Agra Taj', 'آگرہ تاج'], 'parent' => 'Lyari'],
            ['name' => 'Nawa Lane', 'aliases' => ['نوا لین', 'Nawalane'], 'parent' => 'Lyari'],
            ['name' => 'Singo Lane', 'aliases' => ['سنگو لین'], 'parent' => 'Lyari'],
            ['name' => 'Shah Beg Lane', 'aliases' => ['شاہ بیگ لین'], 'parent' => 'Lyari'],
            ['name' => 'Rexer Lane', 'aliases' => ['ریکسر لین'], 'parent' => 'Lyari'],
            ['name' => 'Bihar Colony', 'aliases' => ['بہار کالونی'], 'parent' => 'Lyari'],
            ['name' => 'Kalakot', 'aliases' => ['کالاکوٹ'], 'parent' => 'Lyari'],
            ['name' => 'Usmanabad', 'aliases' => ['عثمان آباد'], 'parent' => 'Lyari'],
            ['name' => 'Gul Muhammad Lane', 'aliases' => ['گل محمد لین'], 'parent' => 'Lyari'],
            ['name' => 'Orangi Town Sector 11', 'aliases' => ['Sector 11 Orangi', 'Sector 11 1/2'], 'parent' => 'Orangi Town'],
            ['name' => 'Korangi Sector 33', 'aliases' => ['Sector 33 Korangi'], 'parent' => 'Korangi'],
            ['name' => 'Korangi Sector 31', 'parent' => 'Korangi'],
            ['name' => 'Landhi Sector 5', 'parent' => 'Landhi'],
            ['name' => 'Shah Faisal Colony', 'parent' => 'Shah Faisal'],
            ['name' => 'Model Colony Malir', 'parent' => 'Model Colony'],
            ['name' => 'Malir 15', 'aliases' => ['Malir Fifteen'], 'parent' => 'Malir Town'],
            ['name' => 'Gadap Town Centre', 'parent' => 'Gadap'],
            ['name' => 'Ibrahim Hyderi Fish Harbour', 'parent' => 'Ibrahim Hyderi'],
            ['name' => 'Bin Qasim Town Centre', 'parent' => 'Bin Qasim'],
            ['name' => 'SITE Area', 'aliases' => ['سائٹ ایریا'], 'parent' => 'SITE'],
            ['name' => 'Baldia Town Centre', 'parent' => 'Baldia'],
            ['name' => 'Saeedabad', 'aliases' => ['سعید آباد'], 'parent' => 'Baldia'],

            // Keamari — the other town the source doc names as priority; it was
            // also absent from v1 and had a single node before this pass.
            ['name' => 'Keamari Fish Harbour', 'aliases' => ['Fish Harbour', 'مچھلی بندرگاہ'], 'parent' => 'Keamari'],
            ['name' => 'Machar Colony', 'aliases' => ['مچھر کالونی', 'Machhar Colony'], 'parent' => 'Keamari'],
            ['name' => 'Maripur', 'aliases' => ['ماڑی پور', 'Mauripur', 'Maripur Road'], 'parent' => 'Keamari'],
            ['name' => 'Shershah', 'aliases' => ['شیرشاہ', 'Sher Shah'], 'parent' => 'Keamari'],
            ['name' => 'Jackson Market', 'aliases' => ['جیکسن مارکیٹ', 'Jackson Bazaar'], 'parent' => 'Keamari'],
            ['name' => 'Sultanabad', 'aliases' => ['سلطان آباد'], 'parent' => 'Keamari'],
            ['name' => 'Baba Bhit', 'aliases' => ['بابا بھٹ', 'Baba Island'], 'parent' => 'Keamari'],
            ['name' => 'Shams Pir', 'aliases' => ['شمس پیر'], 'parent' => 'Keamari'],
            ['name' => 'Gabopat', 'aliases' => ['گابوپٹ'], 'parent' => 'Keamari'],
            ['name' => 'Manghopir Road', 'parent' => 'Manghopir'],
            ['name' => 'Mominabad Chowk', 'parent' => 'Mominabad'],
            ['name' => 'Liaquatabad No. 10', 'parent' => 'Liaquatabad'],
            ['name' => 'Gulberg Chowrangi', 'parent' => 'Gulberg'],
            ['name' => 'New Karachi Sector 5-C', 'parent' => 'New Karachi'],
            ['name' => 'Sohrab Goth Chowk', 'parent' => 'Sohrab Goth'],
            ['name' => 'Safoora Chowrangi', 'parent' => 'Safoora'],
            ['name' => 'Chanesar Goth', 'parent' => 'Chanesar'],
            ['name' => 'Saddar Empress Market', 'aliases' => ['Empress Market'], 'parent' => 'Saddar'],
            ['name' => 'Bahadurabad', 'parent' => 'Jamshed'],

            // Extra depth for Lyari and Keamari — highest priority per the
            // source doc (highest complaint density / missing from v1).
            ['name' => 'Kalakot', 'parent' => 'Lyari'],
            ['name' => 'Agra Taj Colony', 'parent' => 'Lyari'],
            ['name' => 'Bihar Colony', 'parent' => 'Lyari'],
            ['name' => 'Mauripur', 'parent' => 'Keamari'],
            ['name' => 'Native Jetty Bridge', 'parent' => 'Keamari'],
            ['name' => 'Hawksbay', 'aliases' => ['Hawke\'s Bay'], 'parent' => 'Keamari'],
            ['name' => 'Bin Qasim Sector 2', 'parent' => 'Bin Qasim'],
            ['name' => 'Landhi Sector 7', 'parent' => 'Landhi'],
            ['name' => 'Shah Faisal Sector 44', 'parent' => 'Shah Faisal'],
            ['name' => 'Manzoor Colony', 'parent' => 'Model Colony'],
        ];

        foreach ($landmarks as $landmark) {
            $this->node($landmark['name'], [
                'kind' => 'landmark',
                'aliases' => $landmark['aliases'] ?? [],
                'parent_id' => $this->nodeIds[$landmark['parent']],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function node(string $name, array $attributes): void
    {
        $node = GazetteerNode::query()->create([
            'name' => $name,
            'aliases' => $attributes['aliases'] ?? [],
            'kind' => $attributes['kind'],
            'parent_id' => $attributes['parent_id'] ?? null,
            'district' => $attributes['district'] ?? null,
            'tmc_authority_id' => $attributes['tmc_authority_id'] ?? null,
            'special_zone_authority_id' => $attributes['special_zone_authority_id'] ?? null,
            'needs_human_review' => $attributes['needs_human_review'] ?? false,
        ]);

        $this->nodeIds[$name] = $node->id;
    }
}
