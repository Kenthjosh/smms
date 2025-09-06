<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDeletedUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Deleted Users';

    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()?->onlyTrashed();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToUsers')
                ->label('Back to Users')
                ->url(UserResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
