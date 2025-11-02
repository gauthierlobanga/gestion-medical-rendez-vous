<?php

namespace App\Filament\Resources\Rendezvouses;

use App\Filament\Resources\Rendezvouses\Pages\CreateRendezvous;
use App\Filament\Resources\Rendezvouses\Pages\EditRendezvous;
use App\Filament\Resources\Rendezvouses\Pages\ListRendezvouses;
use App\Filament\Resources\Rendezvouses\Schemas\RendezvousForm;
use App\Filament\Resources\Rendezvouses\Tables\RendezvousesTable;
use App\Models\Rendezvous;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RendezvousResource extends Resource
{
    protected static ?string $model = Rendezvous::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;
    protected static string|UnitEnum|null $navigationGroup = 'Gestion des rendez-vous';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationLabel = 'Rendez-vous';

    protected static ?string $recordTitleAttribute = 'specialite';

    public static function form(Schema $schema): Schema
    {
        return RendezvousForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RendezvousesTable::configure($table);
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
            'index' => ListRendezvouses::route('/'),
            // 'create' => CreateRendezvous::route('/create'),
            'edit' => EditRendezvous::route('/{record}/edit'),
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'success' : 'danger';
    }
}
