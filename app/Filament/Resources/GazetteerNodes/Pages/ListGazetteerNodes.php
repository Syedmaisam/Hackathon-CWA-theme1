<?php

namespace App\Filament\Resources\GazetteerNodes\Pages;

use App\Filament\Resources\GazetteerNodes\GazetteerNodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGazetteerNodes extends ListRecords
{
    protected static string $resource = GazetteerNodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
