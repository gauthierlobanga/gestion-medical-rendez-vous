<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ChartSecteurUserOverview extends ChartWidget
{
    protected ?string $heading = '';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Trend::model(User::class)
            ->between(
                start: now()->subMonths(10),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Utilisateurs',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => [
                        'rgba(255, 100, 132, 0.9)',
                        'rgba(255, 159, 64, 0.9)',
                        'rgba(255, 205, 86, 0.9)',
                        'rgba(75, 192, 192, 0.9)',
                        'rgba(54, 162, 235, 0.9)',
                        'rgba(153, 102, 255, 0.9)',
                        'rgba(201, 203, 207, 0.9)'
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 0.4)',
                        'rgba(255, 159, 64, 0.4)',
                        'rgba(255, 205, 86, 0.4)',
                        'rgba(75, 192, 192, 0.4)',
                        'rgba(54, 162, 235, 0.4)',
                        'rgba(153, 102, 255, 0.4)',
                        'rgba(201, 203, 207, 0.4)'
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
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
