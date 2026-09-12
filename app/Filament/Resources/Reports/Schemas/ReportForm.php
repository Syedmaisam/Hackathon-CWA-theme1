<?php

namespace App\Filament\Resources\Reports\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportForm
{
    /**
     * Kept in sync with the status filter in ReportsTable and with
     * App\Http\Controllers\Maisam\ReportController.
     *
     * @var array<string, string>
     */
    private const STATUSES = [
        'pending' => 'Pending',
        'awaiting_answer' => 'Awaiting answer',
        'drafted' => 'Drafted',
        'ai_failed' => 'AI failed',
        'needs_review' => 'Needs review',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Citizen & intake')
                    ->schema([
                        TextInput::make('citizen_name'),
                        TextInput::make('citizen_phone')
                            ->tel(),
                        Select::make('input_mode')
                            ->options(['text' => 'Text', 'voice' => 'Voice', 'photo' => 'Photo'])
                            ->required(),
                        TextInput::make('input_language')
                            ->helperText('en, ur, roman_urdu or mixed.'),
                        Textarea::make('raw_text')
                            ->label('Report as submitted')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('photo_path'),
                        DateTimePicker::make('observed_at'),
                    ]),

                Section::make('Classification')
                    ->description('Set by the classifier. Correct the issue type or severity here when the classifier got it wrong; the full classification payload is on the view page.')
                    ->schema([
                        TextInput::make('issue_type'),
                        TextInput::make('severity')
                            ->helperText('low, medium, high or emergency.'),
                        TextInput::make('requested_remedy'),
                    ]),

                Section::make('Location & routing')
                    ->description('Resolved by the gazetteer and the routing rules. Editing these does not re-run the pipeline.')
                    ->schema([
                        TextInput::make('location_text'),
                        Select::make('gazetteer_node_id')
                            ->label('Gazetteer node')
                            ->relationship('gazetteerNode', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('resolved_area')
                            ->helperText('Town or special zone the report was clustered into.'),
                        TextInput::make('resolved_special_zone'),
                        Select::make('routing_confidence')
                            ->options([
                                'high' => 'High',
                                'medium' => 'Medium',
                                'low' => 'Low',
                                'needs_human_review' => 'Needs human review',
                            ]),
                    ]),

                Section::make('Clarification & drafts')
                    ->schema([
                        TextInput::make('clarifying_question'),
                        TextInput::make('clarifying_answer'),
                        Textarea::make('draft_en')
                            ->label('Draft (English)')
                            ->rows(8)
                            ->columnSpanFull(),
                        Textarea::make('draft_ur')
                            ->label('Draft (Urdu)')
                            ->rows(8)
                            ->extraInputAttributes(['dir' => 'rtl'])
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Select::make('status')
                            ->options(self::STATUSES)
                            ->required()
                            ->default('pending'),
                        DateTimePicker::make('ai_failed_at'),
                    ]),
            ]);
    }
}
