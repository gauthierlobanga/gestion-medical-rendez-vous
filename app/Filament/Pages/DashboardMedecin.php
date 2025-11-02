<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use App\Filament\Resources\Users\Widgets\StatsDashOverview;
use App\Filament\Resources\Users\Widgets\StatsUserOverview;
use App\Filament\Resources\Users\Widgets\UserAnalyticsChart;
use App\Filament\Resources\Users\Widgets\ChartTabUserOverview;
use App\Filament\Resources\Users\Widgets\UserAnalyticsOverView;
use App\Filament\Resources\Users\Widgets\UserAnalyticsLineChart;
use App\Filament\Resources\Users\Widgets\ChartSecteurUserOverview;
use App\Filament\Resources\Users\Widgets\ChartUserPerMonthOverview;
use App\Filament\Resources\Users\Widgets\UserAnalyticsActifOverView;

class DashboardMedecin extends BaseDashboard
{
    use HasFiltersAction;
    use HasFiltersForm;

    protected static ?string $title = 'Médecin Dashboard';
    protected static string $routePath = 'medecin';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;
    protected static bool $isLazy = false;

    public function getColumns(): int | array
    {
        return [
            'sm' => 2,
            'md' => 3,
            'xl' => 4,
        ];
    }

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

    protected function getHeaderWidgets(): array
    {
        return [
            // StatsUserOverview::class,
            // StatsDashOverview::class,
            // UserAnalyticsOverView::class,
            // UserAnalyticsActifOverView::class,
            // ChartSecteurUserOverview::class,
            // ChartUserPerMonthOverview::class,
            // UserAnalyticsChart::class,
            // UserAnalyticsLineChart::class,
            // ChartTabUserOverview::class,
        ];
    }
}
