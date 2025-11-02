<?php

namespace App\Filament\Resources\Services\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CreneauxRelationManager extends RelationManager
{
    protected static string $relationship = 'creneaux';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('jour_semaine')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jour_semaine')
            ->columns([
                TextColumn::make('service.nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jour_semaine')
                    ->label('Jour')
                    ->formatStateUsing(fn($state) => [
                        1 => 'Lundi',
                        2 => 'Mardi',
                        3 => 'Mercredi',
                        4 => 'Jeudi',
                        5 => 'Vendredi',
                        6 => 'Samedi',
                        7 => 'Dimanche',
                    ][$state] ?? 'Inconnu')
                    ->sortable(),
                TextColumn::make('heure_debut')
                    ->label('Début')
                    ->time('H:i'),
                TextColumn::make('heure_fin')
                    ->label('Fin')
                    ->time('H:i'),
                TextColumn::make('nombre_creneaux')
                    ->label('Nombre'),
                TextColumn::make('duree_creneau')
                    ->label('Durée (min)'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
