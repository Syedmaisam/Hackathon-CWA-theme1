<?php

namespace App\Filament\Resources\Authorities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AuthorityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('short_name'),
                Select::make('kind')
                    ->options([
                        'citywide' => 'Citywide',
                        'tmc' => 'Tmc',
                        'cantonment' => 'Cantonment',
                        'estate' => 'Estate',
                        'fallback' => 'Fallback',
                    ])
                    ->required(),
                TextInput::make('district'),
                TextInput::make('website')
                    ->url(),
                Toggle::make('website_verified')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                Toggle::make('email_verified')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                Toggle::make('phone_verified')
                    ->required(),
                TextInput::make('secondary_phone')
                    ->tel(),
                TextInput::make('address'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('citizen_visible')
                    ->required(),
            ]);
    }
}
