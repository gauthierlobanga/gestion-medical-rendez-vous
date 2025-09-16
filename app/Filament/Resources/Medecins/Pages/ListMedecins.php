<?php

namespace App\Filament\Resources\Medecins\Pages;

use App\Filament\Resources\Medecins\MedecinResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedecins extends ListRecords
{
    protected static string $resource = MedecinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
