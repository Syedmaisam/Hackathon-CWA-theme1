<?php

namespace App\Filament\Resources\Authorities\Pages;

use App\Filament\Resources\Authorities\AuthorityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAuthority extends EditRecord
{
    protected static string $resource = AuthorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
