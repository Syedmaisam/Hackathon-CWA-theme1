<?php

namespace App\Http\Controllers\Maisam;

use App\Ai\Agents\ClassifyReport;
use App\Ai\Agents\DraftComplaint;
use App\Http\Controllers\Controller;
use App\Models\Authority;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Ai\Files;
use Throwable;

class ReportController extends Controller
{
    private const ISSUE_TYPES = [
        'garbage', 'construction_debris', 'sewer_overflow', 'water_supply', 'tanker',
        'road_damage', 'streetlight', 'electrical_hazard', 'encroachment',
        'illegal_construction', 'storm_drain', 'park_amenity', 'unknown',
    ];

    public function create(): Response
    {
        return Inertia::render('Maisam/Report/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'raw_text' => ['required', 'string', 'max:4000'],
            'citizen_name' => ['nullable', 'string', 'max:255'],
            'citizen_phone' => ['nullable', 'string', 'max:50'],
            'input_mode' => ['nullable', 'string', 'in:text,voice,photo'],
            'photo' => ['nullable', 'image', 'max:8192'],
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('reports', 'public')
            : null;

        $report = Report::query()->create([
            'citizen_name' => $validated['citizen_name'] ?? null,
            'citizen_phone' => $validated['citizen_phone'] ?? null,
            'input_mode' => $validated['input_mode'] ?? ($photoPath ? 'photo' : 'text'),
            'raw_text' => $validated['raw_text'],
            'photo_path' => $photoPath,
            'status' => 'pending',
        ]);

        $this->runPipeline($report, $request->file('photo'));

        return redirect()->route('reports.show', $report);
    }

    public function show(Report $report): Response
    {
        return Inertia::render('Maisam/Report/Show', [
            'report' => [
                'id' => $report->id,
                'raw_text' => $report->raw_text,
                'input_mode' => $report->input_mode,
                'input_language' => $report->input_language,
                'photo_url' => $report->photo_path ? '/storage/'.$report->photo_path : null,
                'issue_type' => $report->issue_type,
                'severity' => $report->severity,
                'hazards' => $report->hazards ?? [],
                'location_text' => $report->location_text,
                'resolved_area' => $report->resolved_area,
                'status' => $report->status,
                'routing_confidence' => $report->routing_confidence,
                'routing_flags' => $report->routing_flags ?? [],
                'clarifying_question' => $report->clarifying_question,
                'draft_en' => $report->draft_en,
                'draft_ur' => $report->draft_ur,
                'requested_remedy' => $report->requested_remedy,
            ],
            'routing' => $this->routingForDisplay($report),
            'issueTypes' => self::ISSUE_TYPES,
        ]);
    }

    public function clarify(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'answer' => ['required', 'string', 'max:1000'],
        ]);

        $report->clarifying_answer = $validated['answer'];
        $report->save();

        $this->runPipeline($report);

