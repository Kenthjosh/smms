<?php

namespace App\Filament\Resources\Applications\Widgets;

use App\Models\Application;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ApplicationRegistrationsTrend extends ApexChartWidget
{
    protected static ?string $heading = 'Submissions (last 30 days)';

    protected function getOptions(): array
    {
        $raw = Application::selectRaw('DATE(submitted_at) as d, COUNT(*) as c')
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', now()->subDays(30))
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $labels = [];
        $counts = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $key = $date->toDateString();
            $labels[] = $date->format('M j');
            $counts[] = $raw[$key] ?? 0;
        }

        return [
            'chart' => ['type' => 'line', 'height' => 300, 'toolbar' => ['show' => false]],
            'series' => [[
                'name' => 'Submitted',
                'data' => $counts,
            ]],
            'xaxis' => [
                'categories' => $labels,
                'tickPlacement' => 'on',
                'labels' => [
                    'rotate' => -35,
                    'trim' => true,
                    'hideOverlappingLabels' => true,
                    'showDuplicates' => false,
                ],
            ],
            'dataLabels' => ['enabled' => false],
            'stroke' => ['curve' => 'smooth'],
            'colors' => ['#10B981'],
        ];
    }
}


