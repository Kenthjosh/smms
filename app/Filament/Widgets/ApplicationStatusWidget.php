<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Application;

class ApplicationStatusWidget extends ChartWidget
{
    protected ?string $heading = 'Application Status Distribution';
    // protected static ?int $sort = 3;

    public function getDescription(): ?string
    {
        return 'Overview of application statuses across all scholarships';
    }

    protected function getData(): array
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
            'datasets' => [
                [
                    'label' => 'Applications',
                    'data' => array_values($allStatuses),
                    'backgroundColor' => [
                        '#6B7280', // Draft - Gray
                        '#3B82F6', // Submitted - Blue
                        '#F59E0B', // Under Review - Yellow/Orange
                        '#10B981', // Approved - Green
                        '#EF4444', // Rejected - Red
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => [
                'Draft',
                'Submitted',
                'Under Review',
                'Approved',
                'Rejected'
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
