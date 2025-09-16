<?php

namespace App\Filament\Resources\CreneauServices;

use App\Filament\Resources\CreneauServices\Pages\CreateCreneauService;
use App\Filament\Resources\CreneauServices\Pages\EditCreneauService;
use App\Filament\Resources\CreneauServices\Pages\ListCreneauServices;
use App\Filament\Resources\CreneauServices\Schemas\CreneauServiceForm;
use App\Filament\Resources\CreneauServices\Tables\CreneauServicesTable;
use App\Models\CreneauService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CreneauServiceResource extends Resource
{
    protected static ?string $model = CreneauService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jour_semaine';

    public static function form(Schema $schema): Schema
    {
        return CreneauServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreneauServicesTable::configure($table);
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
            'index' => ListCreneauServices::route('/'),
            'create' => CreateCreneauService::route('/create'),
            'edit' => EditCreneauService::route('/{record}/edit'),
        ];
    }
}
