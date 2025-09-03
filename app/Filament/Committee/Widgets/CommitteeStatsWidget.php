<?php

namespace App\Filament\Committee\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CommitteeStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Super admin can see all data, committee members see their scholarship only
        if ($user->isSuperAdmin()) {
            return $this->getSuperAdminStats();
        }

        $scholarshipId = $user->scholarship_id;
        $scholarshipName = $user->scholarship->name ?? 'Unknown';

        return [
            Stat::make(
                'Total Applications',
                Application::where('scholarship_id', $scholarshipId)->count()
            )
                ->description("All applications for {$scholarshipName}")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('blue'),

            Stat::make(
                'Pending Review',
                Application::where('scholarship_id', $scholarshipId)
                    ->whereIn('status', ['submitted', 'under_review'])
                    ->count()
            )
                ->description('Applications awaiting review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(
                'Approved',
                Application::where('scholarship_id', $scholarshipId)
                    ->where('status', 'approved')
                    ->count()
            )
                ->description('Applications approved')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(
                'Active Students',
                User::where('scholarship_id', $scholarshipId)
                    ->where('role', 'student')
                    ->count()
            )
                ->description('Students in your scholarship program')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }

    private function getSuperAdminStats(): array
    {
        return [
            Stat::make('Total Applications', Application::count())
                ->description('Applications across all scholarships')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('blue'),

            Stat::make('All Scholarships', \App\Models\Scholarship::where('is_active', true)->count())
                ->description('Active scholarship programs')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Total Students', User::where('role', 'student')->count())
                ->description('Students across all programs')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Committee Members', User::where('role', 'committee')->count())
                ->description('Active committee members')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }
}
