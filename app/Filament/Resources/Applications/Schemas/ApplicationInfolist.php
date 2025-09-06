<?php

namespace App\Filament\Resources\Applications\Schemas;

use App\Filament\Resources\Users\UserResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Applicant')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Name')
                                    ->url(fn($record) => UserResource::getUrl('view', ['record' => $record->user_id]))
                                    ->openUrlInNewTab(),
                                TextEntry::make('user.email')
                                    ->label('Email'),
                            ]),
                    ]),

                Section::make('Scholarship')
                    ->schema([
                        TextEntry::make('scholarship.name')
                            ->label('Program'),
                    ]),

                Section::make('Status & Timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn(string $state) => match ($state) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'under_review' => 'warning',
                                        'submitted' => 'info',
                                        default => 'gray',
                                    }),
                                TextEntry::make('submitted_at')->dateTime()->since(),
                                TextEntry::make('reviewed_at')->dateTime()->since(),
                            ]),
                    ]),

                Section::make('Scores & Notes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('score')->placeholder('—'),
                                TextEntry::make('committee_notes')->columnSpanFull()->placeholder('—'),
                            ]),
                    ]),

                Section::make('Documents')
                    ->schema([
                        RepeatableEntry::make('documents')
                            ->label('Uploaded Documents')
                            ->schema([
                                TextEntry::make('original_name')
                                    ->url(fn($record) => $record->file_path ? (str_starts_with($record->file_path, 'http') ? $record->file_path : asset('storage/' . $record->file_path)) : null)
                                    ->openUrlInNewTab()
                                    ->placeholder('—'),
                                TextEntry::make('mime_type')->label('Type')->placeholder('—'),
                            ])
                            ->contained(false),
                        TextEntry::make('documents_empty')
                            ->label('Uploaded Documents')
                            ->state('No documents uploaded')
                            ->visible(fn($record) => $record->documents()->count() === 0),
                    ]),
            ]);
    }
}
