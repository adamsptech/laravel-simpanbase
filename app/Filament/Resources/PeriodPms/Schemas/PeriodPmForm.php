<?php

namespace App\Filament\Resources\PeriodPms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PeriodPmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('days')
                    ->required()
                    ->numeric(),
            ]);
    }
}
