<?php

namespace App\Filament\Resources\TypeChecks\Pages;

use App\Filament\Resources\TypeChecks\TypeCheckResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTypeCheck extends EditRecord
{
    protected static string $resource = TypeCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
