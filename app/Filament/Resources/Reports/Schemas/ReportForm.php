<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('citizen_name'),
                TextInput::make('citizen_phone')
                    ->tel(),
                Select::make('input_mode')
                    ->options(['text' => 'Text', 'voice' => 'Voice', 'photo' => 'Photo'])
                    ->required(),
                TextInput::make('input_language'),
                Textarea::make('raw_text')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('photo_path'),
                DateTimePicker::make('observed_at'),
                TextInput::make('classification'),
                TextInput::make('issue_type'),
                TextInput::make('severity'),
                TextInput::make('hazards'),
                TextInput::make('location_text'),
                Select::make('gazetteer_node_id')
                    ->relationship('gazetteerNode', 'name'),
                TextInput::make('resolved_area'),
                TextInput::make('resolved_special_zone'),
                TextInput::make('routing'),
                TextInput::make('routing_confidence'),
                TextInput::make('routing_flags'),
                TextInput::make('clarifying_question'),
                TextInput::make('clarifying_answer'),
                Textarea::make('draft_en')
                    ->columnSpanFull(),
                Textarea::make('draft_ur')
                    ->columnSpanFull(),
                TextInput::make('requested_remedy'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                DateTimePicker::make('ai_failed_at'),
            ]);
    }
}
