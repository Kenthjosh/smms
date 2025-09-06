<?php

namespace App\Filament\Resources\Applications\Widgets;

use App\Models\Application;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ApplicationStatusDistribution extends ApexChartWidget
{
    protected static ?string $heading = 'Application Status';

    protected function getOptions(): array
    {
        $counts = Application::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status')->toArray();

        return [
            'chart' => ['type' => 'donut', 'height' => 260],
            'labels' => ['Draft', 'Submitted', 'Under review', 'Approved', 'Rejected'],
            'series' => [
                (int) ($counts['draft'] ?? 0),
                (int) ($counts['submitted'] ?? 0),
                (int) ($counts['under_review'] ?? 0),
                (int) ($counts['approved'] ?? 0),
                (int) ($counts['rejected'] ?? 0),
            ],
            'colors' => ['#9CA3AF', '#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
            'stroke' => ['width' => 0],
            'plotOptions' => [
                'pie' => [
                    'donut' => ['size' => '65%'],
                ],
            ],
            'legend' => ['position' => 'bottom'],
        ];
    }
}


