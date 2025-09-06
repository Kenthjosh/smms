<?php

namespace App\Filament\Resources\Scholarships\Widgets;

use App\Models\Application;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ScholarshipApplicationsByStatus extends ApexChartWidget
{
    protected static ?string $heading = 'Applications by Status';

    protected function getOptions(): array
    {
        $statuses = Application::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $allStatuses = [
            'draft' => $statuses['draft'] ?? 0,
            'submitted' => $statuses['submitted'] ?? 0,
            'under_review' => $statuses['under_review'] ?? 0,
            'approved' => $statuses['approved'] ?? 0,
            'rejected' => $statuses['rejected'] ?? 0,
        ];

        return [
            'chart' => ['type' => 'donut', 'height' => 280],
            'labels' => ['Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected'],
            'series' => array_values($allStatuses),
            'colors' => ['#6B7280', '#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
            'legend' => ['position' => 'bottom'],
            'stroke' => ['width' => 0],
            'plotOptions' => [
                'pie' => [
                    'donut' => ['size' => '60%'],
                ],
            ],
        ];
    }
}
