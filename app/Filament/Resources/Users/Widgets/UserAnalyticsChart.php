<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UserAnalyticsChart extends ChartWidget
{
    protected ?string $heading = '';

    // protected static ?string $pollingInterval = '10s';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 3;


    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
            'aspectRatio' => 1.9, // Réduit la taille (1.5 = moins de hauteur)
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'datalabels' => [ // Configuration de chartjs-plugin-datalabels
                    'color' => '#fff', // Couleur du texte
                    'font' => [
                        'size' => 14,
                        'weight' => 'bold',
                    ],
                    'formatter' => function ($value, $context) {
                        // Calcul du pourcentage
                        $total = array_sum($context['dataset']['data']);
                        $percentage = ($value / $total) * 100;
                        return round($percentage, 1) . '%'; // Arrondir à 1 décimale
                    },
                ],
            ],
            'scales' => [
                // Suppression des lignes horizontales et des labels
                'x' => [
                    'display' => false, // Désactive complètement l'axe des X
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
            'animation' => [
                'duration' => 1500,
                'easing' => 'linear',
            ],
            'layout' => [
                'padding' => [
                    'top' => 10,
                    'bottom' => 10,
                    'left' => 10,
                    'right' => 10,
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $data = Trend::model(User::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
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
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }


    public function getDescription(): ?string
    {
        return '';
    }
}
