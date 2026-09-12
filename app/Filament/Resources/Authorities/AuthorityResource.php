<?php

namespace App\Filament\Resources\Authorities;

use App\Filament\Resources\Authorities\Pages\CreateAuthority;
use App\Filament\Resources\Authorities\Pages\EditAuthority;
use App\Filament\Resources\Authorities\Pages\ListAuthorities;
use App\Filament\Resources\Authorities\Schemas\AuthorityForm;
use App\Filament\Resources\Authorities\Tables\AuthoritiesTable;
use App\Models\Authority;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AuthorityResource extends Resource
{
    protected static ?string $model = Authority::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'Civic Routing';

    public static function form(Schema $schema): Schema
    {
        return AuthorityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuthoritiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuthorities::route('/'),
            'create' => CreateAuthority::route('/create'),
            'edit' => EditAuthority::route('/{record}/edit'),
        ];
    }
}
