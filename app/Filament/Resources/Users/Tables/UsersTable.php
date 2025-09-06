<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): void {
                $currentUserId = Auth::id();

                if ($currentUserId !== null) {
                    $query->whereKeyNot($currentUserId);
                }
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('scholarship.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('role'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordAction('view')
            ->recordActions([
                RestoreAction::make()
                    ->visible(fn($record) => method_exists($record, 'trashed') && $record->trashed())
                    ->successNotificationTitle('User restored'),
                ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently delete user?')
                    ->modalDescription('This cannot be undone. Applications and documents will be removed.')
                    ->authorize(fn() => Auth::user()?->isSuperAdmin() === true)
                    ->visible(fn($record) => method_exists($record, 'trashed') && $record->trashed()),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn() => Auth::user()?->isSuperAdmin() === true)
                        ->successNotificationTitle('Selected users deleted')
                        ->modalDescription('This will remove the selected users and cascade delete their applications and documents.'),
                ]),
            ]);
    }
}
