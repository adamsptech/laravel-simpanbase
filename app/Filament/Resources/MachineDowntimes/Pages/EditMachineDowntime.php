<?php

namespace App\Filament\Resources\MachineDowntimes\Pages;

use App\Filament\Resources\MachineDowntimes\MachineDowntimeResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditMachineDowntime extends EditRecord
{
    protected static string $resource = MachineDowntimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
