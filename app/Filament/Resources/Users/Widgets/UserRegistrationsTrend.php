<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class UserRegistrationsTrend extends ApexChartWidget
{
    protected static ?string $heading = 'Registrations (last 30 days)';

    protected function getOptions(): array
    {
        $raw = User::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('created_at', '>=', now()->subDays(30))
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
                'name' => 'New Users',
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
            'colors' => ['#3B82F6'],
        ];
    }
}