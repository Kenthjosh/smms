<?php

namespace App\Filament\Resources\Scholarships\Schemas;

use App\Models\Application;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScholarshipInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('slug')
                                    ->label('Code'),
                                TextEntry::make('type')
                                    ->badge(),
                                IconEntry::make('is_active')
                                    ->label('Active')
                                    ->boolean(),
                            ]),
                        TextEntry::make('description')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ]),

                Section::make('Status & Dates')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('application_deadline')
                                    ->label('Deadline')
                                    ->date('M j, Y')
                                    ->placeholder('—'),
                                TextEntry::make('start_date')
                                    ->date('M j, Y')
                                    ->placeholder('—'),
                                TextEntry::make('end_date')
                                    ->date('M j, Y')
                                    ->placeholder('—'),
                            ]),
                    ]),

                Section::make('Counts')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('applications_total')
                                    ->label('Applications')
                                    ->state(fn(\App\Models\Scholarship $record): int => $record->applications()->count())
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('applications_approved')
                                    ->label('Approved')
                                    ->state(fn(\App\Models\Scholarship $record): int => $record->applications()->where('status', 'approved')->count())
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('applications_rejected')
                                    ->label('Rejected')
                                    ->state(fn(\App\Models\Scholarship $record): int => $record->applications()->where('status', 'rejected')->count())
                                    ->badge()
                                    ->color('danger'),
                                TextEntry::make('applications_pending')
                                    ->label('Pending Review')
                                    ->state(fn(\App\Models\Scholarship $record): int => $record->applications()->whereIn('status', ['submitted', 'under_review'])->count())
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ]),

                Section::make('Program Details')
                    ->schema([
                        KeyValueEntry::make('settings')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->state(function ($record): array {
                                $state = $record->settings ?? [];

                                if (is_string($state)) {
                                    $decoded = json_decode($state, true);
                                    $state = is_array($decoded) ? $decoded : [];
                                }

                                if (! is_array($state)) {
                                    return [];
                                }

                                $normalize = function ($value) use (&$normalize) {
                                    if (is_array($value)) {
                                        $isAssoc = array_keys($value) !== range(0, count($value) - 1);
                                        if ($isAssoc) {
                                            return implode(', ', array_map(function ($k) use ($value, $normalize) {
                                                $v = $normalize($value[$k]);
                                                return $k . ': ' . $v;
                                            }, array_keys($value)));
                                        }

                                        return implode(', ', array_map(fn($v) => $normalize($v), $value));
                                    }

                                    if ($value instanceof \DateTimeInterface) {
                                        return $value->format('M j, Y g:i A');
                                    }

                                    if (is_bool($value)) {
                                        return $value ? 'Yes' : 'No';
                                    }

                                    return (string) $value;
                                };

                                $normalized = [];
                                foreach ($state as $key => $value) {
                                    $normalized[$key] = $normalize($value);
                                }

                                return $normalized;
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
