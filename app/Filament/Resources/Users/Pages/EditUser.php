<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->authorize(function (Model $record): bool {
                    $authUser = Auth::user();

                    if (! $authUser) {
                        return false;
                    }

                    if ($authUser->id === $record->getKey()) {
                        return false;
                    }

                    if ($record->role === 'admin' && ! $authUser->isSuperAdmin()) {
                        return false;
                    }

                    return true;
                })
                ->visible(function (Model $record): bool {
                    $authUser = Auth::user();

                    if (! $authUser) {
                        return false;
                    }

                    // Never allow deleting your own account
                    if ($authUser->id === $record->getKey()) {
                        return false;
                    }

                    // Only superadmins can delete admin users
                    if ($record->role === 'admin' && ! $authUser->isSuperAdmin()) {
                        return false;
                    }

                    return true;
                })
                ->successNotificationTitle('User deleted')
                ->modalDescription('This will remove the user and cascade delete related applications and documents.'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Server-side guard: only superadmins can assign admin role
        if (($data['role'] ?? null) === 'admin' && ! Auth::user()?->isSuperAdmin()) {
            unset($data['role']);
        }

        // If role is admin, clear scholarship assignment
        if (($data['role'] ?? null) === 'admin') {
            $data['scholarship_id'] = null;
        }

        $record->update($data);

        return $record;
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'User updated successfully';
    }
}