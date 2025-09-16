<?php

namespace App\Filament\Resources\NotificationRendezVouses\Pages;

use App\Filament\Resources\NotificationRendezVouses\NotificationRendezVousResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotificationRendezVouses extends ListRecords
{
    protected static string $resource = NotificationRendezVousResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
