<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('sublocation_id')
                    ->relationship('sublocation', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('serial_number')
                    ->default(null),
                TextInput::make('category')
                    ->default(null),
                DatePicker::make('warranty_expiry_date')
                    ->label('Warranty Expiry Date')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}

