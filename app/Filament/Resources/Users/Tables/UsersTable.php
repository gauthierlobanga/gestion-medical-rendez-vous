<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('avatars')
                    ->conversion('thumb')
                    ->label('avatar')
                    ->circular(),
                TextColumn::make('roles.name')
                    ->icon('heroicon-m-finger-print')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Super Admin' => 'success',
                        'patient' => 'warning',
                        'personnel' => 'danger',
                        'medecin' => 'info',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'Super Admin' => 'Super Admin',
                        'patient' => 'Patient',
                        'personnel' => 'Personnel',
                        'medecin' => 'Medecin',
                    }),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('has_email_authentication')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('role_id')
                    ->label('Filter By Role')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->preload()
                    ->searchable()
                    ->indicator('Role'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create user')
                    ->url(route('filament.admin.resources.users.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->striped();
    }
}
