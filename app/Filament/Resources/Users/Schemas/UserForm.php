<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('scholarship_id')
                    ->relationship('scholarship', 'name')
                    ->default(null),
                Select::make('role')
                    ->options(['admin' => 'Admin', 'committee' => 'Committee', 'student' => 'Student'])
                    ->default('student')
                    ->required(),
                Textarea::make('profile_data')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
