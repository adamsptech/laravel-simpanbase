<?php

namespace App\Filament\Resources\OeeMonthlies\Pages;

use App\Filament\Resources\OeeMonthlies\OeeMonthlyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOeeMonthlies extends ListRecords
{
    protected static string $resource = OeeMonthlyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
