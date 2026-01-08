<?php

namespace App\Filament\Resources\TypeChecks\Pages;

use App\Filament\Resources\TypeChecks\TypeCheckResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

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
