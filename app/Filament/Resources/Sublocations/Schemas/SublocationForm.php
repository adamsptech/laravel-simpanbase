<?php

namespace App\Filament\Resources\Sublocations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SublocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
