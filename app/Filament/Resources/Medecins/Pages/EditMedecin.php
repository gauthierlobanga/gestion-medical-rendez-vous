<?php

namespace App\Filament\Resources\Medecins\Pages;

use App\Filament\Resources\Medecins\MedecinResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedecin extends EditRecord
{
    protected static string $resource = MedecinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
