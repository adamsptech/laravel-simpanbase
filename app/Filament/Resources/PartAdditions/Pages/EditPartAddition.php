<?php

namespace App\Filament\Resources\PartAdditions\Pages;

use App\Filament\Resources\PartAdditions\PartAdditionResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditPartAddition extends EditRecord
{
    protected static string $resource = PartAdditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
