<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Citizen & Intake')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('citizen_name')
                            ->placeholder('—'),
                        TextEntry::make('citizen_phone')
                            ->placeholder('—'),
                        TextEntry::make('input_mode')
                            ->badge(),
                        TextEntry::make('input_language')
                            ->placeholder('—'),
                        TextEntry::make('observed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('raw_text')
                            ->columnSpanFull(),
                        ImageEntry::make('photo_path')
                            ->label('Photo')
                            ->disk('public')
                            ->visible(fn (?string $state): bool => filled($state))
                            ->columnSpanFull(),
                    ]),

                Section::make('Classification')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('issue_type')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('severity')
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('requested_remedy')
                            ->placeholder('—'),
                        KeyValueEntry::make('classification')
                            ->columnSpanFull(),
                        KeyValueEntry::make('hazards')
                            ->columnSpanFull(),
                    ]),

                Section::make('Location & Routing')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('location_text')
                            ->placeholder('—'),
                        TextEntry::make('gazetteerNode.name')
                            ->label('Gazetteer Node')
                            ->placeholder('—'),
                        TextEntry::make('resolved_area')
                            ->placeholder('—'),
                        TextEntry::make('resolved_special_zone')
                            ->placeholder('—'),
                        TextEntry::make('routing_confidence')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'high' => 'success',
                                'medium' => 'warning',
                                'low' => 'danger',
                                'needs_human_review' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('—'),
                        KeyValueEntry::make('routing')
                            ->columnSpanFull(),
                        KeyValueEntry::make('routing_flags')
                            ->columnSpanFull(),
                    ]),

                Section::make('Clarification')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('clarifying_question')
                            ->placeholder('—'),
                        TextEntry::make('clarifying_answer')
                            ->placeholder('—'),
                    ]),

                Section::make('Drafts')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('draft_en')
                            ->label('Draft (English)')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('draft_ur')
                            ->label('Draft (Urdu)')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'awaiting_answer' => 'warning',
                                'drafted' => 'success',
                                'ai_failed' => 'danger',
                                'needs_review' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('ai_failed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
