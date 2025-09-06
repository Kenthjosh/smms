<?php

namespace App\Filament\Resources\Scholarships\Pages;

use App\Filament\Resources\Scholarships\ScholarshipResource;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListDeletedScholarships extends ListRecords
{
    protected static string $resource = ScholarshipResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Deleted Scholarships';
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->onlyTrashed();
    }

    protected function getTableRecordActions(): array
    {
        return [
            RestoreAction::make()
                ->successNotificationTitle('Scholarship restored'),
            ForceDeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Permanently delete scholarship?')
                ->modalDescription('This cannot be undone. Related applications may be removed unless archived elsewhere.')
                ->authorize(fn() => Auth::user()?->isSuperAdmin() === true),
        ];
    }
}
