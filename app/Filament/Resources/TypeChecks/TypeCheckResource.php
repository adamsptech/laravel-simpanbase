<?php

namespace App\Filament\Resources\TypeChecks;

use App\Filament\Resources\TypeChecks\Pages\CreateTypeCheck;
use App\Filament\Resources\TypeChecks\Pages\EditTypeCheck;
use App\Filament\Resources\TypeChecks\Pages\ListTypeChecks;
use App\Filament\Resources\TypeChecks\RelationManagers\ChecklistsRelationManager;
use App\Filament\Resources\TypeChecks\Schemas\TypeCheckForm;
use App\Filament\Resources\TypeChecks\Tables\TypeChecksTable;
use App\Models\TypeCheck;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TypeCheckResource extends Resource
{
    protected static ?string $model = TypeCheck::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static \UnitEnum|string|null $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Checklist';

    protected static ?string $modelLabel = 'Checklist Type';
    
    protected static ?string $pluralModelLabel = 'Checklists';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TypeCheckForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TypeChecksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // Using inline Repeater in form instead
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTypeChecks::route('/'),
            'create' => CreateTypeCheck::route('/create'),
            'edit' => EditTypeCheck::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner']);
    }
}

