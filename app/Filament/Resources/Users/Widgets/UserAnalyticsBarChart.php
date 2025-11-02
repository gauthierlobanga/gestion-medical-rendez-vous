<?php

namespace App\Filament\Resources\Users\Widgets;

use Filament\Widgets\ChartWidget;

class UserAnalyticsBarChart extends ChartWidget
{
    protected ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
