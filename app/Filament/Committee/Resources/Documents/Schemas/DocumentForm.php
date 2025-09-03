<?php

namespace App\Filament\Committee\Resources\Documents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('application_id')
                    ->relationship('application', 'id')
                    ->required(),
                TextInput::make('document_type')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('original_name')
                    ->required(),
                TextInput::make('mime_type')
                    ->required(),
                TextInput::make('file_size')
                    ->required()
                    ->numeric(),
                Toggle::make('is_verified')
                    ->required(),
                DateTimePicker::make('verified_at'),
                TextInput::make('verified_by')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
