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
        // is_admin gates the Filament panel. The citizen account below is the
        // proof it was needed: before this flag it could sign straight into
        // /admin and edit routing rules.
        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@cityaround.pk',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        User::query()->create([
            'name' => 'Citizen',
            'email' => 'citizen@cityaround.pk',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $this->seedAuthorities();
        $this->seedRoutingRules();
        $this->seedGazetteer();
        $this->seedUnionCouncils();
    }

    private function seedAuthorities(): void
    {
        $authorities = [
            // Citywide, SBCA, DC office, PMDU fallback.
            ['id' => 'kmc', 'name' => 'Karachi Metropolitan Corporation', 'short_name' => 'KMC', 'kind' => 'citywide', 'website' => 'https://kmc.gos.pk', 'website_verified' => true, 'email' => 'mayor@kmc.gos.pk', 'email_verified' => false, 'phone' => '1339', 'phone_verified' => true, 'secondary_phone' => '+92 21 992 1511-7', 'address' => '1st Floor, M.A. Jinnah Road, Karachi', 'notes' => '1339 (Citizens Complaints Information System) publicly covers sewage complaints, road potholes and non-functional street lights. E-KMC: https://kmc.gos.pk/e-kmc/.', 'citizen_visible' => true],
            ['id' => 'kwsc', 'name' => 'Karachi Water & Sewerage Corporation', 'short_name' => 'KWSC', 'kind' => 'citywide', 'website' => 'https://www.kwsc.gos.pk', 'website_verified' => true, 'email' => 'info@kwsb.gos.pk', 'email_verified' => false, 'phone' => '1334', 'phone_verified' => true, 'secondary_phone' => '(+92) 021 111 597 200', 'address' => '9th Mile Karsaz, Main Shahrah-e-Faisal, Karachi-75350', 'notes' => 'Legacy name KWSB still used by residents; the legacy kwsb.gos.pk domain is still live. "KWSC Unified" single-app system for tanker booking went live 1 Feb 2026.', 'citizen_visible' => true],
            ['id' => 'sswmb', 'name' => 'Sindh Solid Waste Management Board', 'short_name' => 'SSWMB', 'kind' => 'citywide', 'website' => 'https://sswmb.gos.pk', 'website_verified' => true, 'email' => null, 'email_verified' => false, 'phone' => '1128', 'phone_verified' => true, 'secondary_phone' => '+92 21 99333710-03', 'address' => '3rd Floor, DMC (South) Building, opposite Aram Bagh Police Station, near Haqqani Chowk, District South, Karachi-74200', 'notes' => 'Does not handle construction debris or building material, only garbage. Do not route storm-water drain desilting here.', 'citizen_visible' => true],
            ['id' => 'ke', 'name' => 'K-Electric', 'short_name' => 'KE', 'kind' => 'citywide', 'website' => 'https://www.ke.com.pk', 'website_verified' => true, 'email' => 'customer.care@ke.com.pk', 'email_verified' => true, 'phone' => '118', 'phone_verified' => true, 'secondary_phone' => null, 'address' => null, 'notes' => 'Street lights are KMC, not KE. Route dead streetlights to 1339 and live/sparking wires to 118.', 'citizen_visible' => true],
            ['id' => 'sbca', 'name' => 'Sindh Building Control Authority', 'short_name' => 'SBCA', 'kind' => 'citywide', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Repeatedly named by the Sindh CM as the authority for builder undertakings, illegal commercial conversion, and plaza-parking restoration. Co-route with KMC/DC for builder-generated encroachment and debris. No verified public complaint channel yet.', 'citizen_visible' => false],
            ['id' => 'dc_office', 'name' => 'Deputy Commissioner / Assistant Commissioner (district administration)', 'short_name' => 'DC/AC Office', 'kind' => 'citywide', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Named as a co-recipient on encroachment complaints, never as sole recipient. Per-district contacts not yet verified.', 'citizen_visible' => false],
            ['id' => 'pmdu', 'name' => 'Pakistan Citizen Portal', 'short_name' => 'PMDU', 'kind' => 'fallback', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Federal grievance system routing to federal and provincial organisations, submitted via a government mobile app/portal rather than phone or email. Escalation/fallback channel when ownership is unclear or a local authority has not responded. Not a direct emergency response service.', 'citizen_visible' => true],
            // 27 Town Municipal Corporations.
            ['id' => 'tmc_saddar', 'name' => 'TMC Saddar', 'short_name' => 'TMC Saddar', 'kind' => 'tmc', 'district' => 'South', 'website' => 'https://tmcsaddar.gos.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'V225+MQ4, Haqqani Chowk, New Chali, near Ocean Mall, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. Covers Saddar, Civil Lines and parts of Clifton per that dataset. Its UC list there is a copy of Orangi\'s and was not loaded.', 'citizen_visible' => false],
            ['id' => 'tmc_lyari', 'name' => 'TMC Lyari', 'short_name' => 'TMC Lyari', 'kind' => 'tmc', 'district' => 'South', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'High complaint density, 11-13 UCs. Priority for gazetteer coverage.', 'citizen_visible' => false],
            ['id' => 'tmc_jamshed', 'name' => 'TMC Jamshed', 'short_name' => 'TMC Jamshed', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'May be the same town as TMC Jinnah under two names — unresolved in the source data. Do not route to both. The 12 Sep 2026 TMC dataset entry for Jamshed carries TMC Jinnah\'s website and email and TMC Chanesar\'s map pin, so none of it was loaded here.', 'citizen_visible' => false],
            ['id' => 'tmc_jinnah', 'name' => 'TMC Jinnah', 'short_name' => 'TMC Jinnah', 'kind' => 'tmc', 'district' => 'East', 'website' => 'https://tmcjinnah.gos.pk', 'website_verified' => false, 'email' => 'tmcjinnah3@gmail.com', 'email_verified' => false, 'phone' => '0316 3779792', 'phone_verified' => false, 'secondary_phone' => '021-992131355-59', 'address' => 'First Floor, CMO Office, Secretariat Chowrangi, Mufti Ahmed Ur Rehman Road, near Tayyaba Masjid, Amil Colony, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. May be the same town as TMC Jamshed under two names — unresolved in the source data.', 'citizen_visible' => false],
            ['id' => 'tmc_gulshan_e_iqbal', 'name' => 'TMC Gulshan-e-Iqbal', 'short_name' => 'TMC Gulshan-e-Iqbal', 'kind' => 'tmc', 'district' => 'East', 'website' => 'https://tmcgulshan.gos.pk', 'website_verified' => false, 'email' => 'tmcgulshan@gmail.com', 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'Near Civic Centre, University Road, Block 14, Gulshan-e-Iqbal, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. Public social-media account (X: @tmc_gulshan) is not treated as an official complaint channel.', 'citizen_visible' => false],
            ['id' => 'tmc_chanesar', 'name' => 'TMC Chanesar', 'short_name' => 'TMC Chanesar', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_sohrab_goth', 'name' => 'TMC Sohrab Goth', 'short_name' => 'TMC Sohrab Goth', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_safoora', 'name' => 'TMC Safoora', 'short_name' => 'TMC Safoora', 'kind' => 'tmc', 'district' => 'East', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => null, 'citizen_visible' => false],
            ['id' => 'tmc_nazimabad', 'name' => 'TMC Nazimabad', 'short_name' => 'TMC Nazimabad', 'kind' => 'tmc', 'district' => 'Central', 'website' => 'https://tmcnazimabad.gos.pk', 'website_verified' => true, 'email' => 'tmcnazimabad25@gmail.com', 'email_verified' => true, 'phone' => '021-99260342', 'phone_verified' => true, 'secondary_phone' => '0312-1117088', 'address' => 'Near Gujjar Nala, Shahrah-e-Ibn-e-Sina Road, Nazimabad No. 2', 'notes' => 'One of the few TMCs with a fully verified official complaint channel.', 'citizen_visible' => true],
            ['id' => 'tmc_north_nazimabad', 'name' => 'TMC North Nazimabad', 'short_name' => 'TMC North Nazimabad', 'kind' => 'tmc', 'district' => 'Central', 'website' => 'https://tmcnorthnazimabad.gos.pk', 'website_verified' => true, 'email' => 'info@tmc-nn.gos.pk', 'email_verified' => true, 'phone' => '0213-99260366', 'phone_verified' => true, 'secondary_phone' => null, 'address' => 'ST-04, Nazim Street, Block-A, North Nazimabad, opposite Sindh Rangers Hospital', 'notes' => null, 'citizen_visible' => true],
            ['id' => 'tmc_liaquatabad', 'name' => 'TMC Liaquatabad', 'short_name' => 'TMC Liaquatabad', 'kind' => 'tmc', 'district' => 'Central', 'website' => 'https://tmcliaquatabad.gos.pk', 'website_verified' => false, 'email' => 'tmcliaqutabad@gmail.com', 'email_verified' => false, 'phone' => '(021) 99260388', 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'Liaquatabad Town Office, Sir Shah Muhammad Suleman Road, Nazimabad No. 4', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified.', 'citizen_visible' => false],
            ['id' => 'tmc_gulberg', 'name' => 'TMC Gulberg', 'short_name' => 'TMC Gulberg', 'kind' => 'tmc', 'district' => 'Central', 'website' => 'https://tmcgulberg.gos.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => '0324 2920766', 'phone_verified' => false, 'secondary_phone' => null, 'address' => '1185 Rashid Minhas Road, Federal B Area Block 16, Gulberg Town, Karachi 75950', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified.', 'citizen_visible' => false],
            ['id' => 'tmc_new_karachi', 'name' => 'TMC New Karachi', 'short_name' => 'TMC New Karachi', 'kind' => 'tmc', 'district' => 'Central', 'website' => 'https://tmcnewkarachi.gos.pk', 'website_verified' => true, 'email' => 'tmcnewkarachi@gmail.com', 'email_verified' => true, 'phone' => '0300-8995331', 'phone_verified' => true, 'secondary_phone' => '0300-7001714', 'address' => null, 'notes' => 'Site also displays 0313-9299666; role unverified before showing as a complaint number.', 'citizen_visible' => true],
            ['id' => 'tmc_orangi', 'name' => 'TMC Orangi', 'short_name' => 'TMC Orangi', 'kind' => 'tmc', 'district' => 'West', 'website' => 'https://tmcorangi.gos.pk', 'website_verified' => false, 'email' => 'tmc@orangitown.com', 'email_verified' => false, 'phone' => '0331 2802784', 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'Near 5 Number Chowrangi, 11-E, Muhammad Nagar, Sector 11-E, Orangi Town, Karachi 75800', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified.', 'citizen_visible' => false],
            ['id' => 'tmc_mominabad', 'name' => 'TMC Mominabad', 'short_name' => 'TMC Mominabad', 'kind' => 'tmc', 'district' => 'West', 'website' => 'https://tmcmominabad.gos.pk', 'website_verified' => false, 'email' => 'mominabadtmc@gmail.com', 'email_verified' => false, 'phone' => '(021) 99333710', 'phone_verified' => false, 'secondary_phone' => '+92 345 2154417', 'address' => 'W2P5+J3J, Metroville Sector 5, Muhammad Nagar, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. Confirm directly with the TMC before treating as a live complaint line.', 'citizen_visible' => false],
            ['id' => 'tmc_manghopir', 'name' => 'TMC Manghopir', 'short_name' => 'TMC Manghopir', 'kind' => 'tmc', 'district' => 'West', 'website' => 'https://www.tmcmangopir.gos.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => '0321 1450314', 'phone_verified' => false, 'secondary_phone' => '0300-9231641', 'address' => 'X2QR+78R, New Karachi Town, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. 0300-9231641 is listed there as the IT/payroll in-charge\'s mobile, not an office line.', 'citizen_visible' => false],
            ['id' => 'tmc_keamari', 'name' => 'TMC Keamari', 'short_name' => 'TMC Keamari', 'kind' => 'tmc', 'district' => 'Keamari', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Was absent from the v1 source; high priority for gazetteer coverage.', 'citizen_visible' => false],
            ['id' => 'tmc_site', 'name' => 'TMC SITE', 'short_name' => 'TMC SITE', 'kind' => 'tmc', 'district' => 'Keamari', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'Not to be confused with the SITE industrial association.', 'citizen_visible' => false],
            ['id' => 'tmc_baldia', 'name' => 'TMC Baldia', 'short_name' => 'TMC Baldia', 'kind' => 'tmc', 'district' => 'Keamari', 'website' => 'https://tmcbaldia.gos.pk', 'website_verified' => false, 'email' => 'Tmcbaldia@gmail.com', 'email_verified' => false, 'phone' => '(021) 99334226', 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'Hub River Road, Sector 5, Baldia Town, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. Website observed but not yet validated as current.', 'citizen_visible' => false],
            ['id' => 'tmc_korangi', 'name' => 'TMC Korangi', 'short_name' => 'TMC Korangi', 'kind' => 'tmc', 'district' => 'Korangi', 'website' => 'https://tmckorangi.gos.pk', 'website_verified' => false, 'email' => 'tmckorangi@gmail.com', 'email_verified' => false, 'phone' => '021-99333929', 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'ST-1/3, Sector 41/B, Korangi 2-½, near Chiniot General Hospital, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified.', 'citizen_visible' => false],
            ['id' => 'tmc_landhi', 'name' => 'TMC Landhi', 'short_name' => 'TMC Landhi', 'kind' => 'tmc', 'district' => 'Korangi', 'website' => 'https://tmclandhi.gos.pk', 'website_verified' => false, 'email' => 'tmclandhi@gmail.com', 'email_verified' => false, 'phone' => '02199333979', 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'R5PJ+WFH, Sector 36-E, Landhi Town, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified.', 'citizen_visible' => false],
            ['id' => 'tmc_shah_faisal', 'name' => 'TMC Shah Faisal', 'short_name' => 'TMC Shah Faisal', 'kind' => 'tmc', 'district' => 'Korangi', 'website' => 'https://tmcshahfaisal.gos.pk', 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => '(021) 99333576', 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'V42J+9P5, Sector 8-A, Korangi Industrial Area, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. The email listed there (tmc@gmail.com) is not a plausible official address and was not loaded.', 'citizen_visible' => false],
            ['id' => 'tmc_model_colony', 'name' => 'TMC Model Colony', 'short_name' => 'TMC Model Colony', 'kind' => 'tmc', 'district' => 'Korangi', 'website' => 'https://www.tmcmodelcolony.gos.pk', 'website_verified' => false, 'email' => 'tmcmodelcolony2024@gmail.com', 'email_verified' => false, 'phone' => '02199248123', 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'Street No. 2, Saudabad Darakhshan Cooperative Housing Society, Kala Board, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified.', 'citizen_visible' => false],
            ['id' => 'tmc_malir', 'name' => 'TMC Malir', 'short_name' => 'TMC Malir', 'kind' => 'tmc', 'district' => 'Malir', 'website' => null, 'website_verified' => false, 'email' => 'TMCMalir@gmail.com', 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'V5PV+6PR, Saudabad, Ghazi Dawood Brohi Goth, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. Some sources render this as "Malik Town" — treated as the same entity pending confirmation.', 'citizen_visible' => false],
            ['id' => 'tmc_gadap', 'name' => 'TMC Gadap', 'short_name' => 'TMC Gadap', 'kind' => 'tmc', 'district' => 'Malir', 'website' => 'https://tmcgadap.gos.pk', 'website_verified' => false, 'email' => 'tmcgadapofficial@gmail.com', 'email_verified' => false, 'phone' => '021-99232593', 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'Camp office: Street 5, Block 14, Gulshan-e-Iqbal, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified. Website reported but not yet validated.', 'citizen_visible' => false],
            ['id' => 'tmc_ibrahim_hyderi', 'name' => 'TMC Ibrahim Hyderi', 'short_name' => 'TMC Ibrahim Hyderi', 'kind' => 'tmc', 'district' => 'Malir', 'website' => 'https://tmcibrahimhydri.gos.pk', 'website_verified' => false, 'email' => 'tmcibrahihyderi48@gmail.com', 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => 'V79R+GR4, National Highway – Super Highway Link Road, Razzaqabad, Bin Qasim Town, Karachi', 'notes' => 'Contacts from the 12 Sep 2026 TMC dataset, unverified.', 'citizen_visible' => false],
            ['id' => 'tmc_bin_qasim', 'name' => 'TMC Bin Qasim', 'short_name' => 'TMC Bin Qasim', 'kind' => 'tmc', 'district' => 'Malir', 'website' => null, 'website_verified' => false, 'email' => null, 'email_verified' => false, 'phone' => null, 'phone_verified' => false, 'secondary_phone' => null, 'address' => null, 'notes' => 'The 12 Sep 2026 TMC dataset entry for Bin Qasim repeats TMC Ibrahim Hyderi\'s website, email, address and UC list verbatim, so none of it was loaded here.', 'citizen_visible' => false],
        ];

        foreach ($authorities as $authority) {
            Authority::query()->create($authority);
        }
    }

    private function seedRoutingRules(): void
    {
        $rules = [
            ['issue_type' => 'garbage', 'primary_authority_id' => 'sswmb', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => "Municipal garbage, overflowing bins, and illegal dumping go to SSWMB's 24/7 helpline 1128."],
            ['issue_type' => 'construction_debris', 'primary_authority_id' => 'kmc', 'co_authority_ids' => ['sbca'], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Malba and building material dumped on a road or footpath goes to KMC 1339. Never SSWMB — it has publicly stated debris is not its job. Co-route to SBCA when a builder or under-construction building is the source.'],
            ['issue_type' => 'sewer_overflow', 'primary_authority_id' => 'kwsc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Sewage overflow, choked sewers, and open manholes go to KWSC 1334. Include a photo and exact location. A visibly storm-water drain (not sewage) routes to KMC/TMC instead.'],
            ['issue_type' => 'water_supply', 'primary_authority_id' => 'kwsc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Burst mains, leaks, no water, and low or dirty water pressure go to KWSC 1334.'],
            ['issue_type' => 'tanker', 'primary_authority_id' => 'kwsc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'Official tanker booking and tariff questions go through the KWSC Unified app or 1334. Always label official KWSC rates separately from private-market quotes.'],
            ['issue_type' => 'road_damage', 'primary_authority_id' => 'kmc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => true, 'flags' => ['road_ownership_uncertain'], 'rule_note' => 'Potholes and damaged local streets go to the mapped TMC; major roads, flyovers, and KMC-controlled footpaths go to KMC 1339. KMC-versus-TMC road ownership has no public register, so this is logged as uncertain whenever an internal street resolves against a mapped TMC.'],
            ['issue_type' => 'streetlight', 'primary_authority_id' => 'kmc', 'co_authority_ids' => [], 'internal_street_goes_to_tmc' => false, 'flags' => [], 'rule_note' => 'A dead street light is publicly documented as a KMC 1339 issue, not K-Electric. K-Electric only handles supply-side faults.'],
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

        // Clifton is not a TMC. The 12 Sep 2026 TMC dataset lists it under
        // TMC Saddar ("Clifton (parts)"), so it and its blocks hang off Saddar.
        // Cantonment boards and DHA are out of scope and have no nodes at all.
        $this->node('Clifton', [
            'kind' => 'landmark',
            'aliases' => ['کلفٹن'],
            'parent_id' => $this->nodeIds['Saddar'],
        ]);

        for ($block = 1; $block <= 9; $block++) {
            $this->node("Clifton Block {$block}", [
                'kind' => 'landmark',
                'parent_id' => $this->nodeIds['Clifton'],
            ]);
        }

        $this->node('Boat Basin', [
            'kind' => 'landmark',
            'parent_id' => $this->nodeIds['Clifton Block 2'],
        ]);

        $this->node('Saudabad', [
            'kind' => 'landmark',
            'aliases' => ['سعود آباد'],
            'parent_id' => $this->nodeIds['Malir Town'],
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
     * Union councils from data/karachi_tmc_data_integrated-v2.md (12 Sep 2026).
     * A UC is a landmark node under its town with uc_code set; the compose
     * screen's place picker lists towns and these. Names keep the file's
     * wording (trailing dots stripped, ALL-CAPS title-cased); UC codes are
     * normalised to UC-NN. Chairman name/number are admin-only and unverified.
     *
     * Not loaded: Saddar, Jamshed and Jinnah (the file repeats Orangi's list
     * under all three), Bin Qasim (repeats Ibrahim Hyderi's), Gadap (no list),
     * and the reserved-seat repeats in Ibrahim Hyderi.
     */
    private function seedUnionCouncils(): void
    {
        $unionCouncils = [
            'Liaquatabad' => [
                ['UC-01', 'Moosa Colony', 'Ubaid Saharanpuri', '03442266458'],
                ['UC-02', 'Sharifabad', 'Hafiz Irshad', '03118476731'],
                ['UC-03', 'Bandhani Colony', 'Younus Bandhani', '03212553041'],
                ['UC-04', 'Ibn e Sina', 'Ubaid Ahmed Khan', '03343739408'],
                ['UC-05', 'Commercial Area', 'Qutubuddin', '03343638772'],
                ['UC-06', 'B-1 Area', 'Syed Moin Abbas Madani', '03332288032'],
                ['UC-07', 'C-Area', 'Azhar Shamsi', '03222527506'],
            ],
            'Baldia' => [
                ['UC-01', 'Methan', 'Asif Raza (Chairman)', '0348-2108006'],
                ['UC-02', 'Ittehad Town', 'Muhammad Arif (Chairman)', '0333-2298299'],
                ['UC-03', 'Shaheed Nawab Khan', 'Jamroz Khan (Chairman)', '0300-2710046'],
                ['UC-04', 'Gulshan-e-Ghazi', 'Murad Khan (Chairman)', '0315-8285077'],
                ['UC-05', 'Islam Nagar', 'Dildar Ahmed (Chairman)', '0345-6227787'],
                ['UC-06', 'Jam Nagar', 'Muhammad Ahad Iqbal (Chairman)', '0333-2272903'],
                ['UC-07', 'Madina Colony', 'Ellahi Bux (Chairman)', '0300-3642984'],
                ['UC-08', 'Saeedabad', 'Muhammad Farooq (Chairman)', '0312-9227225'],
                ['UC-09', 'Nai Abadi Ranger Muhalla', 'Zeeshan Zaheer (Chairman)', '0347-2056270'],
                ['UC-10', 'Shaheed Eidi Ameen', 'Muhammad Akram (Chairman)', null],
                ['UC-11', 'New Saeedabad', 'Khan Muhammad', '0341-2557447'],
                ['UC-12', 'Naval Colony', 'Dildar (Chairman)', '0300-2617403'],
                ['UC-13', 'Yousuf Goth', null, null],
            ],
            'Gulshan-e-Iqbal' => [
                ['UC-01', 'Essa Nagri', 'Sanam Gabol (Chairman)', '0312-2441923'],
                ['UC-02', 'Hassan Square', 'Riaz Azhar (Chairman)', '0302-8274361'],
                ['UC-03', 'Zia Ul Haq Colony', 'Majid Ali (Chairman)', '0300-3332203'],
                ['UC-04', 'Disco Bakery', 'Fayyaz Ul Huda (Chairman)', '0311-1647429'],
                ['UC-05', 'Quaid-E-Azam Colony', 'Muzafar Iqbal (Chairman)', '0334-3653545'],
                ['UC-06', 'Metroville', 'Nasir Ashfaq (Chairman)', '0300-8258780'],
                ['UC-07', 'Shanti Nagar', 'Sikandar Baloch (Chairman)', '0300-2264407'],
                ['UC-08', 'National Stadium', 'Khizar Baqi (Chairman)', '0333-2223962'],
            ],
            'Gulberg' => [
                ['UC-01', 'Shafiq Mill', 'Asim Makhdomi (Chairman)', '0344-5532288'],
                ['UC-02', 'Samanabad', 'Faisal Sheikh (Chairman)', '0311-2977133'],
                ['UC-03', 'Waterpump', 'Noman ul haq(Vice Chairman)', '0333-2280585'],
                ['UC-04', 'Naseerabad', 'Owais Baig(Vice Chairman)', '0331-2107024'],
                ['UC-05', 'Yaseenabad', 'Arif Munir(Chairman)', '0300-9276565'],
                ['UC-06', 'Azizabad', 'Muhammad Ilyas (Vice Chairman)', '0344-2606660'],
                ['UC-07', 'Hussainabad', 'Ilyas Memon (Vice Chairman)', '0345-1992508'],
                ['UC-08', 'Ayesha Manzil', 'Shallal Ahmed ( Vice Chairman)', '0312-215 0345'],
            ],
            'Korangi' => [
                ['UC-01', 'Qayyumabad', 'Naveed Rehman / Zareen Awan (Town Vice Chairman)', '0300-2627443 / 0300-9208450'],
                ['UC-02', 'Makhdoom Bilawal', 'Muhammad Israr / Muhammad Naeem Shaikh, ( Town Chairman)', '0306-2089168'],
                ['UC-03', 'Nasir Colony', 'Qaisar Manzoor / Saied Feroz', '0321-2042102'],
                ['UC-04', 'Zia Colony', 'Salman Rind / Asghar Ali', '0313-3405041'],
                ['UC-05', 'Sector 33', 'Amjad / Waseem Haris', '0302-1952354'],
                ['UC-06', 'Korangi', 'Akhtar Hussain / Muhammad Haroon', '0346-2930690'],
                ['UC-07', 'Rahim Abad', 'Khursheed / Muhammad Saleem', '0322-2258565'],
                ['UC-08', 'Madina Colony', 'Ahmed Raza / Umer Farooq', '0321-355145'],
                ['UC-09', 'Ittihad Colony', 'Muhammad Ashraf / Riaz', '0311-3717359 / 0310-1175656'],
                ['UC-10', 'Ghousia Colony', 'Habibullah / Adnan Mughal', '0313-2748777'],
                ['UC-11', 'Chakra Goth', 'Murtuza Ali Memon / Ghafoor Alam Saifi', '0312-2689612'],
            ],
            'North Nazimabad' => [
                ['UC-01', 'Sir Syed', 'Arshad Hassan', '0333 2188952'],
                ['UC-02', 'Farooq-e-Azam', 'Farhan Sohail', '03332254434'],
                ['UC-03', 'Siddiq-e-Akber', 'Maaz Hanfi', '03212320015'],
                ['UC-04', 'Buffer Zone', 'Zeeshan', '03002353278'],
                ['UC-05', 'Taimuria', 'Onaib', '0333-2197718'],
                ['UC-06', 'Sakhi Hassan', 'Mujahid', '03008243806'],
                ['UC-07', 'Hydairy', 'Khalid', '03340710102'],
                ['UC-08', 'Al-Falah', 'Zulfiqar', '03212114939'],
                ['UC-09', 'Pharganj', 'Majid', '03028226118'],
                ['UC-10', 'Mustafabad', 'Rehan', '03181923997'],
            ],
            'Nazimabad' => [
                ['UC-01', 'Paposh Nagar Near Allama Iqbal School', 'Aftab Hameed (Vice Chairman)', '0302-8259212'],
                ['UC-02', 'Abbasi Hospital Near Noor ul Islam Masjid', 'Ali Hassan Shaikh (Vice Chairman)', '0336-8221398'],
                ['UC-03', 'Hadi Market Nazimabad', 'Muhammad Saleem (Vice Chairman)', '0322-6164943'],
                ['UC-04', 'Nazimabad No.1 Near Agha Juice Center', 'Syed Muhammad Muzafar (Vice Chairman/Town Chairman)', '0346-3149195'],
                ['UC-05', 'Rizvia Society', 'Abdul Latif (Vice Chairman)', '0311-3000295'],
                ['UC-06', 'Firdous Colony Near Lal Masjid', 'Abdul Khaliq (Vice Chairman)', '0333-3396207'],
                ['UC-07', 'Rizvia Imam Bargah Gulbahar No 2', 'Furqan Islam (Vice Chairman)', '0335-3466485'],
            ],
            'New Karachi' => [
                ['UC-01', 'Shahnawaz Bhutto Colony', '(Chairman) Muhammad Naveed (Vice Chairman) Sohail Mubarak', '0300-8995331, 0300-7001714'],
                ['UC-02', 'Gulshan-e-Saeed', '(Chairman) Muhammad Ahmer Khan (Vice Chairman) Faizan Qureshi', '0312-2762575, 0311-6625907'],
                ['UC-03', 'Khawaja Ajmeer Nagri', '(Chairman) Muhammad Ali Arshad (Vice Chairman) Shoaib Bin Zaheer', '0332-3643343, 0317-2787071'],
                ['UC-04', 'Mustafa Colony', '(Chairman) Muhammad Amir (Vice Chairman) Mir Balaj', '0315-2591014, 0314-2177815'],
                ['UC-05', 'Kala School', '(Chairman) Atta Ur Rehman (Vice Chairman) Tauqeer Khan', '0335-7193357, 0313-2466267'],
                ['UC-06', 'Khamiso Goth', '(Chairman) Rehmat Ali (Vice Chairman) Afzal Brohi', '0313-2039783, 0314-2210548'],
                ['UC-07', 'Madina Colony', '(Chairman) Abdul Ghaffar Chishti (Vice Chairman) Adnan Razi', '0332-2134028, 0313-3983075'],
                ['UC-08', 'Shah Faisal', '(Chairman) Khalid Mehmood (Vice Chariman) Imran Faqeer', '0319-2134028, 0334-2221148'],
                ['UC-09', 'Abu Zar Ghaffari', '(Chairman) Muhammad Abbas Shaikh (Vice Chairman) Hanzalah Anwar', '0300-2757491, 0313-3549380'],
                ['UC-10', 'Godhra', '(Chairman) Faisal Ahmed (Vice Chairman) Owais Essa', '0310-9218352, 0345-2732714'],
                ['UC-11', 'Hakeem Ahsan', '(Chairman) Muhammad Ghazanfar Ali (Vice Chairman) Imran Shafiq', '0324-2874737, 0332-3129570'],
                ['UC-12', 'Kalyana', '(Chairman) Azeem Anwar (Vice Chairman) Shahzad Ahmed', '0341-1284040, 0321-2727989'],
                ['UC-13', 'Muhammad Shah', '(Chairman) Shahzaib Satti (Vice Chairman) Jawad Hasan', '0314-4116111, 0300-7037676'],
            ],
            'Manghopir' => [
                ['UC-01', 'Mai Garhi', 'SALEEM BROHI / HAJI NAWAZ ALI BROHI', '0316-2689769'],
                ['UC-02', 'Manghoopir', 'YOUNUS MENGAL / ALI AKBER KACHELO', '0317-2181605'],
                ['UC-03', 'Pakhtoonabad', 'MUFTI KHALID / FAZAL MOLA', '0305-1312012'],
                ['UC-04', 'Surjani Town', 'ATIF HAYAT / JIBRAN', '0313-3942737'],
                ['UC-05', 'Yousuf Goth', 'ASHRAF / MUSHTAQ', '0312-2922365'],
                ['UC-06', 'Raheem Goth', 'ZUBAIDA IQBAL / NOOR MOHD', '0307-2276807'],
                ['UC-07', 'K.D.A Flats', 'UMEED ALI QAZI / HAFIZ IMRAN', '0313-9251311'],
                ['UC-08', 'Bhatti Goth', 'QADIR BUX BROHI / RANA M ARIF', '0315-2844092'],
                ['UC-09', 'Khuda Ki Basti', 'IRSHAD MUGHAL / ALI HASSAN BROHI', '0313-3247986'],
                ['UC-10', 'Liyari Expressway Resettlement Project', 'MUSTAFA BADAR / MALIK AMIR', '0311-6351306'],
                ['UC-11', 'Hassan Goth', 'MOR KHAN / QARI ASHRAF', '0333-9234816'],
                ['UC-12', 'Gulshan-e-Mayman', 'SYED HILAL REHMANI / SAQIB', '0332-2202181'],
                ['UC-13', 'Mullah Hussain Brohi', 'ANWER BROHI / ALI AKBAR BROHI', '0314-2988310'],
                ['UC-14', 'Kunwari Colony', 'MUNEEB / HABIBULLAH SAWATI', '0300-2063034'],
                ['UC-15', 'M.P.R Colony', 'DR AZIZ / NISAR', '0347-2564942'],
                ['UC-16', 'Gabool Colony', 'HAJI JUMMAN DARBAN/HASSAN BALOCH', '0302-2462076'],
            ],
            'Malir Town' => [
                ['UC-01', 'Gharibabad', 'Muhammad Riaz Baloch', '03012251750'],
                ['UC-02', 'Dawood Goth', 'Jan Muhammad Baloch (Chairman)', '03002245377'],
                ['UC-03', 'Jaffar e tayar', 'Syed Raza Husssain Rizvi', '03462757113'],
                ['UC-04', 'Khuldabad', 'Syed Muzammil Shah (vice Chairman)', '03132120719'],
                ['UC-05', 'Qaidabad', 'Saeed ullah', '03002974601'],
                ['UC-06', 'Dawood Chorangi', 'Abdullah khan Raja Khailq', '03112492701'],
                ['UC-07', 'Future Colony', 'Iqbal Din', '03332211928'],
                ['UC-08', 'Sharafi Goth', 'Nizar Muhammad Baloch', '03369259212'],
                ['UC-09', 'Bakhtawar Goth', 'Muhammad Ibrahim Soomro', '03122784884'],
                ['UC-10', 'Bhittaiabad', 'Allah Dino Soomro', '03008218959'],
            ],
            'Ibrahim Hyderi' => [
                ['UC-01', 'Chowkandi', 'Mr. Manzoor Ahmed Arfani (Elected Member PPPP)', '0300-8994688'],
                ['UC-02', 'Shah Lateef', 'Mr. Muhammad Ameen Jutt (Elected Member-Jamat-e-Islami)', '0301-2236812'],
                ['UC-03', 'Cattle Colony', 'Mr. Abdul Sattar Himayatti (Elected Member PPPP)', '0333-2307719'],
                ['UC-04', 'Majeed Colony', 'Mr. Saif Kamil (Elected Member of PPPP)', '0344-4550881'],
                ['UC-05', 'Muzzaffarabad', 'Mr. Muhammad Yousuf (Elected Member of PTI)', '0304-1262009'],
                ['UC-06', 'Muslimabad', 'Dr. Muhammad Fazal (Elected Member of PTI )', '0348-2283800'],
                ['UC-07', 'Sher Pao colony', 'Mr. Naeem Abbasi (Elected Member of PPPP )', '0332-3636361'],
                ['UC-08', 'Ibrahim Hyderi', 'Mr. Jan Alam Jamot (Elected Member of PPPP)', '0342-2074212'],
                ['UC-09', 'Chashma', 'Mr. Nazeer Ahmed Bhutto (Elected Town Chairman PPPP)', '0300-9235141'],
                ['UC-10', 'Rehri', 'Mr. Muhammad Rafiq Dawood Jat (Elected Vice-Chairman PPPP)', '0321-7052799'],
                ['UC-11', 'Ali Akber Shah', 'Mr. Muhammad Azeem Baloch (Elected Member of PPPP)', '0312-2831926'],
            ],
            'Landhi' => [
                ['UC-01', 'Labor Squre', 'Noor Hussain (Chairman)', '0333-2127428'],
                ['UC-02', 'Zaman Town', 'Muhammad Qasim Khan (Chairman)', '0304-2141034'],
                ['UC-03', 'Shareef Colony', 'Nadir Ali Khan Lodhi (Chairman)', '0333-2214923'],
                ['UC-04', 'Kawaja Ajmeer', 'Muhammad Idrees ( Chairman)', '0300-2557619'],
                ['UC-05', 'Bhutto Nagar', 'Sarfraz Ahmed (Chairman)', '0315-2658986'],
                ['UC-06', 'Farooq Villas', 'Israr Ahmed Siddiqui (Chairman)', '0312-2125185'],
                ['UC-07', 'Zaman Abad', 'Mirza Farhan Baig (Chairman)', '0333-2145770'],
                ['UC-08', 'Musarat Mohani Colony', 'Muhammad Ayub Abbasi (Chairman)', '0311-3311539'],
                ['UC-09', 'Nizam -e- Mustafa Colony', 'Hafiz Anwar Elahi (Chairman)', '0316-0018107'],
                ['UC-10', '100 Quarter', 'Abdul Hafeez (Chairman)', '0346-8225488'],
            ],
            'Shah Faisal' => [
                ['UC-01', 'Natha Khan', 'ATEEQ (CHAIRMAN)', '0312-8511688'],
                ['UC-02', 'Al-Falah', 'SAIF UDDIN (CHAIRMAN)', '0333-3008176'],
                ['UC-03', 'Green Town', 'CH. ASAD (CHAIRMAN)', '0333-2236738'],
                ['UC-04', 'Reta Plot', 'ADNAN (CHAIRMAN)', '0315-5924753'],
                ['UC-05', 'Gulzar Colony', 'SALMAN (CHAIRMAN)', '0324-3056150'],
                ['UC-06', 'Awami Colony', 'YAQOOB KALROO (CHAIRMAN)', '0300-3398596'],
                ['UC-07', 'Bilal Colony', 'ABDUL REHMAN (CHAIRMAN)', '0301-2568872'],
                ['UC-08', 'Mehran Town', 'ZEESHAN ZAIB (CHAIRMAN)', '0301-8103144'],
            ],
            'Model Colony' => [
                ['UC-01', 'Khazmabad', 'RAFIQ UDDIN (Chairman)', '0333-3155547'],
                ['UC-02', 'Indus Mehran', 'WASEEM MIRZA (Chairman)', '03332129952'],
                ['UC-03', 'Khokrapar', 'ABDUL MOEEZ (Chairman)', '0331-2598472'],
                ['UC-04', 'Liaquat Market', 'KAREEM BUKSH (Chairman)', '0332-3640671'],
                ['UC-05', 'Kala Board', 'TALHA KHAN (Chairman)', '0321-2248788'],
                ['UC-06', 'Haji Raheem Khan Jokio', 'SAAD YOUSUF ZAI (Chairman)', '0333-7154887'],
                ['UC-07', 'Rafee Banglow', 'FARHAN KHAN (Chairman)', '0333-2396425'],
                ['UC-08', 'Jamiya Milya', 'AHSAN ZAMEER (Chairman)', '0300-8213042'],
            ],
            'Orangi Town' => [
                ['UC-01', 'Fareed Colony', 'Muhammad Kamran (Vice Chairman)', '0300-2544710'],
                ['UC-02', 'Haryana Colony', 'Abdullah Baloach (Town Vice Chairman)', '0312-215 0345'],
                ['UC-03', 'Bismillah Colony', 'Asif Rehman (Vice Chairman)', '0345-1992508'],
                ['UC-04', 'Islam Nagar', 'Abid Shah (Vice Chairman)', '0344-2606660'],
                ['UC-05', 'Mominabad', 'Malik Arif Awan (Town Chairman)', '0300-9276565'],
                ['UC-06', 'Frontier Colony', 'Mukhtiar Shah (Vice Chairman)', '0331-2107024'],
                ['UC-07', 'Banaras Colony', 'Dr. Kabeer (Vice Chairman)', '0333-2280585'],
                ['UC-08', 'Peerabad', 'Nadir ur Rehman (Vice Chairman)', '0311-2977133'],
                ['UC-09', 'Qasba Colony', 'Syed Baseer Uddin (Vice Chairman)', '0344-5532288'],
            ],
            'Mominabad' => [
                ['UC-01', 'Fareed Colony', 'Muhammad Kamran (Vice Chairman)', '0300-2544710'],
                ['UC-02', 'Haryana Colony', 'Abdullah Baloach (Town Vice Chairman)', '0312-215 0345'],
                ['UC-03', 'Bismillah Colony', 'Asif Rehman (Vice Chairman)', '0345-1992508'],
                ['UC-04', 'Islam Nagar', 'Abid Shah (Vice Chairman)', '0344-2606660'],
                ['UC-05', 'Mominabad', 'Malik Arif Awan (Town Chairman)', '0300-9276565'],
                ['UC-06', 'Frontier Colony', 'Mukhtiar Shah (Vice Chairman)', '0331-2107024'],
                ['UC-07', 'Banaras Colony', 'Dr. Kabeer (Vice Chairman)', '0333-2280585'],
                ['UC-08', 'Peerabad', 'Nadir ur Rehman (Vice Chairman)', '0311-2977133'],
            ],
        ];

        foreach ($unionCouncils as $town => $rows) {
            foreach ($rows as [$code, $name, $contactName, $contactPhone]) {
                $this->node("{$code} {$name}", [
                    'kind' => 'landmark',
                    'uc_code' => $code,
                    'display_name' => $name,
                    'parent_id' => $this->nodeIds[$town],
                    'contact_name' => $contactName,
                    'contact_phone' => $contactPhone,
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function node(string $name, array $attributes): void
    {
        $node = GazetteerNode::query()->create([
            'name' => $attributes['display_name'] ?? $name,
            'aliases' => $attributes['aliases'] ?? [],
            'kind' => $attributes['kind'],
            'uc_code' => $attributes['uc_code'] ?? null,
            'parent_id' => $attributes['parent_id'] ?? null,
            'district' => $attributes['district'] ?? null,
            'tmc_authority_id' => $attributes['tmc_authority_id'] ?? null,
            'special_zone_authority_id' => $attributes['special_zone_authority_id'] ?? null,
            'needs_human_review' => $attributes['needs_human_review'] ?? false,
            'contact_name' => $attributes['contact_name'] ?? null,
            'contact_phone' => $attributes['contact_phone'] ?? null,
        ]);

        $this->nodeIds[$name] = $node->id;
    }
}
