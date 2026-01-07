<?php

namespace App\Filament\Resources\Checklists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChecklistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type_check_id')
                    ->relationship('typeCheck', 'name')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('recommended')
                    ->default(null),
            ]);
    }
}
