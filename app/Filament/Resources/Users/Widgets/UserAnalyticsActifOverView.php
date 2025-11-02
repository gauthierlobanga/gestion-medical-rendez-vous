<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserAnalyticsActifOverView extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;


    protected function getStats(): array
    {

        return [
            Stat::make('Utilisateurs avec e-mail différent de @gmail.com', abbreviateNumberFormat(value: User::where('email', 'not like', '%@gmail.com')
                ->count(), precision: 0))
                ->description('Nombre des utilisateurs')
                ->icon('heroicon-s-envelope')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make('Utilisateurs avec e-mail @gmail.com', abbreviateNumberFormat(value: User::where('email', 'like', '%@gmail.com')
                ->count(), precision: 0))
                ->description('Nombre des utilisateurs')
                ->icon('heroicon-s-envelope')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make('Utilisateurs Supprimés', abbreviateNumberFormat(value: User::onlyTrashed()
                ->count(), precision: 0))
                ->description('Utilisateurs supprimés')
                ->icon('heroicon-s-trash')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),

            Stat::make('Utilisateurs non supprimés', abbreviateNumberFormat(value: User::whereNull('deleted_at')->count(), precision: 0))
                ->description('Utilisateurs non supprimés')
                ->icon('heroicon-s-user')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
