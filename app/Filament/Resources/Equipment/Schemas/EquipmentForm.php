<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sublocation_id')
                    ->relationship('sublocation', 'name')
                    ->default(null),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('serial_number')
                    ->default(null),
                TextInput::make('category')
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
