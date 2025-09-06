<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Filament\Resources\Applications\Widgets\ApplicationRegistrationsTrend;
use App\Filament\Resources\Applications\Widgets\ApplicationStatusDistribution;
use App\Filament\Resources\Applications\Widgets\ApplicationStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('deletedApplications')
                ->label('Deleted Applications')
                ->url(ApplicationResource::getUrl('deleted'))
                ->color('gray')
                ->visible(fn() => Auth::user()?->isSuperAdmin()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ApplicationStatsOverview::class,
            ApplicationRegistrationsTrend::class,
            ApplicationStatusDistribution::class,
        ];
    }
}
