<?php

namespace App\Filament\Resources\GazetteerNodes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GazetteerNodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('aliases'),
                Select::make('kind')
                    ->options([
                        'district' => 'District',
                        'town' => 'Town',
                        'special_zone' => 'Special zone',
                        'landmark' => 'Landmark',
                    ])
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'name'),
                TextInput::make('district'),
                Select::make('tmc_authority_id')
                    ->relationship('tmcAuthority', 'name'),
                Select::make('special_zone_authority_id')
                    ->relationship('specialZoneAuthority', 'name'),
                Toggle::make('needs_human_review')
                    ->required(),
            ]);
    }
}
