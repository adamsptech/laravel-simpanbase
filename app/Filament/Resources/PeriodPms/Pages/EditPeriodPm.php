<?php

namespace App\Filament\Resources\PeriodPms\Pages;

use App\Filament\Resources\PeriodPms\PeriodPmResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPeriodPm extends EditRecord
{
    protected static string $resource = PeriodPmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
