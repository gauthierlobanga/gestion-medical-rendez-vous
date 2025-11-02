<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Laravel\Prompts\Support\Utils;

class ChartUserPerMonthOverview extends ChartWidget
{
    protected ?string $heading = '';
    protected static bool $isLazy = false;
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;


    protected function getData(): array
    {
        $data = $this->getUserPerMonth();

        return [
            'labels' => $data['months'],
            'datasets' => [
                [

                    'label' => 'Users created',
                    'data' => $data['usersPerMonth'],
                    'backgroundColor' => [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(99, 255, 132, 1)',
                        'rgba(235, 162, 54, 1)',
                        'rgba(86, 206, 255, 1)',
                        'rgba(192, 192, 75, 1)',
                        'rgba(200, 99, 200, 1)',
                        'rgba(100, 100, 255, 1)',

                    ],
                    'borderColor' => [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(99, 255, 132, 0.2)',
                        'rgba(235, 162, 54, 0.2)',
                        'rgba(86, 206, 255, 0.2)',
                        'rgba(192, 192, 75, 0.2)',
                        'rgba(200, 99, 200, 0.2)',
                        'rgba(100, 100, 255, 0.2)',
                    ],

                    'borderWidth' => 1,
                    'fill' => false,
                    'tension' => 0.4,
                    'datalabels' => [
                        'align' => 'start',
                        'anchor' => 'start',
                        'backgroundColor' => 'rgba(250, 55, 192, 0.8)',
                        'borderRadius' => 4,
                        'color' => 'white',
                        'font' => [
                            'weight' => 'bold',
                        ],
                        'formatter' => 'Math.round',
                        'padding' => 6,
                    ],
                ],
                // Ajoutez d'autres ensembles de données ici si nécessaire
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getUserPerMonth(): array
    {
        $currentYear = Carbon::now()->year;

        $userCounts = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $usersPerMonth = array_fill(0, 12, 0);
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
        }

        foreach ($userCounts as $userCount) {
            $usersPerMonth[$userCount->month - 1] = $userCount->count;
        }

        return [
            'usersPerMonth' => $usersPerMonth,
            'months' => $months,
        ];
    }


    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true, // Permet de contrôler la hauteur
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'aspectRatio' => 1.9,
            'layout' => [
                'padding' => [
                    'top' => 32,
                    'right' => 16,
                    'bottom' => 16,
                    'left' => 8,
                ],
            ],
            'elements' => [
                'line' => [
                    'fill' => false,
                    'tension' => 0.4,
                ],
            ],
            'scales' => [
                // Suppression des lignes horizontales et des labels
                'x' => [
                    'display' => true, // Désactive complètement l'axe des X
                    'grid' => [
                        'display' => false, // Supprime les lignes de la grille horizontale
                    ],
                ],
                'y' => [
                    'display' => false, // Désactive complètement l'axe des Y
                    'grid' => [
                        'display' => false, // Supprime les lignes de la grille verticale
                    ],
                ],
            ],
            'animation'  => [
                'duration'  => 1500,
                'easing'  => 'linear',
            ],
        ];
    }
}
