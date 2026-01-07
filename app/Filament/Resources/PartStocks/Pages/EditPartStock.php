<?php

namespace App\Filament\Resources\PartStocks\Pages;

use App\Filament\Resources\PartStocks\PartStockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

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
