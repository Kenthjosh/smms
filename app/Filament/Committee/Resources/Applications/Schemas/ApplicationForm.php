<?php

namespace App\Filament\Committee\Resources\Applications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('scholarship_id')
                    ->relationship('scholarship', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Textarea::make('application_data')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ])
                    ->default('draft')
                    ->required(),
                Textarea::make('committee_notes')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('submitted_at'),
                DateTimePicker::make('reviewed_at'),
                TextInput::make('reviewed_by')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
