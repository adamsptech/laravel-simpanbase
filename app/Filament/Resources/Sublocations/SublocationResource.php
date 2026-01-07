<?php

namespace App\Filament\Resources\Sublocations;

use App\Filament\Resources\Sublocations\Pages\CreateSublocation;
use App\Filament\Resources\Sublocations\Pages\EditSublocation;
use App\Filament\Resources\Sublocations\Pages\ListSublocations;
use App\Filament\Resources\Sublocations\Schemas\SublocationForm;
use App\Filament\Resources\Sublocations\Tables\SublocationsTable;
use App\Models\Sublocation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SublocationResource extends Resource
{
    protected static ?string $model = Sublocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SublocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SublocationsTable::configure($table);
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
            'index' => ListSublocations::route('/'),
            'create' => CreateSublocation::route('/create'),
            'edit' => EditSublocation::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner']);
    }
}

