<?php

namespace App\Filament\Resources\PartStocks\Pages;

use App\Filament\Resources\PartStocks\PartStockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPartStocks extends ListRecords
{
    protected static string $resource = PartStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
