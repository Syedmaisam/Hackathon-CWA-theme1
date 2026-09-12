<?php

namespace App\Filament\Resources\GazetteerNodes;

use App\Filament\Resources\GazetteerNodes\Pages\CreateGazetteerNode;
use App\Filament\Resources\GazetteerNodes\Pages\EditGazetteerNode;
use App\Filament\Resources\GazetteerNodes\Pages\ListGazetteerNodes;
use App\Filament\Resources\GazetteerNodes\Schemas\GazetteerNodeForm;
use App\Filament\Resources\GazetteerNodes\Tables\GazetteerNodesTable;
use App\Models\GazetteerNode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GazetteerNodeResource extends Resource
{
    protected static ?string $model = GazetteerNode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|UnitEnum|null $navigationGroup = 'Civic Routing';

    public static function form(Schema $schema): Schema
    {
        return GazetteerNodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GazetteerNodesTable::configure($table);
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
            'index' => ListGazetteerNodes::route('/'),
            'create' => CreateGazetteerNode::route('/create'),
            'edit' => EditGazetteerNode::route('/{record}/edit'),
        ];
    }
}
