<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserRoleDistribution extends ChartWidget
{
    protected ?string $heading = 'User Roles';

    protected function getData(): array
    {
        $counts = User::selectRaw('role, COUNT(*) as c')->groupBy('role')->pluck('c', 'role')->toArray();

        return [
            'datasets' => [[
                'label' => 'Users',
                'data' => [
                    $counts['admin'] ?? 0,
                    $counts['committee'] ?? 0,
                    $counts['student'] ?? 0,
                ],
                'backgroundColor' => ['#EF4444', '#F59E0B', '#10B981'],
                'borderWidth' => 0,
                'cutout' => '70%',
            ]],
            'labels' => ['Admins', 'Committee', 'Students'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
