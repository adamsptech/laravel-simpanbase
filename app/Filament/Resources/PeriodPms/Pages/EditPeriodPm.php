<?php

namespace App\Filament\Resources\PeriodPms\Pages;

use App\Filament\Resources\PeriodPms\PeriodPmResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

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
