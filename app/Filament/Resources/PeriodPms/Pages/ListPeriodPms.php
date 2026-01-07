<?php

namespace App\Filament\Resources\PeriodPms\Pages;

use App\Filament\Resources\PeriodPms\PeriodPmResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPeriodPms extends ListRecords
{
    protected static string $resource = PeriodPmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
