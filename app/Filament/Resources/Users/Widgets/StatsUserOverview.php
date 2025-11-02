<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StatsUserOverview extends BaseWidget
{
    protected static bool $isLazy = false;
    protected static ?int $sort = 1;


    protected function getStats(): array
    {
        $allUsers = User::query()->count();

        return [

            Stat::make('Nombre Total d\'Utilisateurs', abbreviateNumberFormat(value: $allUsers, precision: 0))
                ->icon('heroicon-s-user-group')
                ->description('Total des utilisateurs enregistrés')
                ->descriptionIcon($allUsers > 10 ? 'heroicon-s-user-group' : 'heroicon-s-user', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($allUsers > 10 ? 'primary' : 'danger'),

            Stat::make('Utilisateurs Actifs', abbreviateNumberFormat(value: User::query()->where('is_active', true)->count(), precision: 0))
                ->description('Utilisateurs avec une vérification d\'email')
                ->icon('heroicon-s-user-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Utilisateurs Inactifs', abbreviateNumberFormat(value: User::query()->where('is_active', false)->count(), precision: 0))
                ->description('Utilisateurs sans vérification d\'email')
                ->icon('heroicon-s-user')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),

            Stat::make('Utilisateurs Crée récemment', value: abbreviateNumberFormat(value: User::where('created_at', '>=', now()
                ->subMonth())
                ->count(), precision: 0))
                ->description('Utilisateurs ajoutés au cours du dernier mois')
                ->icon('heroicon-s-clock')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make('Utilisateurs Avec Rôle', abbreviateNumberFormat(value: User::has('roles')->count(), precision: 0))
                ->description('Utilisateurs ayant au moins un rôle assigné')
                ->icon('heroicon-s-check-circle')
                ->chart(User::has('roles')
                    ->selectRaw('COUNT(*) as count')
                    ->groupByRaw('DATE(created_at)')
                    ->pluck('count')->toArray())
                ->color('success'),

            Stat::make('Utilisateurs Sans Rôle', abbreviateNumberFormat(value: User::doesntHave('roles')->count(), precision: 0))
                ->description('Utilisateurs n\'ayant aucun rôle assigné')
                ->icon('heroicon-s-user-circle')
                ->chart(User::doesntHave('roles')
                    ->selectRaw('COUNT(*) as count')
                    ->groupByRaw('DATE(created_at)')
                    ->pluck('count')->toArray())
                ->color('warning'),

        ];
    }
}
