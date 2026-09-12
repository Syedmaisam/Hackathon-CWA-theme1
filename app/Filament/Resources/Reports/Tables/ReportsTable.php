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

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->disk('public')
                    ->square(),
                TextColumn::make('issue_type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('severity')
                    ->badge()
                    ->sortable(),
                TextColumn::make('resolved_area')
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
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'high' => 'success',
                        'medium' => 'warning',
                        'low' => 'danger',
                        'needs_human_review' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
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
