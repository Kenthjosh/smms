<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class UserRoleDistribution extends ApexChartWidget
{
    protected static ?string $heading = 'User Roles';

    protected function getOptions(): array
    {
        $counts = User::selectRaw('role, COUNT(*) as c')->groupBy('role')->pluck('c', 'role')->toArray();

        return [
            'chart' => ['type' => 'donut', 'height' => 300],
            'labels' => ['Admins', 'Committee', 'Students'],
            'series' => [
                (int) ($counts['admin'] ?? 0),
                (int) ($counts['committee'] ?? 0),
                (int) ($counts['student'] ?? 0),
            ],
            'colors' => ['#EF4444', '#F59E0B', '#10B981'],
            'stroke' => ['width' => 0], // remove white borders
            'plotOptions' => [
                'pie' => [
                    'donut' => ['size' => '70%'],
                ],
            ],
            'legend' => ['position' => 'bottom'],
        ];
    }
}
