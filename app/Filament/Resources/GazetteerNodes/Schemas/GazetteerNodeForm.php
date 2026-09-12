<?php

namespace App\Filament\Resources\GazetteerNodes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GazetteerNodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        Select::make('kind')
                            ->options([
                                'district' => 'District',
                                'town' => 'Town',
                                'special_zone' => 'Special zone',
                                'landmark' => 'Landmark',
                            ])
                            ->required(),
                        TagsInput::make('aliases')
                            ->helperText('Every other spelling a resident might type, including Roman Urdu and Urdu script. The resolver substring-matches these, so more aliases means better location resolution.')
                            ->columnSpanFull(),
                        TextInput::make('district'),
                    ]),

                Section::make('Union council')
                    ->description('Set uc_code to make a landmark node a union council. The compose screen lists every town and every UC. Chairman contacts are personal numbers from the source dataset — admin-only, never shown to citizens.')
                    ->schema([
                        TextInput::make('uc_code')
                            ->label('UC code')
                            ->placeholder('UC-04'),
                        TextInput::make('contact_name')
                            ->label('Chairman / vice chairman'),
                        TextInput::make('contact_phone')
                            ->label('Contact number'),
                    ]),

                Section::make('Routing')
                    ->description('Special-zone authority beats the district hierarchy and is evaluated first. Needs-human-review beats both.')
                    ->schema([
                        Select::make('parent_id')
                            ->label('Parent node')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('District contains town contains landmark. The resolver walks up this chain.'),
                        Select::make('tmc_authority_id')
                            ->label('TMC authority')
                            ->relationship('tmcAuthority', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Set on town nodes. Used when a rule escalates internal streets to the town TMC.'),
                        Select::make('special_zone_authority_id')
                            ->label('Special-zone authority')
                            ->relationship('specialZoneAuthority', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Set on cantonments and similar. Overrides the normal district route for this node and everything under it.'),
                        Toggle::make('needs_human_review')
                            ->helperText('Jurisdiction here is genuinely unresolved. Reports resolving to this node route to the citizen portal fallback instead of guessing.'),
                    ]),
            ]);
    }
}
