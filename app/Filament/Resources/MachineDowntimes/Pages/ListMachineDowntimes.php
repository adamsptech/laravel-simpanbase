<?php

namespace App\Filament\Resources\MachineDowntimes\Pages;

use App\Filament\Resources\MachineDowntimes\MachineDowntimeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMachineDowntimes extends ListRecords
{
    protected static string $resource = MachineDowntimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
