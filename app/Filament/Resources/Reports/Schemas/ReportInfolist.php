<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Models\Authority;
use App\Models\Report;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ReportInfolist
{
    /**
     * The admin's read view of one report. This screen answers three questions
     * in order: what did the citizen say, where did it go, and what was sent.
     * Everything else is supporting detail.
     *
     * It deliberately renders no raw JSON. The scaffold showed `classification`
     * and `routing` as key/value dumps, which meant an authority appeared only
     * as the slug `sswmb`, the location as four nulls, and severity, hazards
     * and issue type each appeared twice on the same screen.
     */
    public static function configure(Schema $schema): Schema
    {
        // One column. The default two-column grid sized each card against its
        // neighbour, so a short "Routed to" card left a tall empty well beside
        // a long draft. Reading order also matters here: complaint, then where
        // it went, then what was sent.
        return $schema
            ->columns(1)
            ->components([
                self::outcome(),
                self::complaint(),
                self::routing(),
                self::drafts(),
                self::analysis(),
            ]);
    }

    /**
     * The headline: status, confidence and where it landed. An admin opening a
     * report wants this without scrolling, so it leads and spans the width.
     */
    private static function outcome(): Section
    {
        return Section::make()
            ->columns(4)
            ->schema([
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'drafted' => 'success',
                        'awaiting_answer', 'needs_review' => 'warning',
                        'ai_failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'awaiting_answer' => 'Awaiting citizen answer',
                        'needs_review' => 'Needs a human',
                        'ai_failed' => 'AI failed',
                        'drafted' => 'Drafted',
                        'pending' => 'Still processing',
                        default => Str::headline((string) $state),
                    }),
                TextEntry::make('routing_confidence')
                    ->label('Confidence')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'high' => 'success',
                        'medium' => 'warning',
                        'low', 'needs_human_review' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => $state === 'needs_human_review'
                        ? 'Needs a human'
                        : Str::ucfirst((string) $state))
                    ->placeholder('Not routed'),
                TextEntry::make('resolved_area')
                    ->label('Area')
                    ->icon('heroicon-m-map-pin')
                    ->placeholder('Not yet resolved'),
                TextEntry::make('created_at')
                    ->label('Received')
                    ->since()
                    ->tooltip(fn ($state): ?string => $state?->toDayDateTimeString()),
            ]);
    }

    /**
     * What the citizen actually sent, in their own words.
     */
    private static function complaint(): Section
    {
        return Section::make('The complaint')
            ->icon('heroicon-m-chat-bubble-left-right')
            ->columns(4)
            ->schema([
                TextEntry::make('raw_text')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    // Urdu was rendering left-to-right in a Latin font, which
                    // is unreadable. Direction follows the detected language.
                    ->formatStateUsing(fn (?string $state, Report $record): HtmlString => new HtmlString(
                        '<div dir="'.($record->input_language === 'ur' ? 'rtl' : 'ltr').'" style="white-space:pre-wrap">'
                        .e((string) $state).'</div>'
                    )),
                ImageEntry::make('photo_path')
                    ->label('Photo')
                    ->disk('public')
                    ->visible(fn (?string $state): bool => filled($state))
                    ->columnSpanFull(),
                TextEntry::make('citizen_name')
                    ->label('Name')
                    ->placeholder('Anonymous'),
                TextEntry::make('citizen_phone')
                    ->label('Phone')
                    ->placeholder('Not shared')
                    ->copyable(),
                TextEntry::make('input_mode')
                    ->label('Sent by')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'voice' => 'Voice note',
                        'photo' => 'Photo',
                        default => 'Typed',
                    }),
                TextEntry::make('input_language')
                    ->label('Language')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ur' => 'Urdu',
                        'en' => 'English',
                        default => Str::upper((string) $state),
                    })
                    ->placeholder('Unknown'),

                // Only shown once a clarifying question exists, so a report
                // that never needed one does not carry two empty rows.
                TextEntry::make('clarifying_question')
                    ->label('We asked')
                    ->columnSpan(2)
                    ->visible(fn (Report $record): bool => filled($record->clarifying_question)),
                TextEntry::make('clarifying_answer')
                    ->label('They answered')
                    ->columnSpan(2)
                    ->placeholder('Still waiting')
                    ->visible(fn (Report $record): bool => filled($record->clarifying_question)),
            ]);
    }

    /**
     * Where it was sent and why. The scaffold showed the authority as a slug
     * inside a JSON blob; an admin checking a routing decision needs the
     * authority's real name, the rule that fired, and a way to contact them.
     */
    private static function routing(): Section
    {
        return Section::make('Routed to')
            ->icon('heroicon-m-arrow-right-circle')
            ->schema([
                // Built from the record, not the entry state: `routing` is an
                // array cast, so Filament calls formatStateUsing once per item
                // and hands the closure a string rather than the whole list.
                TextEntry::make('routing_summary')
                    ->hiddenLabel()
                    ->state(fn (Report $record): ?HtmlString => self::routingHtml($record))
                    ->placeholder('Not routed to anyone yet.')
                    ->columnSpanFull(),

                Grid::make(3)->schema([
                    TextEntry::make('gazetteerNode.name')
                        ->label('Place picked')
                        ->placeholder('None — resolved from text'),
                    TextEntry::make('location_text')
                        ->label('Location mentioned')
                        ->placeholder('None'),
                    TextEntry::make('resolved_special_zone')
                        ->label('Special zone')
                        ->placeholder('None'),
                ]),

                // Flags only matter when something is wrong with the routing.
                TextEntry::make('routing_flags')
                    ->label('Flags')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state): string => Str::headline((string) $state))
                    ->visible(fn (Report $record): bool => filled($record->routing_flags))
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Each routed authority as a name, the rule that picked it, its contacts
     * and the reason. Returns null when nothing routed, so the entry falls
     * through to its placeholder.
     */
    private static function routingHtml(Report $record): ?HtmlString
    {
        $rows = collect($record->routing ?? [])
            ->filter(fn ($r): bool => is_array($r))
            ->map(function (array $r): string {
                $authority = Authority::find($r['authority_id'] ?? '');
                $contacts = collect([$authority?->phone, $authority?->email])->filter()->implode(' · ');
                $meta = collect([
                    filled($r['role'] ?? null) ? Str::headline($r['role']) : null,
                    filled($r['rule'] ?? null) ? 'rule: '.$r['rule'] : null,
                ])->filter()->implode(' · ');

                return implode('', [
                    '<div style="padding:.75rem 0;border-top:1px solid rgba(128,128,128,.2)">',
                    '<div style="font-weight:600">'.e($authority?->name ?? $r['authority_id'] ?? 'Unknown authority').'</div>',
                    $meta ? '<div style="font-size:.75rem;opacity:.6;text-transform:uppercase;letter-spacing:.04em">'.e($meta).'</div>' : '',
                    $contacts ? '<div style="font-size:.8125rem;opacity:.85;margin-top:.25rem">Contact: '.e($contacts).'</div>' : '',
                    filled($r['reason'] ?? null) ? '<div style="font-size:.8125rem;opacity:.75;margin-top:.375rem">'.e($r['reason']).'</div>' : '',
                    '</div>',
                ]);
            })
            ->implode('');

        return $rows ? new HtmlString($rows) : null;
    }

    /**
     * The two letters the citizen can send. These are long, so they sit in
     * their own full-width section rather than a half-width card.
     */
    private static function drafts(): Section
    {
        return Section::make('Drafted complaint')
            ->icon('heroicon-m-document-text')
            ->columns(2)
            ->collapsible()
            ->schema([
                TextEntry::make('draft_en')
                    ->label('English')
                    ->placeholder('No English draft.')
                    ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString(
                        '<div style="white-space:pre-wrap;line-height:1.6">'.e((string) $state).'</div>'
                    )),
                TextEntry::make('draft_ur')
                    ->label('Urdu')
                    ->placeholder('No Urdu draft.')
                    // Urdu needs right-to-left and a Nastaliq-capable stack,
                    // or it renders as unreadable disconnected letterforms.
                    ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString(
                        '<div dir="rtl" lang="ur" style="white-space:pre-wrap;line-height:2.1;font-family:\'Noto Nastaliq Urdu\',serif">'
                        .e((string) $state).'</div>'
                    )),
                TextEntry::make('requested_remedy')
                    ->label('Requested remedy')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => Str::headline((string) $state))
                    ->placeholder('None')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * What the model concluded. Collapsed by default — it is diagnostic detail,
     * and the fields that matter are already promoted above.
     */
    private static function analysis(): Section
    {
        return Section::make('AI analysis')
            ->icon('heroicon-m-sparkles')
            ->columns(4)
            ->collapsible()
            ->collapsed()
            ->schema([
                TextEntry::make('classification.summary_en')
                    ->label('Summary')
                    ->columnSpanFull()
                    ->placeholder('No summary.'),
                TextEntry::make('issue_type')
                    ->label('Issue')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Str::headline((string) $state))
                    ->placeholder('Unclassified'),
                TextEntry::make('severity')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => Str::ucfirst((string) $state))
                    ->placeholder('—'),
                TextEntry::make('classification.issue_confidence')
                    ->label('Issue confidence')
                    ->formatStateUsing(fn (?string $state): string => Str::ucfirst((string) $state))
                    ->placeholder('—'),
                TextEntry::make('classification.observed_when')
                    ->label('Observed')
                    ->placeholder('Unknown'),
                TextEntry::make('hazards')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state): string => Str::headline((string) $state))
                    ->placeholder('None identified')
                    ->columnSpanFull(),

                // The scaffold rendered this as {"area":"Saddar","road":null,
                // "landmark":null,...}. Only the parts the model actually found
                // are worth showing.
                TextEntry::make('location_detected')
                    ->label('Location detected')
                    ->columnSpanFull()
                    ->placeholder('Nothing detected')
                    ->state(fn (Report $record): ?string => collect($record->classification['location'] ?? [])
                        ->filter()
                        ->map(fn ($v, $k): string => Str::headline($k).': '.$v)
                        ->implode(' · ') ?: null),
                TextEntry::make('ai_failed_at')
                    ->label('AI failed at')
                    ->dateTime()
                    ->color('danger')
                    ->visible(fn (Report $record): bool => filled($record->ai_failed_at))
                    ->columnSpan(2),
                TextEntry::make('updated_at')
                    ->label('Last updated')
                    ->since()
                    ->tooltip(fn ($state): ?string => $state?->toDayDateTimeString())
                    ->columnSpan(2),
            ]);
    }
}
