<?php

namespace App\Filament\Resources\MaintCategories;

use App\Filament\Resources\MaintCategories\Pages\CreateMaintCategory;
use App\Filament\Resources\MaintCategories\Pages\EditMaintCategory;
use App\Filament\Resources\MaintCategories\Pages\ListMaintCategories;
use App\Filament\Resources\MaintCategories\Schemas\MaintCategoryForm;
use App\Filament\Resources\MaintCategories\Tables\MaintCategoriesTable;
use App\Models\MaintCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaintCategoryResource extends Resource
{
    protected static ?string $model = MaintCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MaintCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintCategoriesTable::configure($table);
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
            'index' => ListMaintCategories::route('/'),
            'create' => CreateMaintCategory::route('/create'),
            'edit' => EditMaintCategory::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner']);
    }
}

