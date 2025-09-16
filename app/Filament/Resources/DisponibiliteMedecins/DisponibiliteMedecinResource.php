<?php

namespace App\Filament\Resources\DisponibiliteMedecins;

use App\Filament\Resources\DisponibiliteMedecins\Pages\CreateDisponibiliteMedecin;
use App\Filament\Resources\DisponibiliteMedecins\Pages\EditDisponibiliteMedecin;
use App\Filament\Resources\DisponibiliteMedecins\Pages\ListDisponibiliteMedecins;
use App\Filament\Resources\DisponibiliteMedecins\Schemas\DisponibiliteMedecinForm;
use App\Filament\Resources\DisponibiliteMedecins\Tables\DisponibiliteMedecinsTable;
use App\Models\DisponibiliteMedecin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DisponibiliteMedecinResource extends Resource
{
    protected static ?string $model = DisponibiliteMedecin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jour_semaine';

    public static function form(Schema $schema): Schema
    {
        return DisponibiliteMedecinForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisponibiliteMedecinsTable::configure($table);
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
            'index' => ListDisponibiliteMedecins::route('/'),
            'create' => CreateDisponibiliteMedecin::route('/create'),
            'edit' => EditDisponibiliteMedecin::route('/{record}/edit'),
        ];
    }
}
