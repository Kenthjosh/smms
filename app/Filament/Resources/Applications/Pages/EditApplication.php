<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Automatically set submitted_at when moving to submitted
        if (($data['status'] ?? null) === 'submitted' && empty($this->record->submitted_at) && empty($data['submitted_at'])) {
            $data['submitted_at'] = now();
        }

        // Require committee_notes when approving or rejecting
        if (in_array($data['status'] ?? null, ['approved', 'rejected'], true)) {
            if (empty($data['committee_notes'])) {
                throw ValidationException::withMessages([
                    'committee_notes' => 'A short review note is required when approving or rejecting.',
                ]);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Application updated')
            ->success()
            ->send();
    }
}
