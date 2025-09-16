<?php

namespace App\Filament\Resources\Patients\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.phone')
                    ->label('Téléphone')
                    ->searchable(),
                TextColumn::make('numero_securite_sociale')
                    ->label('N° Sécurité Sociale')
                    ->searchable(),
                TextColumn::make('age')
                    ->label('Âge')
                    ->sortable(),
                IconColumn::make('user.is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label('Patients actifs')
                    ->query(fn(Builder $query) => $query->whereHas('user', function ($q) {
                        $q->where('is_active', true);
                    })),
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
