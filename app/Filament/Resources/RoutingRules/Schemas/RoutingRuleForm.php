<?php

namespace App\Filament\Resources\RoutingRules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoutingRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('primary_authority_id')
                    ->relationship('primaryAuthority', 'name')
                    ->required(),
                TextInput::make('co_authority_ids'),
                Toggle::make('internal_street_goes_to_tmc')
                    ->required(),
                TextInput::make('flags'),
                Textarea::make('rule_note')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
