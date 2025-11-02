<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Tables\Table;
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
use Illuminate\Database\Eloquent\Builder;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('responsable.user.name')
                    ->label('Responsable')
                    ->searchable(),
                TextColumn::make('duree_rendezvous')
                    ->label('Durée RDV (min)')
                    ->sortable(),
                TextColumn::make('medecins_count')
                    ->label('Médecins')
                    ->counts('medecins'),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label('Services actifs')
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
                    ->button()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
