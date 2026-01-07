<?php

namespace App\Filament\Resources\PartAdditions;

use App\Filament\Resources\PartAdditions\Pages\CreatePartAddition;
use App\Filament\Resources\PartAdditions\Pages\EditPartAddition;
use App\Filament\Resources\PartAdditions\Pages\ListPartAdditions;
use App\Filament\Resources\PartAdditions\Schemas\PartAdditionForm;
use App\Filament\Resources\PartAdditions\Tables\PartAdditionsTable;
use App\Models\PartAddition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PartAdditionResource extends Resource
{
    protected static ?string $model = PartAddition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlusCircle;

    protected static \UnitEnum|string|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Stock Additions';

    protected static ?string $modelLabel = 'Stock Addition';

    protected static ?string $pluralModelLabel = 'Stock Additions';

    protected static ?string $recordTitleAttribute = 'opb_number';

    public static function form(Schema $schema): Schema
    {
        return PartAdditionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartAdditionsTable::configure($table);
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
            'index' => ListPartAdditions::route('/'),
            'create' => CreatePartAddition::route('/create'),
            'edit' => EditPartAddition::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['partStock', 'supplier']);
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner']);
    }
}
