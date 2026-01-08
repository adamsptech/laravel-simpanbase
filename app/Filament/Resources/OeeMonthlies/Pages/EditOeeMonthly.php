<?php

namespace App\Filament\Resources\OeeMonthlies\Pages;

use App\Filament\Resources\OeeMonthlies\OeeMonthlyResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditOeeMonthly extends EditRecord
{
    protected static string $resource = OeeMonthlyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
