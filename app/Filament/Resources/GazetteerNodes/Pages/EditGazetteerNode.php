<?php

namespace App\Filament\Resources\GazetteerNodes\Pages;

use App\Filament\Resources\GazetteerNodes\GazetteerNodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGazetteerNode extends EditRecord
{
    protected static string $resource = GazetteerNodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
