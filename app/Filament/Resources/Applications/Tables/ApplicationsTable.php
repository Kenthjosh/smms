<?php

namespace App\Filament\Resources\Applications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->deferFilters(false)
            ->columns([
                TextColumn::make('user.name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('scholarship.name')
                    ->label('Scholarship')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'under_review' => 'warning',
                        'submitted' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('documents_count')
                    ->label('Documents')
                    ->counts('documents')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'under_review' => 'Under review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('scholarship_id')
                    ->label('Scholarship')
                    ->relationship('scholarship', 'name'),
                TernaryFilter::make('submitted')
                    ->label('Submitted')
                    ->nullable()
                    ->queries(
                        true: fn(Builder $q) => $q->whereNotNull('submitted_at'),
                        false: fn(Builder $q) => $q->whereNull('submitted_at'),
                        blank: fn(Builder $q) => $q,
                    ),
                Filter::make('created_between')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $q, array $data): Builder {
                        return $q
                            ->when($data['created_from'] ?? null, fn($qq, $date) => $qq->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn($qq, $date) => $qq->whereDate('created_at', '<=', $date));
                    }),
                TrashedFilter::make(),
            ])
            ->recordAction('view')
            ->recordActions([
                RestoreAction::make()
                    ->visible(fn($record) => method_exists($record, 'trashed') && $record->trashed()),
                ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently delete application?')
                    ->modalDescription('This cannot be undone. Documents will be removed.')
                    ->authorize(fn() => Auth::user()?->isSuperAdmin() === true)
                    ->visible(fn($record) => method_exists($record, 'trashed') && $record->trashed()),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn() => Auth::user()?->isSuperAdmin() === true),
                    \Filament\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->authorize(fn() => Auth::user()?->isSuperAdmin() === true),
                ]),
            ]);
    }
}