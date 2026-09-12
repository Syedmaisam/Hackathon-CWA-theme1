<?php

namespace App\Filament\Resources\Authorities\Tables;

use App\Models\Authority;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuthoritiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('kind')
                    ->badge(),
                TextColumn::make('phone')
                    ->placeholder('—')
                    ->icon(fn (Authority $record): ?Heroicon => $record->phone_verified ? null : Heroicon::OutlinedExclamationTriangle)
                    ->iconColor('danger')
                    ->iconPosition('after')
                    ->tooltip(fn (Authority $record): ?string => $record->phone_verified ? null : 'Unverified'),
                TextColumn::make('email')
                    ->placeholder('—')
                    ->icon(fn (Authority $record): ?Heroicon => $record->email_verified ? null : Heroicon::OutlinedExclamationTriangle)
                    ->iconColor('danger')
                    ->iconPosition('after')
                    ->tooltip(fn (Authority $record): ?string => $record->email_verified ? null : 'Unverified'),
                TextColumn::make('website')
                    ->placeholder('—')
                    ->icon(fn (Authority $record): ?Heroicon => $record->website_verified ? null : Heroicon::OutlinedExclamationTriangle)
                    ->iconColor('danger')
                    ->iconPosition('after')
                    ->tooltip(fn (Authority $record): ?string => $record->website_verified ? null : 'Unverified'),
                IconColumn::make('citizen_visible')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kind')
                    ->options([
                        'citywide' => 'Citywide',
                        'tmc' => 'TMC',
                        'cantonment' => 'Cantonment',
                        'estate' => 'Estate',
                        'fallback' => 'Fallback',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
