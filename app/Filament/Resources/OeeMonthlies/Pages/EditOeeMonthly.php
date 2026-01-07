<?php

namespace App\Filament\Resources\OeeMonthlies\Pages;

use App\Filament\Resources\OeeMonthlies\OeeMonthlyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

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
