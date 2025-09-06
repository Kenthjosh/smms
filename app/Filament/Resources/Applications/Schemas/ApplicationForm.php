<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();

        return $schema
            ->components([
                Section::make('Associations')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Applicant')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('scholarship_id')
                                    ->label('Scholarship')
                                    ->relationship('scholarship', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ]),

                Section::make('Application Data')
                    ->schema([
                        Textarea::make('application_data')
                            ->helperText('JSON or structured data captured from public application form')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Status & Review')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->options(function () use ($user): array {
                                        $all = [
                                            'draft' => 'Draft',
                                            'submitted' => 'Submitted',
                                            'under_review' => 'Under review',
                                            'approved' => 'Approved',
                                            'rejected' => 'Rejected',
                                        ];

                                        return ($user?->isStudent())
                                            ? ['draft' => 'Draft', 'submitted' => 'Submitted']
                                            : $all;
                                    })
                                    ->default('draft')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                        if ($state === 'submitted' && blank($get('submitted_at'))) {
                                            $set('submitted_at', now());
                                        }
                                    }),
                                DateTimePicker::make('submitted_at')
                                    ->label('Submitted at')
                                    ->seconds(false),
                                DateTimePicker::make('reviewed_at')
                                    ->label('Reviewed at')
                                    ->seconds(false),
                                Textarea::make('committee_notes')
                                    ->label('Review notes')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->required(fn(Get $get) => in_array($get('status'), ['approved', 'rejected'], true))
                                    ->visible(fn() => $user?->isCommittee() || $user?->isAdmin()),
                                TextInput::make('score')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->visible(fn() => $user?->isCommittee() || $user?->isAdmin()),
                                TextInput::make('reviewed_by')
                                    ->label('Reviewer user ID')
                                    ->numeric()
                                    ->default(null)
                                    ->visible(fn() => $user?->isCommittee() || $user?->isAdmin()),
                            ]),
                    ]),
            ]);
    }
}