<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsDashOverview extends BaseWidget
{
    protected static bool $isLazy = false;
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $superAdminRole = User::role('Super Admin')->count();
        $patientRole = User::role('patient')->count();
        $medecinRole = User::role('medecin')->count();
        $chefServiceRole = User::role('Medecin Chef Service')->count();
        $enseignantRole = User::role('personnel')->count();
        $enseignantRole = User::role('secretaire')->count();

        return [

            Stat::make('Patient', $patientRole)
                ->icon('heroicon-s-shield-check')
                ->description('Nombre de Patients')
                ->descriptionIcon($patientRole > 2 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($patientRole > 2 ? 'warning' : 'danger'),

            Stat::make('Super Admin', $superAdminRole)
                ->icon('heroicon-s-shield-check')
                ->description('Nombre des Super Admins')
                ->descriptionIcon($superAdminRole > 2 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($superAdminRole > 2 ? 'warning' : 'danger'),

            Stat::make('Tous les Médecins', abbreviateNumberFormat($medecinRole, 0))
                ->icon('heroicon-s-academic-cap')
                ->description('Enregistré en ' . now()->year)
                ->descriptionIcon($medecinRole > 10 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($medecinRole > 10 ? 'success' : 'danger'),

            Stat::make('Tous les Chef de Services', $chefServiceRole)
                ->icon('heroicon-s-book-open')
                ->description('Nombre de Chef de Services')
                ->descriptionIcon($chefServiceRole > 5 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($chefServiceRole > 5 ? 'success' : 'danger'),
        ];
    }
}
