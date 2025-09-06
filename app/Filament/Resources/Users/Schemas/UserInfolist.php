<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use App\Models\Application;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Full Name'),
                                TextEntry::make('email')
                                    ->label('Email Address')
                                    ->copyable()
                                    ->icon(Heroicon::Envelope),
                            ])
                    ]),
                Section::make('Account & Program')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('role')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'admin' => 'danger',
                                        'committee' => 'warning',
                                        'student' => 'success',
                                        default => 'gray',
                                    }),
                                TextEntry::make('scholarship.name')
                                    ->label('Scholarship Program')
                                    ->placeholder('None')
                                    ->icon(Heroicon::AcademicCap),
                            ])
                    ]),
                Section::make('Status & Activity')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                IconEntry::make('email_verified_at')
                                    ->label('Email Verified')
                                    ->boolean(),
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->since()
                                    ->dateTimeTooltip('M j, Y g:i A'),
                                TextEntry::make('updated_at')
                                    ->label('Updated')
                                    ->since()
                                    ->dateTimeTooltip('M j, Y g:i A'),
                            ])
                    ]),
                Section::make('Applications')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('applications_count')
                                    ->label('Total Applications')
                                    ->state(fn(\App\Models\User $record): int => $record->applications()->count())
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('latest_application_status')
                                    ->label('Latest Application Status')
                                    ->state(function (\App\Models\User $record): ?string {
                                        $latest = $record->applications()->latest('created_at')->first();
                                        return $latest?->status ? ucfirst(str_replace('_', ' ', $latest->status)) : null;
                                    })
                                    ->placeholder('None')
                                    ->badge()
                                    ->color(fn(?string $state): string => match (strtolower((string) $state)) {
                                        'approved' => 'success',
                                        'under review' => 'warning',
                                        'submitted' => 'primary',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('latest_application_date')
                                    ->label('Latest Application Date')
                                    ->state(fn(\App\Models\User $record): ?\Illuminate\Support\Carbon => $record->applications()->latest('created_at')->value('created_at'))
                                    ->dateTime('M j, Y')
                                    ->placeholder('—'),
                            ])
                    ])
                    ->visible(fn(\App\Models\User $record): bool => $record->role === 'student'),

                Section::make('Committee Overview')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('committee_total_applications')
                                    ->label('Total Applications')
                                    ->state(fn(\App\Models\User $record): int => Application::query()
                                        ->when($record->scholarship_id, fn($q) => $q->where('scholarship_id', $record->scholarship_id))
                                        ->count())
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('committee_pending_review')
                                    ->label('Pending Review')
                                    ->state(fn(\App\Models\User $record): int => Application::query()
                                        ->when($record->scholarship_id, fn($q) => $q->where('scholarship_id', $record->scholarship_id))
                                        ->whereIn('status', ['submitted', 'under_review'])
                                        ->count())
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('committee_approved')
                                    ->label('Approved')
                                    ->state(fn(\App\Models\User $record): int => Application::query()
                                        ->when($record->scholarship_id, fn($q) => $q->where('scholarship_id', $record->scholarship_id))
                                        ->where('status', 'approved')
                                        ->count())
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('committee_rejected')
                                    ->label('Rejected')
                                    ->state(fn(\App\Models\User $record): int => Application::query()
                                        ->when($record->scholarship_id, fn($q) => $q->where('scholarship_id', $record->scholarship_id))
                                        ->where('status', 'rejected')
                                        ->count())
                                    ->badge()
                                    ->color('danger'),
                            ])
                    ])
                    ->visible(fn(\App\Models\User $record): bool => $record->role === 'committee'),
                Section::make('Profile Data')
                    ->schema([
                        KeyValueEntry::make('profile_data')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->state(function ($record): array {
                                $state = $record->profile_data ?? [];

                                if (is_string($state)) {
                                    $decoded = json_decode($state, true);
                                    $state = is_array($decoded) ? $decoded : [];
                                }

                                if (! is_array($state)) {
                                    return [];
                                }

                                $normalize = function ($value) use (&$normalize) {
                                    if (is_array($value)) {
                                        // If associative array, format as key: value; otherwise join values
                                        $isAssoc = array_keys($value) !== range(0, count($value) - 1);
                                        if ($isAssoc) {
                                            return implode(', ', array_map(function ($k) use ($value, $normalize) {
                                                $v = $normalize($value[$k]);
                                                return $k . ': ' . $v;
                                            }, array_keys($value)));
                                        }

                                        return implode(', ', array_map(fn($v) => $normalize($v), $value));
                                    }

                                    if (is_bool($value)) {
                                        return $value ? 'Yes' : 'No';
                                    }

                                    if ($value instanceof \DateTimeInterface) {
                                        return $value->format('M j, Y g:i A');
                                    }

                                    return (string) $value;
                                };

                                $normalized = [];
                                foreach ($state as $key => $value) {
                                    $normalized[$key] = $normalize($value);
                                }

                                return $normalized;
                            }),
                    ])
                    ->collapsible()
                    ->collapsed(),
                Section::make('Contact')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('contact_number')
                                    ->label('Contact Number')
                                    ->placeholder('—')
                                    ->icon(Heroicon::Phone),
                                TextEntry::make('address')
                                    ->label('Address')
                                    ->placeholder('—')
                                    ->columnSpan(1)
                                    ->icon(Heroicon::MapPin),
                            ])
                    ])
            ]);
    }
}
