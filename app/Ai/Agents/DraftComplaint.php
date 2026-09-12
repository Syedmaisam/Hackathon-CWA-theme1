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
#[Timeout(40)]
#[MaxTokens(1500)]
class DraftComplaint implements Agent, HasProviderOptions, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'TEXT'
        You write formal civic complaints for residents of Karachi, Pakistan, in the register
        used for official correspondence to a government department. You are given the
        classified issue, the deterministic routing result (recipient names, their role, and
        the rule that selected them), the citizen's raw report, and whether the citizen
        consented to share their phone number. Address the complaint to the recipients given —
        do not add, invent, or guess any phone number, email, address, or URL; the application
        renders the contact block separately from your text.

        Every complaint you write must include: the specific issue classification; the full
        available location (landmark, road, block/sector, area) and the road name where
        possible; the start date/time or "unknown" if not given; the harm or urgency (health
        risk, traffic obstruction, flooding, child safety, exposed electrical hazard, etc., as
        applicable); the requested remedy; a list of attachments if a photo was provided; and a
        closing request for a complaint/reference number and the expected resolution time.
        Include the citizen's phone number only if consent was given.

        Produce both an English version and an Urdu version. Write the Urdu in proper Nastaliq-
        script Urdu prose, not Roman Urdu transliteration.
        TEXT;
    }

    public function schema(JsonSchema $schema): array
    {
        $remedies = [
            'collect_waste', 'clear_sewer', 'cover_manhole', 'repair_pipe', 'restore_supply',
            'repair_road', 'repair_streetlight', 'remove_encroachment', 'remove_debris',
            'stop_construction', 'clear_drain', 'make_safe_electrical', 'other',
        ];

        return [
            'subject_en' => $schema->string()->required(),
            'body_en' => $schema->string()->required(),
            'subject_ur' => $schema->string()->required(),
            'body_ur' => $schema->string()->required(),
            'requested_remedy' => $schema->string()->enum($remedies)->required(),
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
