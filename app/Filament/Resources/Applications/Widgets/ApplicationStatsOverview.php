<?php

namespace App\Filament\Resources\Applications\Widgets;

use App\Models\Application;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApplicationStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Applications', Application::count())
                ->description('All statuses')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),

            Stat::make('Submitted Today', Application::whereDate('submitted_at', now()->toDateString())->count())
                ->description('Since midnight')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),

            Stat::make('Approved', Application::where('status', 'approved')->count())
                ->description('Total approved')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Rejected', Application::where('status', 'rejected')->count())
                ->description('Total rejected')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}


