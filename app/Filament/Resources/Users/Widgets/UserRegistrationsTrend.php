<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserRegistrationsTrend extends ChartWidget
{
    protected ?string $heading = 'Registrations (last 30 days)';

    protected function getData(): array
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
            'datasets' => [[
                'label' => 'New Users',
                'data' => $counts,
                'borderColor' => '#3B82F6',
                'backgroundColor' => 'rgba(59,130,246,0.15)',
                'fill' => true,
                'tension' => 0.35,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
