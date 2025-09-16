<?php

namespace App\Filament\Resources\Rendezvouses\Pages;

use App\Filament\Resources\Rendezvouses\RendezvousResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRendezvouses extends ListRecords
{
    protected static string $resource = RendezvousResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
