<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Models\Report;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->disk('public')
                    ->square()
                    // Nothing has a photo yet, and 14 empty squares in the first
                    // column read as a broken table. The column reappears on its
                    // own as soon as a single report carries one.
                    ->visible(fn (): bool => Report::query()->whereNotNull('photo_path')->exists()),
                TextColumn::make('issue_type')
                    ->label('Issue')
                    ->formatStateUsing(fn (?string $state): string => $state ? Str::headline($state) : '—')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('severity')
                    ->badge()
                    ->sortable(),
                TextColumn::make('resolved_area')
                    ->label('Area')
                    // Two reports legitimately have no area: one is awaiting a
                    // clarifying answer, the other never ran the pipeline. Saying
                    // so beats a blank cell that reads as missing data.
                    ->placeholder('Not yet resolved')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'awaiting_answer' => 'warning',
                        'drafted' => 'success',
                        'ai_failed' => 'danger',
                        'needs_review' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('routing_confidence')
                    ->label('Confidence')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'needs_human_review'
                        ? 'Needs a human'
                        : Str::ucfirst((string) $state))
                    ->color(fn (?string $state): string => match ($state) {
                        'high' => 'success',
                        'medium' => 'warning',
                        'low' => 'danger',
                        'needs_human_review' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('Not routed')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->tooltip(fn ($state): ?string => $state?->toDayDateTimeString())
                    ->sortable(),
            ])
            // Anything a human still has to deal with floats to the top, newest
            // first within that. Sorting purely by date buried the two reports
            // that most need attention — the unresolved-jurisdiction case and
            // the AI failure — on the second page, which is exactly backwards
            // for a triage screen.
            ->defaultSort(fn (Builder $query): Builder => $query
                ->orderByRaw("CASE WHEN status IN ('needs_review', 'ai_failed', 'awaiting_answer') THEN 0 ELSE 1 END")
                ->orderByDesc('created_at'))
            ->groups([
                'resolved_area',
                'issue_type',
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'awaiting_answer' => 'Awaiting answer',
                        'drafted' => 'Drafted',
                        'ai_failed' => 'AI failed',
                        'needs_review' => 'Needs review',
                    ]),
                SelectFilter::make('issue_type')
                    ->options(fn (): array => Report::query()
                        ->whereNotNull('issue_type')
                        ->distinct()
                        ->orderBy('issue_type')
                        ->pluck('issue_type', 'issue_type')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
