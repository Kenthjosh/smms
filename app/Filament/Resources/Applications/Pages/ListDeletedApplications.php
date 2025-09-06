<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDeletedApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected static ?string $title = 'Deleted Applications';

    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()?->onlyTrashed();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToApplications')
                ->label('Back to Applications')
                ->url(ApplicationResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}


