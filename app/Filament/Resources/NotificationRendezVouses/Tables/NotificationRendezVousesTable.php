<?php

namespace App\Filament\Resources\NotificationRendezVouses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotificationRendezVousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('rendezvous.id')
                //     ->searchable(),
                TextColumn::make('type_notification')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'confirmation' => 'success',
                        'rappel_24h' => 'warning',
                        'rappel_1h' => 'warning',
                        'annulation' => 'danger',
                        'modification' => 'info',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'confirmation' => 'Confirmation',
                        'rappel_24h' => 'Rappel 24h',
                        'rappel_1h' => 'Rappel 1h',
                        'annulation' => 'Annulation',
                        'modification' => 'Modification',
                    }),
                TextColumn::make('destinataire')
                    ->searchable(),
                TextColumn::make('sujet')
                    ->searchable(),
                TextColumn::make('date_envoi')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('statut')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'envoye' => 'success',
                        'en_attente' => 'warning',
                        'erreur' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'envoye' => 'Envoyé',
                        'en_attente' => 'En attente',
                        'erreur' => 'Erreur',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
