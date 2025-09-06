<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('email_verified_at')
                    ->label('Email verified at'),
                TextInput::make('contact_number')
                    ->label('Contact number')
                    ->maxLength(32)
                    ->tel()
                    ->helperText('e.g., +63 912 345 6789')
                    ->default(null),
                TextInput::make('address')
                    ->maxLength(500)
                    ->default(null),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->rule('confirmed')
                    ->minLength(8)
                    ->helperText('Leave blank to keep current password when editing'),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->label('Confirm password')
                    ->dehydrated(false)
                    ->required(fn(Get $get): bool => filled($get('password'))),
                Select::make('role')
                    ->options(fn() => Auth::user()?->isSuperAdmin()
                        ? [
                            'admin' => 'Admin',
                            'committee' => 'Committee',
                            'student' => 'Student',
                        ]
                        : [
                            'committee' => 'Committee',
                            'student' => 'Student',
                        ])
                    ->default('student')
                    ->required()
                    ->reactive(),
                Select::make('scholarship_id')
                    ->label('Scholarship Program')
                    ->relationship('scholarship', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null)
                    ->required(fn(Get $get): bool => in_array($get('role'), ['committee', 'student'], true)),
                KeyValue::make('profile_data')
                    ->label('Profile data')
                    ->keyLabel('Field')
                    ->valueLabel('Value')
                    ->reorderable()
                    ->addActionLabel('Add field')
                    ->columnSpanFull(),
            ]);
    }
}