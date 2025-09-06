<?php

namespace App\Filament\Resources\Scholarships\Widgets;

use App\Models\Scholarship;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ScholarshipStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Scholarships', Scholarship::count())
                ->description('All programs')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Active', Scholarship::where('is_active', true)->count())
                ->description('Currently accepting')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Upcoming Deadlines', Scholarship::whereDate('application_deadline', '>=', now()->toDateString())->count())
                ->description('From today onwards')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
        ];
    }
}
