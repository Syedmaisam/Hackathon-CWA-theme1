<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasProviderOptions;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::DeepSeek)]
#[Model('deepseek-flash')]
#[Timeout(25)]
#[MaxTokens(800)]
class ClassifyReport implements Agent, HasProviderOptions, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'TEXT'
        You classify raw civic complaint reports from residents of Karachi, Pakistan. You do
        not decide which government authority handles the report — a separate, deterministic
        routing system does that from the enums you emit. Never name an authority, phone
        number, email, or department in any field.

        Reports arrive in English, Urdu script, or Roman Urdu, and sometimes mix all three.
        Common Roman Urdu terms you must recognise: "malba" or "gutter/gatar" (debris/sewer),
        "kachra" (garbage), "pani" (water), "bijli ka taar" (electric wire), "qabza"
        (encroachment), "gali" (street), "sadak" (road), "batti" (light).

        issue_type meanings, each with two examples:
        - garbage: uncollected trash, overflowing bin. "kachra 3 din se nahi utha", "bin overflowing outside our gate"
        - construction_debris: malba/building material dumped on a road or footpath. "builder ne malba daal diya sadak par", "bricks and rubble blocking the footpath"
        - sewer_overflow: sewage, open manhole, choked sewer line (not plain rainwater). "gutter ubal raha hai", "manhole cover missing, sewage on the road"
        - water_supply: burst main, leak, no water, low pressure, dirty water. "3 din se pani nahi aa raha", "pipeline burst, water everywhere"
        - tanker: official water tanker booking or tariff questions. "tanker book karna hai", "KWSC tanker rate kya hai"
        - road_damage: pothole or damaged street/footpath surface. "gehra gadha hai gali mein", "footpath toot gaya hai"
        - streetlight: a street light pole is dark or broken. "streetlight kharab hai", "gali mein andhera hai, batti nahi hai"
        - electrical_hazard: sparking wire, exposed cable, leaning pole, transformer fault. "taar latak raha hai", "transformer mein spark ho raha hai"
        - encroachment: shops/stalls/parking illegally occupying a road or footpath. "footpath par thela laga hua hai", "shopkeepers took over the whole street"
        - illegal_construction: unauthorised floors, plaza parking converted to shops/godowns. "bina naqsha ke building ban rahi hai", "parking area converted into a warehouse"
        - storm_drain: blocked storm-water drain/nullah causing flooding, distinct from a sewer pipe. "nullah band hai, gali doob gayi", "storm drain choked with plastic"
        - park_amenity: a public park, playground, or civic amenity in disrepair.
        - unknown: cannot confidently place it in any of the above.

        road_scope judges the ROAD ITSELF, not who owns it — arterial (a major/named
        through-road or flyover), internal_street (a small residential street or lane),
        not_applicable (issue isn't about a road), or unknown.

        severity: low, medium, high, or emergency. Use emergency only for immediate danger to
        life (exposed live wire, structural collapse, active flooding into homes).

        hazards: any of child_risk, traffic_blocked, exposed_wire, flooding, health_risk,
        fire_risk, structural_collapse that plainly apply. Return an empty array if none do.

        location: extract landmark, road, block_or_sector, area (neighbourhood/town as the
        citizen said it, with spelling normalised to English), and cantonment_or_estate (only
        if the citizen explicitly said a cantonment, "DHA", "Defence", or a named estate).
        Leave any field null rather than guessing. For orientation only, common Karachi
        top-level places include: the seven districts (South, East, Central, West, Korangi,
        Malir, Keamari); the six cantonment boards (Clifton, Karachi, Faisal, Malir, Korangi
        Creek, Manora); "DHA Phase 1" through "DHA Phase 8"; "Clifton Block 1" through
        "Clifton Block 9"; and well-known towns such as Nazimabad, North Nazimabad, Gulshan-e-
        Iqbal, Orangi, Lyari, Korangi, Malir, and Landhi. Use this list only to normalise
        spelling — never to decide an authority.

        Ask exactly ONE clarifying_question, and only when the issue type or the location is
        genuinely too vague to act on (e.g. no location at all). Otherwise leave it null.

        Respond only with the requested structured fields.
        TEXT;
    }

    public function schema(JsonSchema $schema): array
    {
        $issueTypes = [
            'garbage', 'construction_debris', 'sewer_overflow', 'water_supply', 'tanker',
            'road_damage', 'streetlight', 'electrical_hazard', 'encroachment',
            'illegal_construction', 'storm_drain', 'park_amenity', 'unknown',
        ];

        return [
            'issue_type' => $schema->string()->enum($issueTypes)->required(),
            'issue_confidence' => $schema->string()->enum(['high', 'medium', 'low'])->required(),
            'road_scope' => $schema->string()->enum(['arterial', 'internal_street', 'not_applicable', 'unknown'])->required(),
            'severity' => $schema->string()->enum(['low', 'medium', 'high', 'emergency'])->required(),
            'hazards' => $schema->array()->items(
                $schema->string()->enum([
                    'child_risk', 'traffic_blocked', 'exposed_wire', 'flooding', 'health_risk', 'fire_risk', 'structural_collapse',
                ])
            )->required(),
            'input_language' => $schema->string()->enum(['en', 'ur', 'roman_urdu', 'mixed'])->required(),
            'location' => $schema->object(fn ($schema) => [
                'landmark' => $schema->string()->nullable(),
                'road' => $schema->string()->nullable(),
                'block_or_sector' => $schema->string()->nullable(),
                'area' => $schema->string()->nullable(),
                'cantonment_or_estate' => $schema->string()->nullable(),
            ])->required(),
            'observed_when' => $schema->string()->nullable(),
            'photo_matches_text' => $schema->boolean()->nullable(),
            'summary_en' => $schema->string()->required(),
            'clarifying_question' => $schema->string()->nullable(),
        ];
    }

    public function providerOptions(Lab|string $provider): array
    {
        return match ($provider) {
            Lab::DeepSeek => ['thinking' => ['type' => 'disabled']],
            default => [],
        };
    }
}
