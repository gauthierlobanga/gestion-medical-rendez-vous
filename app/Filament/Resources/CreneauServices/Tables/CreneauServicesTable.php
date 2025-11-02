<?php

namespace App\Filament\Resources\CreneauServices\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class CreneauServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jour_semaine')
                    ->icon(Heroicon::CalendarDays)
                    ->iconColor('primary')
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
                    ->icon(Heroicon::Clock)
                    ->iconColor('primary')
                    ->label('Début')
                    ->time('H:i'),
                TextColumn::make('heure_fin')
                    ->icon(Heroicon::Clock)
                    ->iconColor('primary')
                    ->label('Fin')
                    ->time('H:i'),
                TextColumn::make('nombre_creneaux')
                    ->label('Nombre'),
                TextColumn::make('duree_creneau')
                    ->label('Durée (min)'),
            ])
            ->filters([
                SelectFilter::make('service')
                    ->relationship('service', 'nom')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jour_semaine')
                    ->searchable()
                    ->options([
                        1 => 'Lundi',
                        2 => 'Mardi',
                        3 => 'Mercredi',
                        4 => 'Jeudi',
                        5 => 'Vendredi',
                        6 => 'Samedi',
                        7 => 'Dimanche',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ViewAction::make(),
                ])->label('More')
                    ->icon(Heroicon::EllipsisVertical)
                    ->size(Size::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
