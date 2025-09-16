<?php

namespace App\Filament\Resources\Personnels;

use App\Filament\Resources\Personnels\Pages\CreatePersonnel;
use App\Filament\Resources\Personnels\Pages\EditPersonnel;
use App\Filament\Resources\Personnels\Pages\ListPersonnels;
use App\Filament\Resources\Personnels\Schemas\PersonnelForm;
use App\Filament\Resources\Personnels\Tables\PersonnelsTable;
use App\Models\Personnel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PersonnelResource extends Resource
{
    protected static ?string $model = Personnel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'poste';

    public static function form(Schema $schema): Schema
    {
        return PersonnelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PersonnelsTable::configure($table);
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
            'index' => ListPersonnels::route('/'),
            'create' => CreatePersonnel::route('/create'),
            'edit' => EditPersonnel::route('/{record}/edit'),
        ];
    }
}
