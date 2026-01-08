<?php

namespace App\Filament\Resources\PartStocks\Pages;

use App\Filament\Resources\PartStocks\PartStockResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditPartStock extends EditRecord
{
    protected static string $resource = PartStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
