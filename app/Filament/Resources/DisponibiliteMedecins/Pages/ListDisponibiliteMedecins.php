<?php

namespace App\Filament\Resources\DisponibiliteMedecins\Pages;

use App\Filament\Resources\DisponibiliteMedecins\DisponibiliteMedecinResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisponibiliteMedecins extends ListRecords
{
    protected static string $resource = DisponibiliteMedecinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
