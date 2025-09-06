<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All roles')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Students', User::where('role', 'student')->count())
                ->description('Registered students')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Committee', User::where('role', 'committee')->count())
                ->description('Committee members')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),

            Stat::make('Admins', User::where('role', 'admin')->count())
                ->description('Administrators')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),
        ];
    }
}
