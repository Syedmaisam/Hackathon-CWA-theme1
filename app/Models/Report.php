<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'citizen_name', 'citizen_phone', 'input_mode', 'input_language', 'raw_text',
    'photo_path', 'observed_at', 'classification', 'issue_type', 'severity',
    'hazards', 'location_text', 'gazetteer_node_id', 'resolved_area',
    'resolved_special_zone', 'routing', 'routing_confidence', 'routing_flags',
    'clarifying_question', 'clarifying_answer', 'draft_en', 'draft_ur',
    'requested_remedy', 'status', 'ai_failed_at',
])]
class Report extends Model
{
    protected function casts(): array
    {
        return [
            'observed_at' => 'datetime',
            'classification' => 'array',
            'hazards' => 'array',
            'routing' => 'array',
            'routing_flags' => 'array',
            'ai_failed_at' => 'datetime',
        ];
    }

    public function gazetteerNode(): BelongsTo
    {
        return $this->belongsTo(GazetteerNode::class);
    }

    /**
     * Stage 5 — match the classifier's free-text location against the gazetteer.
     * Longest, most specific name/alias wins; special zones are preferred over
     * districts/towns when a candidate matches more than one kind equally well.
     */
    public function resolveLocation(): void
    {
        $location = $this->classification['location'] ?? [];

        $candidates = array_values(array_filter([
            $location['cantonment_or_estate'] ?? null,
            $location['area'] ?? null,
            $location['landmark'] ?? null,
            $location['road'] ?? null,
            $location['block_or_sector'] ?? null,
        ]));

        $haystack = Str::lower(implode(' | ', [...$candidates, $this->raw_text]));

        $nodes = GazetteerNode::query()->get(['id', 'name', 'aliases', 'kind', 'parent_id']);

        $kindPriority = ['special_zone' => 0, 'landmark' => 1, 'town' => 2, 'district' => 3];

        $matches = $nodes->filter(function (GazetteerNode $node) use ($haystack) {
            $names = [$node->name, ...($node->aliases ?? [])];

            foreach ($names as $name) {
                if ($name !== '' && str_contains($haystack, Str::lower($name))) {
                    return true;
                }
            }

            return false;
        })->sortBy(function (GazetteerNode $node) use ($kindPriority) {
            return [$kindPriority[$node->kind] ?? 9, -Str::length($node->name)];
        });

        $node = $matches->first();

        if (! $node) {
            return;
        }

        $this->gazetteer_node_id = $node->id;

        $town = $node;
        while ($town && ! in_array($town->kind, ['town', 'special_zone'], true)) {
            $town = $town->parent;
        }

        $this->resolved_area = $town?->name ?? $node->name;
    }

    /**
     * Stage 6 — special-zone overrides beat district hierarchy. Runs before
     * the routing table lookup. Walks the matched node's ancestor chain.
     */
    public function applySpecialZoneOverride(): ?array
    {
        $node = $this->gazetteer_node_id ? GazetteerNode::find($this->gazetteer_node_id) : null;

        if (! $node) {
            return null;
        }

        $walker = $node;
        while ($walker) {
            if ($walker->needs_human_review) {
                $this->resolved_special_zone = null;
                $this->routing_confidence = 'needs_human_review';

                return ['needs_human_review' => true];
            }

            if ($walker->special_zone_authority_id) {
                $this->resolved_special_zone = $walker->special_zone_authority_id;

                return [
                    'primary_authority_id' => $walker->special_zone_authority_id,
                    'flag' => 'special_zone_override',
                ];
            }

            $walker = $walker->parent;
        }

        return null;
    }

