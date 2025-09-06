<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Application created')
            ->success()
            ->send();
    }
}
