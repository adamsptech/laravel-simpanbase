<?php

namespace App\Filament\Resources\Sublocations\Pages;

use App\Filament\Resources\Sublocations\SublocationResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

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
