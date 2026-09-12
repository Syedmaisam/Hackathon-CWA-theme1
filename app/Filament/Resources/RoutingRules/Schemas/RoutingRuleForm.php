<?php

namespace App\Filament\Resources\RoutingRules\Schemas;

use App\Models\Authority;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoutingRuleForm
{
    /**
     * Flags the routing pipeline actually emits. Kept in sync with
     * App\Models\Report::applyRoutingRule() and SumairSeeder::seedRoutingRules().
     *
     * @var array<string, string>
     */
    private const FLAGS = [
        'road_ownership_uncertain' => 'Road ownership uncertain',
        'multi_agency_possible' => 'Multi-agency possible',
        'contact_unverified' => 'Contact unverified',
        'needs_human_review' => 'Needs human review',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Issue')
                    ->description('The issue type is the primary key of this table and cannot be changed after the rule is created.')
                    ->schema([
                        TextInput::make('issue_type')
                            ->required()
                            ->disabledOn('edit')
                            ->helperText('Lowercase snake_case, matching the classifier output. For example garbage, sewer_overflow, road_damage, streetlight.'),
                        Textarea::make('rule_note')
                            ->required()
                            ->helperText('Shown to the citizen as the reason this authority was chosen.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Routing')
                    ->schema([
                        Select::make('primary_authority_id')
                            ->label('Primary authority')
                            ->relationship('primaryAuthority', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('co_authority_ids')
                            ->label('Co-recipients')
                            ->multiple()
                            ->options(fn (): array => Authority::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->helperText('Copied in alongside the primary authority.'),
                        Toggle::make('internal_street_goes_to_tmc')
                            ->label('Internal streets go to the TMC')
                            ->helperText('When the report is on an internal street, route to the town TMC and demote this rule\'s primary authority to a co-recipient.'),
                        Select::make('flags')
                            ->multiple()
                            ->options(self::FLAGS)
                            ->helperText('Attached to every report routed by this rule and used to score routing confidence.'),
                    ]),
            ]);
    }
}
