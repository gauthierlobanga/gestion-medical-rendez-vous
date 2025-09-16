<?php

namespace App\Filament\Resources\CreneauServices\Pages;

use App\Filament\Resources\CreneauServices\CreneauServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCreneauService extends EditRecord
{
    protected static string $resource = CreneauServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
