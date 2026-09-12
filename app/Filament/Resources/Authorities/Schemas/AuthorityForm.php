<?php

namespace App\Filament\Resources\Authorities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuthorityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        TextInput::make('id')
                            ->label('Slug')
                            ->required()
                            ->disabledOn('edit')
                            ->helperText('Lowercase identifier used by the routing rules, for example kmc or cb_clifton.'),
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('short_name'),
                        Select::make('kind')
                            ->options([
                                'citywide' => 'Citywide',
                                'tmc' => 'TMC',
                                'cantonment' => 'Cantonment',
                                'estate' => 'Estate',
                                'fallback' => 'Fallback',
                            ])
                            ->required(),
                        TextInput::make('district'),
                    ]),

                Section::make('Contacts')
                    ->description('Leave a contact unverified unless it has been confirmed against the authority\'s own site or call centre. Unverified contacts are never shown to a citizen as confirmed.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('website')
                            ->url()
                            ->columnSpan(2),
                        Toggle::make('website_verified')
                            ->label('Verified'),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->columnSpan(2),
                        Toggle::make('email_verified')
                            ->label('Verified'),
                        TextInput::make('phone')
                            ->tel()
                            ->columnSpan(2),
                        Toggle::make('phone_verified')
                            ->label('Verified'),
                        TextInput::make('secondary_phone')
                            ->tel()
                            ->columnSpan(3),
                        TextInput::make('address')
                            ->columnSpan(3),
                    ]),

                Section::make('Review')
                    ->schema([
                        Toggle::make('citizen_visible')
                            ->label('Safe to show to citizens')
                            ->helperText('Turn this off when there is no verified citizen-facing channel. Reports routed here also get a KMC 1339 escalation recipient.'),
                        Textarea::make('notes')
                            ->helperText('Provenance and verification status, carried over from the source of truth document.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
