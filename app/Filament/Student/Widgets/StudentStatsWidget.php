<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class StudentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $application = Application::where('user_id', $user->id)->first();
        $scholarship = $user->scholarship;

        return [
            Stat::make(
                'Application Status',
                $application ? ucfirst(str_replace('_', ' ', $application->status)) : 'Not Started'
            )
                ->description($application ? 'Current status of your application' : 'Start your application today')
                ->descriptionIcon($this->getStatusIcon($application?->status))
                ->color($this->getStatusColor($application?->status)),

            Stat::make(
                'Documents Uploaded',
                $application ? $application->documents()->count() : 0
            )
                ->description('Required documents submitted')
                ->descriptionIcon('heroicon-m-document-arrow-up')
                ->color('blue'),

            Stat::make(
                'Scholarship Program',
                $scholarship?->name ?? 'Not Assigned'
            )
                ->description('Your scholarship category')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make(
                'Application Deadline',
                $scholarship?->application_deadline ? $scholarship->application_deadline->format('M d, Y') : 'TBD'
            )
                ->description($scholarship && $scholarship->application_deadline ?
                    ($scholarship->application_deadline->isPast() ? 'Deadline passed' :
                        'Days remaining: ' . now()->diffInDays($scholarship->application_deadline)) :
                    'No deadline set')
                ->descriptionIcon('heroicon-m-calendar')
                ->color($scholarship && $scholarship->application_deadline && $scholarship->application_deadline->isPast() ? 'danger' : 'gray'),
        ];
    }

    private function getStatusIcon(?string $status): string
    {
        return match ($status) {
            'draft' => 'heroicon-m-pencil',
            'submitted' => 'heroicon-m-paper-airplane',
            'under_review' => 'heroicon-m-eye',
            'approved' => 'heroicon-m-check-circle',
            'rejected' => 'heroicon-m-x-circle',
            default => 'heroicon-m-question-mark-circle',
        };
    }

    private function getStatusColor(?string $status): string
    {
        return match ($status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'under_review' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }
}