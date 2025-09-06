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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema as DbSchema;
use Illuminate\Support\Str;
use App\Filament\Resources\Scholarships\Schemas\ScholarshipInfolist;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    public static function getNavigationBadge(): ?string
    {
        $hasIsActive = DbSchema::hasColumn('scholarships', 'is_active');

        return (string) ($hasIsActive
            ? Scholarship::query()->where('is_active', true)->count()
            : Scholarship::query()->count());
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $hasIsActive = DbSchema::hasColumn('scholarships', 'is_active');
        return $hasIsActive ? 'Active scholarships' : 'Total scholarships';
    }

    public static function form(Schema $schema): Schema
    {
        return ScholarshipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScholarshipsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScholarshipInfolist::configure($schema);
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
            'deleted' => Pages\ListDeletedScholarships::route('/deleted'),
            'create' => CreateScholarship::route('/create'),
            'view' => Pages\ViewScholarship::route('/{record}'),
            'edit' => EditScholarship::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'type'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Code' => $record->slug,
            'Type' => Str::headline((string) $record->type),
            'Deadline' => optional($record->application_deadline)?->format('M j, Y') ?? '—',
        ];
    }
}
