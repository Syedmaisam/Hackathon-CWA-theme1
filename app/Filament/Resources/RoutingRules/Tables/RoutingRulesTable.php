<?php

namespace App\Filament\Resources\RoutingRules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoutingRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('issue_type')
                    ->searchable(),
                TextColumn::make('primaryAuthority.name')
                    ->label('Primary Authority')
                    ->searchable(),
                IconColumn::make('internal_street_goes_to_tmc')
                    ->boolean(),
                TextColumn::make('rule_note')
                    ->limit(50)
                    ->tooltip(fn (?string $state): ?string => $state),
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
                //
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
