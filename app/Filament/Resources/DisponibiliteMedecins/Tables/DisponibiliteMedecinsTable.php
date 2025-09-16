<?php

namespace App\Filament\Resources\DisponibiliteMedecins\Tables;

use App\Models\Medecin;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class DisponibiliteMedecinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('medecin.user.name')
                    ->label('Médecin')
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
                IconColumn::make('est_exception')
                    ->label('Exception')
                    ->boolean(),
                TextColumn::make('date_specifique')
                    ->label('Date spécifique')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('medecin')
                    ->options(
                        Medecin::with('user')
                            ->get()
                            ->mapWithKeys(fn($medecin) => [
                                $medecin->id => $medecin->user->name
                            ])
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jour_semaine')
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
