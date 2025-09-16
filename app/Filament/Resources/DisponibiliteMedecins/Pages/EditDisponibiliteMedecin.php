<?php

namespace App\Filament\Resources\DisponibiliteMedecins\Pages;

use App\Filament\Resources\DisponibiliteMedecins\DisponibiliteMedecinResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDisponibiliteMedecin extends EditRecord
{
    protected static string $resource = DisponibiliteMedecinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
