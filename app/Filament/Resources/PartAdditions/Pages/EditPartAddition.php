<?php

namespace App\Filament\Resources\PartAdditions\Pages;

use App\Filament\Resources\PartAdditions\PartAdditionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

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
