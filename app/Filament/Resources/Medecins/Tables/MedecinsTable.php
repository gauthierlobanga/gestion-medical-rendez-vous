<?php

namespace App\Filament\Resources\Medecins\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class MedecinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service.nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('specialite')
                    ->label('Spécialité')
                    ->searchable(),
                TextColumn::make('numero_ordre')
                    ->label('N° Ordre')
                    ->searchable(),
                TextColumn::make('tarif_consultation')
                    ->label('Tarif')
                    ->money('EUR')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('service')
                    ->relationship('service', 'nom')
                    ->searchable()
                    ->preload(),
                Filter::make('is_active')
                    ->label('Médecins actifs')
                    ->toggle()
                    ->query(fn(Builder $query) => $query->where('is_active', true)),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
