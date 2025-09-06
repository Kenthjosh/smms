<?php

namespace App\Filament\Resources\Scholarships\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class ScholarshipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus(),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('type')
                                    ->required()
                                    ->maxLength(100),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->helperText(fn(Get $get): ?string => $get('is_active') ? null : 'Inactive scholarships are hidden from application forms and will not accept new applications.')
                                    ->default(true),
                            ]),
                        Textarea::make('description')
                            ->default(null)
                            ->columnSpanFull(),
                    ]),

                Section::make('Dates')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('application_deadline')
                                    ->label('Application deadline')
                                    ->native(false)
                                    ->minDate(now()->startOfDay())
                                    ->rule('after_or_equal:today')
                                    ->helperText('Must be today or in the future.'),
                                DatePicker::make('start_date')
                                    ->native(false),
                                DatePicker::make('end_date')
                                    ->native(false)
                                    ->rule('after_or_equal:start_date')
                                    ->helperText('End date must be after or same as start date.'),
                            ]),
                    ]),

                Section::make('Settings')
                    ->schema([
                        Fieldset::make('Program Settings')
                            ->schema([
                                KeyValue::make('settings')
                                    ->label('Settings (JSON)')
                                    ->keyLabel('Key')
                                    ->valueLabel('Value')
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }
}
