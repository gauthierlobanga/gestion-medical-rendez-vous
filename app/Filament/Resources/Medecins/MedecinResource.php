<?php

namespace App\Filament\Resources\Medecins;

use App\Filament\Resources\Medecins\Pages\CreateMedecin;
use App\Filament\Resources\Medecins\Pages\EditMedecin;
use App\Filament\Resources\Medecins\Pages\ListMedecins;
use App\Filament\Resources\Medecins\Schemas\MedecinForm;
use App\Filament\Resources\Medecins\Tables\MedecinsTable;
use App\Models\Medecin;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MedecinResource extends Resource
{
    protected static ?string $model = Medecin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;
    protected static string|UnitEnum|null $navigationGroup = 'Gestion du personnel';
    protected static ?int $navigationSort = 7;
    protected static ?string $recordTitleAttribute = 'numero_ordre';

    public static function form(Schema $schema): Schema
    {
        return MedecinForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedecinsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DisponibilitesRelationManager::class,
            RelationManagers\RendezVousRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedecins::route('/'),
            'create' => CreateMedecin::route('/create'),
            'edit' => EditMedecin::route('/{record}/edit'),
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
