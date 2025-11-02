<?php

namespace App\Filament\Resources\Medecins\Tables;

use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Filament\Support\Icons\Heroicon;
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
                    ->formatStateUsing(fn($state): string => Str::limit($state, 20))
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
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
                TextColumn::make('est_responsable')
                    ->label('Responsable')
                    ->badge()
                    ->color(fn($record) => $record->est_responsable ? 'success' : 'gray')
                    ->formatStateUsing(fn($record) => $record->est_responsable ? 'OUI' : 'NON'),
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
