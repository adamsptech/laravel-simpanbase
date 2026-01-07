<?php

namespace App\Filament\Resources\TypeChecks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TypeCheckForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('equipment_id')
                    ->relationship('equipment', 'name')
                    ->default(null),
                Select::make('period_id')
                    ->relationship('period', 'name')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
