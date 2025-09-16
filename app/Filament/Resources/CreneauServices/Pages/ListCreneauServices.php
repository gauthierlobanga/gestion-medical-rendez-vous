<?php

namespace App\Filament\Resources\CreneauServices\Pages;

use App\Filament\Resources\CreneauServices\CreneauServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCreneauServices extends ListRecords
{
    protected static string $resource = CreneauServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
