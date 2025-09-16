<?php

namespace App\Filament\Resources\NotificationRendezVouses\Pages;

use App\Filament\Resources\NotificationRendezVouses\NotificationRendezVousResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNotificationRendezVous extends EditRecord
{
    protected static string $resource = NotificationRendezVousResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
