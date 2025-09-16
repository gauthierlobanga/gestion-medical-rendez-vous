<?php

namespace App\Filament\Resources\NotificationRendezVouses;

use App\Filament\Resources\NotificationRendezVouses\Pages\CreateNotificationRendezVous;
use App\Filament\Resources\NotificationRendezVouses\Pages\EditNotificationRendezVous;
use App\Filament\Resources\NotificationRendezVouses\Pages\ListNotificationRendezVouses;
use App\Filament\Resources\NotificationRendezVouses\Schemas\NotificationRendezVousForm;
use App\Filament\Resources\NotificationRendezVouses\Tables\NotificationRendezVousesTable;
use App\Models\NotificationRendezVous;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationRendezVousResource extends Resource
{
    protected static ?string $model = NotificationRendezVous::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // public static function form(Schema $schema): Schema
    // {
    //     return NotificationRendezVousForm::configure($schema);
    // }

    public static function table(Table $table): Table
    {
        return NotificationRendezVousesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationRendezVouses::route('/'),
            'create' => CreateNotificationRendezVous::route('/create'),
            'edit' => EditNotificationRendezVous::route('/{record}/edit'),
        ];
    }
}
