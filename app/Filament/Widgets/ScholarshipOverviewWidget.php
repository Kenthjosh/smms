<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\User;

class ScholarshipOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $scholarships = Scholarship::where('is_active', true)->get();
        $stats = [];

        foreach ($scholarships as $scholarship) {
            $totalApplications = Application::where('scholarship_id', $scholarship->id)->count();
            $pendingApplications = Application::where('scholarship_id', $scholarship->id)
                ->whereIn('status', ['submitted', 'under_review'])
                ->count();
            $approvedApplications = Application::where('scholarship_id', $scholarship->id)
                ->where('status', 'approved')
                ->count();
            $rejectedApplications = Application::where('scholarship_id', $scholarship->id)
                ->where('status', 'rejected')
                ->count();

            // Create description with breakdown
            $description = "Pending: {$pendingApplications} | Approved: {$approvedApplications}";
            if ($rejectedApplications > 0) {
                $description .= " | Rejected: {$rejectedApplications}";
            }

            $stats[] = Stat::make($scholarship->name, $totalApplications)
                ->description($description)
                ->descriptionIcon('heroicon-m-document-text')
                ->color($this->getScholarshipColor($scholarship->type))
                ->url(route('filament.admin.resources.applications.index', [
                    'tableFilters[scholarship][values][0]' => $scholarship->id
                ]));
        }

        return $stats;
    }

    /**
     * Get color based on scholarship type
     */
    private function getScholarshipColor(string $type): string
    {
        return match ($type) {
            'merit' => 'blue',
            'sports' => 'green',
            'need-based' => 'orange',
            'indigenous' => 'purple',
            default => 'gray',
        };
    }
}
