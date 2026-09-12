<?php

namespace App\Filament\Resources\GazetteerNodes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GazetteerNodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('uc_code')
                    ->label('UC')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('kind')
                    ->badge(),
                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('district')
                    ->searchable(),
                TextColumn::make('tmcAuthority.name')
                    ->label('TMC Authority')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('specialZoneAuthority.name')
                    ->label('Special Zone Authority')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('contact_name')
                    ->label('UC contact')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contact_phone')
                    ->label('UC phone')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('needs_human_review')
                    ->icon(fn (bool $state): Heroicon => $state ? Heroicon::OutlinedExclamationTriangle : Heroicon::OutlinedCheckCircle)
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success'),
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
                        'district' => 'District',
                        'town' => 'Town',
                        'landmark' => 'Landmark',
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
