<?php

namespace App\Filament\Resources\Authorities\Pages;

use App\Filament\Resources\Authorities\AuthorityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuthorities extends ListRecords
{
    protected static string $resource = AuthorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
