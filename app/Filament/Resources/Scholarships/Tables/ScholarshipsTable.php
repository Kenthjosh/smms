<?php

namespace App\Filament\Resources\Scholarships\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ScholarshipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->deferFilters(false)
            ->columns([
                TextColumn::make('name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('application_deadline')
                    ->date()
                    ->sortable(),
                TextColumn::make('applications_count')
                    ->label('Applications')
                    ->counts('applications')
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
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Filter::make('deadline_window')
                    ->label('Deadline')
                    ->indicateUsing(function (array $data): ?Indicator {
                        return match ($data['window'] ?? null) {
                            'upcoming' => Indicator::make('Upcoming deadlines'),
                            'past' => Indicator::make('Past deadlines'),
                            default => null,
                        };
                    })
                    ->form([
                        \Filament\Forms\Components\Select::make('window')
                            ->options([
                                'upcoming' => 'Upcoming',
                                'past' => 'Past',
                            ])
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['window'] ?? null) {
                            'upcoming' => $query->whereDate('application_deadline', '>=', now()->toDateString()),
                            'past' => $query->whereDate('application_deadline', '<', now()->toDateString()),
                            default => $query,
                        };
                    }),
                TrashedFilter::make(),
            ])
            ->recordAction('view')
            ->recordActions([
                RestoreAction::make()
                    ->visible(fn($record) => method_exists($record, 'trashed') && $record->trashed())
                    ->successNotificationTitle('Scholarship restored'),
                ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently delete scholarship?')
                    ->modalDescription('This cannot be undone. Applications will be removed unless archived elsewhere.')
                    ->authorize(fn() => Auth::user()?->isSuperAdmin() === true)
                    ->visible(fn($record) => method_exists($record, 'trashed') && $record->trashed()),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn() => Auth::user()?->isSuperAdmin() === true)
                        ->successNotificationTitle('Selected scholarships deleted')
                        ->modalDescription('This will soft-delete the selected scholarships.'),
                ]),
            ]);
    }
}
