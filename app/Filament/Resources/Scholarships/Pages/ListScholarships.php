<?php

namespace App\Filament\Resources\Scholarships\Pages;

use App\Filament\Resources\Scholarships\ScholarshipResource;
use App\Filament\Resources\Scholarships\Widgets\ScholarshipApplicationsTrend;
use App\Filament\Resources\Scholarships\Widgets\ScholarshipApplicationsByStatus;
use App\Filament\Resources\Scholarships\Widgets\ScholarshipStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListScholarships extends ListRecords
{
    protected static string $resource = ScholarshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('deletedScholarships')
                ->label('Deleted Scholarships')
                ->url(ScholarshipResource::getUrl('deleted'))
                ->color('gray')
                ->visible(fn() => Auth::user()?->isSuperAdmin()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ScholarshipStatsOverview::class,
            ScholarshipApplicationsTrend::class,
            ScholarshipApplicationsByStatus::class,
        ];
    }
}