    /**
     * Stage 7 — apply the routing_rules table for the classified issue_type,
     * honouring the special-zone override from stage 6 when present.
     *
     * @return array<int, array{authority_id: string, role: string, reason: string, rule: string}>
     */
    public function applyRoutingRule(?array $override): array
    {
        $flags = [];
        $recipients = [];

        if ($override && ($override['needs_human_review'] ?? false)) {
            $recipients[] = [
                'authority_id' => 'pmdu',
                'role' => 'primary',
                'reason' => 'Location needs human review before routing (e.g. DHA City is a separate scheme).',
                'rule' => 'needs_human_review',
            ];
            $this->routing_flags = ['needs_human_review'];

            return $recipients;
        }

        if ($override) {
            $recipients[] = [
                'authority_id' => $override['primary_authority_id'],
                'role' => 'primary',
                'reason' => 'Location resolves inside a special zone, which overrides district routing.',
                'rule' => $override['flag'],
            ];
            $flags[] = $override['flag'];

            $this->routing_flags = $flags;

            return $recipients;
        }

        $rule = RoutingRule::find($this->issue_type);

        if (! $rule) {
            $recipients[] = [
                'authority_id' => 'pmdu',
                'role' => 'primary',
                'reason' => 'Issue type could not be classified with confidence.',
                'rule' => 'unknown',
            ];
            $this->routing_flags = ['unrouted_issue_type'];

            return $recipients;
        }

        $roadScope = $this->classification['road_scope'] ?? null;
        $town = $this->gazetteer_node_id ? GazetteerNode::find($this->gazetteer_node_id) : null;
        while ($town && $town->kind !== 'town') {
            $town = $town?->parent;
        }

        $primaryAuthorityId = $rule->primary_authority_id;
        $coAuthorityIds = $rule->co_authority_ids ?? [];

        if ($rule->internal_street_goes_to_tmc && $roadScope === 'internal_street' && $town?->tmc_authority_id) {
            $primaryAuthorityId = $town->tmc_authority_id;
            $coAuthorityIds = [$rule->primary_authority_id, ...$coAuthorityIds];
            $flags[] = 'road_ownership_uncertain';
        }

        $recipients[] = [
            'authority_id' => $primaryAuthorityId,
            'role' => 'primary',
            'reason' => $rule->rule_note,
            'rule' => $this->issue_type,
        ];

        foreach ($coAuthorityIds as $coAuthorityId) {
            if ($coAuthorityId === $primaryAuthorityId) {
                continue;
            }

            $recipients[] = [
                'authority_id' => $coAuthorityId,
                'role' => 'co_recipient',
                'reason' => $rule->rule_note,
                'rule' => $this->issue_type,
            ];
        }

        $flags = [...$flags, ...($rule->flags ?? [])];

        $primary = Authority::find($primaryAuthorityId);
        $alreadyRecipient = collect($recipients)->pluck('authority_id')->contains('kmc');

        if ($primary && ! $primary->citizen_visible && ! $alreadyRecipient) {
            $recipients[] = [
                'authority_id' => 'kmc',
                'role' => 'escalation',
                'reason' => 'Primary authority has no verified citizen-facing contact; KMC 1339 also logs and forwards these.',
                'rule' => 'contact_unverified_fallback',
            ];
            $flags[] = 'contact_unverified';
        }

        $this->routing_flags = array_values(array_unique($flags));

        return $recipients;
    }

    /**
     * Stage 8 — score routing confidence and decide whether the citizen
     * needs to answer a clarifying question before a draft is produced.
     */
    public function scoreConfidence(?array $override): string
    {
        $issueConfidence = $this->classification['issue_confidence'] ?? 'low';

        if ($this->routing_confidence === 'needs_human_review') {
            return 'needs_human_review';
        }

        if ($override) {
            return 'high';
        }

        if (! $this->gazetteer_node_id || $issueConfidence === 'low' || $this->issue_type === 'unknown') {
            return 'low';
        }

        $flags = $this->routing_flags ?? [];

        if (in_array('road_ownership_uncertain', $flags, true) || in_array('contact_unverified', $flags, true)) {
            return 'medium';
        }

        return $issueConfidence === 'high' ? 'high' : 'medium';
    }

    /**
     * Deterministic PHP string template used when the AI draft call fails —
     * guarantees a routed, sendable complaint even with the network down.
     */
    public function templateDraft(string $locale): string
    {
        $recipients = collect($this->routing ?? [])
            ->pluck('authority_id')
            ->map(fn ($id) => Authority::find($id)?->name ?? $id)
            ->implode(', ');

        $observed = $this->classification['observed_when']
            ?? $this->observed_at?->toDateString()
            ?? 'unknown';

        if ($locale === 'ur') {
            return implode("\n\n", [
                'موصول کنندہ: '.$recipients,
                'مسئلہ: '.($this->issue_type ?? 'نامعلوم'),
                'مقام: '.($this->location_text ?? 'نامعلوم'),
                'تاریخ: '.$observed,
                'تفصیل: '.$this->raw_text,
                'براہ کرم شکایت نمبر اور متوقع حل کا وقت فراہم کریں۔',
            ]);
        }

        return implode("\n\n", [
            'To: '.$recipients,
            'Issue: '.($this->issue_type ?? 'unclassified'),
            'Location: '.($this->location_text ?? 'not provided'),
            'Observed since: '.$observed,
            'Details: '.$this->raw_text,
            'Please provide a complaint reference number and expected resolution time.',
        ]);
    }
}
