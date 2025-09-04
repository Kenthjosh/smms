<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Application;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Document;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Applications', Application::count())
                ->description('Applications across all scholarships')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('blue'),
            // ->chart($this->getApplicationTrend()),

            Stat::make('Active Scholarships', Scholarship::where('is_active', true)->count())
                ->description('Currently available programs')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Total Students', User::where('role', 'student')->count())
                ->description('Registered student applicants')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Committee Members', User::where('role', 'committee')->count())
                ->description('Active reviewers across programs')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }

    /**
     * Get application trend data for the last 7 days
     */
    private function getApplicationTrend(): array
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Application::whereDate('created_at', $date)->count();
            $trend[] = $count;
        }
        return $trend;
    }
}