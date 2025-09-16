<?php

namespace App\Filament\Resources\Rendezvouses\Pages;

use App\Filament\Resources\Rendezvouses\RendezvousResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRendezvous extends EditRecord
{
    protected static string $resource = RendezvousResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
