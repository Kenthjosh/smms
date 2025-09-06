<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Application;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ApplicationStatusWidget extends ApexChartWidget
{
    protected static ?string $heading = 'Application Status Distribution';
    // protected static ?int $sort = 3;

    public function getDescription(): ?string
    {
        return 'Overview of application statuses across all scholarships';
    }

    protected function getOptions(): array
    {
        // Get status counts
        $statuses = Application::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses are represented (even if count is 0)
        $allStatuses = [
            'draft' => $statuses['draft'] ?? 0,
            'submitted' => $statuses['submitted'] ?? 0,
            'under_review' => $statuses['under_review'] ?? 0,
            'approved' => $statuses['approved'] ?? 0,
            'rejected' => $statuses['rejected'] ?? 0,
        ];

        return [
            'chart' => ['type' => 'donut', 'height' => 300],
            'labels' => ['Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected'],
            'series' => array_values($allStatuses),
            'colors' => ['#6B7280', '#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
            'legend' => ['position' => 'bottom'],
            'stroke' => ['width' => 0], // remove white borders
            'plotOptions' => [
                'pie' => [
                    'donut' => ['size' => '65%'],
                ],
            ],
        ];
    }
}