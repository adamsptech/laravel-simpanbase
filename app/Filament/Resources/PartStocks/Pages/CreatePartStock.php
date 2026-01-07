<?php

namespace App\Filament\Resources\PartStocks\Pages;

use App\Filament\Resources\PartStocks\PartStockResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartStock extends CreateRecord
{
    protected static string $resource = PartStockResource::class;
}
