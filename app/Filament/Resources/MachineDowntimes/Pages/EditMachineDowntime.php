<?php

namespace App\Filament\Resources\MachineDowntimes\Pages;

use App\Filament\Resources\MachineDowntimes\MachineDowntimeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

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
