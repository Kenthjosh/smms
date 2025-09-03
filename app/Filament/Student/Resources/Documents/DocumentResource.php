<?php

namespace App\Filament\Student\Resources\Documents;

use App\Filament\Student\Resources\Documents\Pages\CreateDocument;
use App\Filament\Student\Resources\Documents\Pages\EditDocument;
use App\Filament\Student\Resources\Documents\Pages\ListDocuments;
use App\Filament\Student\Resources\Documents\Pages\ViewDocument;
use App\Filament\Student\Resources\Documents\Schemas\DocumentForm;
use App\Filament\Student\Resources\Documents\Schemas\DocumentInfolist;
use App\Filament\Student\Resources\Documents\Tables\DocumentsTable;
use App\Models\Document;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
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
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'view' => ViewDocument::route('/{record}'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
