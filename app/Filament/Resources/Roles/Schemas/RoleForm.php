<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\CheckboxColumn;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')->required(),
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->columns(4)
                            ->bulkToggleable()
                            ->noSearchResultsMessage('Aucun rôle trouvé')
                            ->searchable(),
                    ])->columnSpanFull()
            ]);
    }
}
