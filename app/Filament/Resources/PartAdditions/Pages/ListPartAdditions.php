<?php

namespace App\Filament\Resources\PartAdditions\Pages;

use App\Filament\Resources\PartAdditions\PartAdditionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPartAdditions extends ListRecords
{
    protected static string $resource = PartAdditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
