<?php

namespace App\Filament\Resources\NotificationRendezVouses\Pages;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Dashboard\Concerns\HasFilters;
use Filament\Pages\Dashboard\Actions\FilterAction;
use App\Filament\Resources\NotificationRendezVouses\NotificationRendezVousResource;
use App\Models\NotificationRendezVous;

class ListNotificationRendezVouses extends ListRecords
{
    protected static string $resource = NotificationRendezVousResource::class;


    use HasFilters;

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Date de début')
                        ->native(false)
                        ->placeholder('Sélectionnez une date de début')
                        ->format('Y-m-d')
                        ->required()
                        ->minDate(now()->subYear())
                        ->maxDate(now()),

                    DatePicker::make('endDate')
                        ->label('Date de fin')
                        ->native(false)
                        ->placeholder('Sélectionnez une date de fin')
                        ->format('Y-m-d')
                        ->required()
                        ->after('startDate')
                        ->minDate(now()->subYear())
                        ->maxDate(now()),
                ]),
        ];
    }

    public function getTabs(): array
    {

        $thisWeek = NotificationRendezVous::query()->where('created_at', '>=', now()->subWeek())?->count();
        $thisMonth = NotificationRendezVous::query()->where('created_at', '>=', now()->subMonth())?->count();
        $thisYear = NotificationRendezVous::query()->where('created_at', '>=', now()->subYear())?->count();
        $thisQuarter = NotificationRendezVous::query()->where('created_at', '>=', now()->subQuarter())?->count();
        $thisSemester = NotificationRendezVous::query()->where('created_at', '>=', now()->subMonths(6))?->count();

        return [
            'all' => Tab::make(),
            'Cette Semaine' => Tab::make()
                ->badgeIcon('heroicon-m-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subWeek()))
                ->badgeColor($thisWeek > 10 ? 'success' : 'warning')
                ->badge($thisWeek),
            'Ce Mois' => Tab::make()
                ->badgeColor($thisMonth > 10 ? 'success' : 'warning')
                ->badgeIcon('heroicon-m-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subMonth()))
                ->badge($thisMonth),
            'Cette Année' => Tab::make()
                ->badgeColor($thisYear > 10 ? 'success' : 'warning')
                ->badgeIcon('heroicon-m-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subYear()))
                ->badge($thisYear),
            'Ce Trim' => Tab::make()
                ->badgeColor($thisQuarter > 10 ? 'success' : 'warning')
                ->badgeIcon('heroicon-m-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subQuarter()))
                ->badge($thisQuarter),
            'Ce Semestre' => Tab::make()
                ->badgeColor($thisSemester > 10 ? 'success' : 'warning')
                ->badgeIcon('heroicon-m-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subMonths(6)))
                ->badge($thisSemester),

        ];
    }
}
