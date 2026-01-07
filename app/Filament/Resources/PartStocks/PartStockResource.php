<?php

namespace App\Filament\Resources\PartStocks;

use App\Filament\Resources\PartStocks\Pages\CreatePartStock;
use App\Filament\Resources\PartStocks\Pages\EditPartStock;
use App\Filament\Resources\PartStocks\Pages\ListPartStocks;
use App\Filament\Resources\PartStocks\Schemas\PartStockForm;
use App\Filament\Resources\PartStocks\Tables\PartStocksTable;
use App\Models\PartStock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PartStockResource extends Resource
{
    protected static ?string $model = PartStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static \UnitEnum|string|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PartStockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartStocksTable::configure($table);
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
            'index' => ListPartStocks::route('/'),
            'create' => CreatePartStock::route('/create'),
            'edit' => EditPartStock::route('/{record}/edit'),
        ];
    }

    /**
     * Admin, Manager, Planner, Engineer can access spare parts
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        $role = $user?->role?->name ?? 'User';
        
        return in_array($role, ['Admin', 'Manager', 'Planner', 'Engineer']);
    }
}

