<?php

namespace App\Filament\Resources\PartStocks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PartStockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('part_id')
                    ->required(),
                TextInput::make('sap_id')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('min_quantity')
                    ->numeric()
                    ->default(null),
                TextInput::make('price')
                    ->numeric()
                    ->default(null)
                    ->prefix('$'),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->default(null),
                Select::make('address_id')
                    ->relationship('address', 'name')
                    ->default(null),
                Select::make('equipment_id')
                    ->relationship('equipment', 'name')
                    ->default(null),
                Toggle::make('is_obsolete')
                    ->required(),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('reminder_days')
                    ->numeric()
                    ->default(null),
                DatePicker::make('last_reminder_at'),
            ]);
    }
}
