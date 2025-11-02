<?php

namespace App\Filament\Resources\Users\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\Users\UserResource;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Notifications\Actions\Action as ActionsAction1;
use Filament\Notifications\Actions\Action as ActionsAction2;
use Filament\Infolists\Components\Section as ComponentsSection;


class ChartTabUserOverview extends BaseWidget
{
    protected static ?int $sort = 5;
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserResource::getEloquentQuery())
            ->defaultPaginationPageOption(3)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->icon('heroicon-m-user-circle')
                    ->iconColor('primary')
                    ->searchable(),
                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Adresse email copiée')
                    ->copyMessageDuration(1500)
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('avatars')
                    ->conversion('thumb')
                    ->label('avatar')
                    ->circular(),
                TextColumn::make('roles.name')
                    ->label('Rôle')
                    ->badge()
                    ->icon('heroicon-m-finger-print')
                    ->iconColor('primary')
                    ->color(fn(string $state): string => match ($state) {
                        'Super Admin' => 'success',
                        'patient' => 'warning',
                        'personnel' => 'danger',
                        'medecin' => 'info',
                        'Medecin Chef Service' => 'info',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(?string $state) => match ($state) {
                        'Super Admin' => 'Super Admin',
                        'patient' => 'Patient',
                        'personnel' => 'Personnel',
                        'medecin' => 'Médecin',
                        'Medecin Chef Service' => 'Chef Service',
                        default => 'inconnu',
                    }),
                TextColumn::make('email_verified_at')
                    ->label('Email vérifié le')
                    ->dateTime()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->filters([
                SelectFilter::make('role_id')
                    ->label('Filter By Role')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->preload()
                    ->searchable()
                    ->indicator('Role'),
                Filter::make('created_at')->schema([
                    DatePicker::make('created_from')->native(false),
                    DatePicker::make('created_until')->native(false)
                ])->query(function (Builder $query, array $data) {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '=>', $date)
                        )
                        ->when(
                            $data['created_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                        );
                })->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['from'] ?? null) {
                        $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
                            ->removeField('from');
                    }

                    if ($data['until'] ?? null) {
                        $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['until'])->toFormattedDateString())
                            ->removeField('until');
                    }

                    return $indicators;
                })
            ]);
        // ->actions([
        //     ViewAction::make()
        //         ->button()
        //         ->color('success')
        //         ->label('Aperçu'),
        // ]);
    }
}