        return redirect()->route('reports.show', $report);
    }

    public function confirmCategory(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'issue_type' => ['required', 'string', 'in:'.implode(',', self::ISSUE_TYPES)],
        ]);

        $report->issue_type = $validated['issue_type'];
        $report->resolveLocation();
        $override = $report->applySpecialZoneOverride();
        $report->routing = $report->applyRoutingRule($override);
        $report->routing_confidence = $report->scoreConfidence($override);
        $report->draft_en = $report->templateDraft('en');
        $report->draft_ur = $report->templateDraft('ur');
        $report->requested_remedy = 'other';
        $report->status = 'drafted';
        $report->save();

        return redirect()->route('reports.show', $report);
    }

    /**
     * Stages 3-9: classify, resolve location, route, score confidence, and
     * draft. Any failure (network, timeout, empty model output) is caught
     * so the citizen always gets a routed, sendable complaint — via the AI
     * draft when possible, via the PHP template when not.
     */
    private function runPipeline(Report $report, ?UploadedFile $freshPhoto = null): void
    {
        try {
            $attachments = [];

            if ($freshPhoto) {
                $attachments[] = $freshPhoto;
            } elseif ($report->photo_path) {
                $attachments[] = Files\Image::fromStorage($report->photo_path, disk: 'public');
            }

            $textForClassification = trim($report->raw_text.($report->clarifying_answer
                ? "\n\nAdditional detail from the citizen: {$report->clarifying_answer}"
                : ''));

            $cacheKey = 'ai:classify:'.sha1($textForClassification.($report->photo_path ?? ''));

            $classification = Cache::remember(
                $cacheKey,
                now()->addDays(7),
                fn () => (new ClassifyReport)->prompt($textForClassification, attachments: $attachments)->toArray(),
            );

            $report->classification = $classification;
            $report->issue_type = $classification['issue_type'];
            $report->severity = $classification['severity'];
            $report->hazards = $classification['hazards'];
            $report->input_language = $classification['input_language'];
            $report->location_text = collect($classification['location'] ?? [])->filter()->implode(', ');
            $report->clarifying_question = $classification['clarifying_question'] ?? null;

            $report->resolveLocation();
            $override = $report->applySpecialZoneOverride();
            $report->routing = $report->applyRoutingRule($override);
            $report->routing_confidence = $report->scoreConfidence($override);

            if ($report->routing_confidence === 'low' && ! $report->clarifying_answer) {
                $report->status = 'awaiting_answer';
                $report->save();

                return;
            }

            if ($report->routing_confidence === 'needs_human_review') {
                $report->status = 'needs_review';
                $report->save();

                return;
            }

            $this->draftComplaint($report);
        } catch (Throwable $e) {
            report($e);

            $report->status = 'ai_failed';
            $report->ai_failed_at = now();
            $report->save();
        }
    }

    private function draftComplaint(Report $report): void
    {
        $recipients = collect($report->routing)->map(fn (array $r) => [
            'name' => Authority::find($r['authority_id'])?->name ?? $r['authority_id'],
            'role' => $r['role'],
            'reason' => $r['reason'],
        ])->all();

        $prompt = implode("\n", [
            'Classified issue: '.$report->issue_type,
            'Severity: '.$report->severity,
            'Location: '.($report->location_text ?: 'not provided'),
            'Observed since: '.($report->classification['observed_when'] ?? 'unknown'),
            'Hazards: '.implode(', ', $report->hazards ?: []),
            'Recipients (name, role, reason): '.json_encode($recipients),
            'Citizen consented to share phone number: '.($report->citizen_phone ? 'yes, '.$report->citizen_phone : 'no'),
            'Photo attached: '.($report->photo_path ? 'yes' : 'no'),
            'Original report text: '.$report->raw_text,
        ]);

        $cacheKey = 'ai:draft:'.sha1(json_encode($report->routing).$report->raw_text);

        $draft = Cache::remember(
            $cacheKey,
            now()->addDays(7),
            fn () => (new DraftComplaint)->prompt($prompt)->toArray(),
        );

        $report->draft_en = $draft['body_en'];
        $report->draft_ur = $draft['body_ur'];
        $report->requested_remedy = $draft['requested_remedy'];
        $report->status = 'drafted';
        $report->save();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function routingForDisplay(Report $report): array
    {
        return collect($report->routing ?? [])->map(function (array $r) {
            $authority = Authority::find($r['authority_id']);

            return [
                'authority_id' => $r['authority_id'],
                'name' => $authority?->name ?? $r['authority_id'],
                'role' => $r['role'],
                'reason' => $r['reason'],
                'rule' => $r['rule'],
                'phone' => $authority?->phone_verified ? $authority->phone : null,
                'email' => $authority?->email_verified ? $authority->email : null,
                'website' => $authority?->website_verified ? $authority->website : null,
                'contact_unverified' => ! $authority
                    || ! ($authority->phone_verified || $authority->email_verified || $authority->website_verified),
            ];
        })->all();
    }
}
