<?php

namespace App\Filament\Resources\Scholarships;

use App\Filament\Resources\Scholarships\Pages\CreateScholarship;
use App\Filament\Resources\Scholarships\Pages\EditScholarship;
use App\Filament\Resources\Scholarships\Pages\ListScholarships;
use App\Filament\Resources\Scholarships\Schemas\ScholarshipForm;
use App\Filament\Resources\Scholarships\Tables\ScholarshipsTable;
use App\Models\Scholarship;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ScholarshipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScholarshipsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScholarships::route('/'),
            'create' => CreateScholarship::route('/create'),
            'edit' => EditScholarship::route('/{record}/edit'),
        ];
    }
}
