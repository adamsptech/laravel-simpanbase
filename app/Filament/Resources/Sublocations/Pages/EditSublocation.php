<?php

namespace App\Filament\Resources\Sublocations\Pages;

use App\Filament\Resources\Sublocations\SublocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSublocation extends EditRecord
{
    protected static string $resource = SublocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
