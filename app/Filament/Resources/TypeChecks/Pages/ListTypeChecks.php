<?php

namespace App\Filament\Resources\TypeChecks\Pages;

use App\Filament\Resources\TypeChecks\TypeCheckResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTypeChecks extends ListRecords
{
    protected static string $resource = TypeCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
