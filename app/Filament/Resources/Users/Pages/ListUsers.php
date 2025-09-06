<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Widgets\UserRegistrationsTrend;
use App\Filament\Resources\Users\Widgets\UserRoleDistribution;
use App\Filament\Resources\Users\Widgets\UserStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('deletedUsers')
                ->label('Deleted Users')
                ->url(UserResource::getUrl('deleted'))
                ->color('gray')
                ->visible(fn() => Auth::user()?->isSuperAdmin()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsOverview::class,
            UserRegistrationsTrend::class,
            UserRoleDistribution::class,
        ];
    }
}
