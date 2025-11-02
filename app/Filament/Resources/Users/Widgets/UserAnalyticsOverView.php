<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserAnalyticsOverView extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        return [
            Stat::make('Permissions pour Super Admin', function () {
                $superAdminRole = Role::where('name', 'Super Admin')->first();
                return $superAdminRole ? $superAdminRole->permissions->count() : 0;
            })
                ->description('Nombre total')
                ->icon('heroicon-s-key')
                ->chart(Permission::selectRaw('COUNT(*) as count')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where('role_id', Role::where('name', 'Super Admin')->value('id'))
                    ->groupBy('created_at')
                    ->pluck('count')->toArray())
                ->color('primary'),

            Stat::make('Permissions pour Medécin', function () {
                $superAdminRole = Role::where('name', 'medecin')->first();
                return $superAdminRole ? $superAdminRole->permissions->count() : 0;
            })
                ->description('Nombre total')
                ->icon('heroicon-s-key')
                ->chart(Permission::selectRaw('COUNT(*) as count')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where('role_id', Role::where('name', 'Super Admin')->value('id'))
                    ->groupBy('created_at')
                    ->pluck('count')->toArray())
                ->color('primary'),

            Stat::make('Permissions pour Chef de Service', function () {
                $superAdminRole = Role::where('name', 'Medecin Chef Service')->first();
                return $superAdminRole ? $superAdminRole->permissions->count() : 0;
            })
                ->description('Nombre total')
                ->icon('heroicon-s-key')
                ->chart(Permission::selectRaw('COUNT(*) as count')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where('role_id', Role::where('name', 'Super Admin')->value('id'))
                    ->groupBy('created_at')
                    ->pluck('count')->toArray())
                ->color('primary'),

            Stat::make('Permissions pour Patient', function () {
                $superAdminRole = Role::where('name', 'patient')->first();
                return $superAdminRole ? $superAdminRole->permissions->count() : 0;
            })
                ->description('Nombre total')
                ->icon('heroicon-s-key')
                ->chart(Permission::selectRaw('COUNT(*) as count')
                    ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where('role_id', Role::where('name', 'Super Admin')->value('id'))
                    ->groupBy('created_at')
                    ->pluck('count')->toArray())
                ->color('primary'),
        ];
    }
}
