<?php

namespace App\Filament\Resources\Sublocations\Pages;

use App\Filament\Resources\Sublocations\SublocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSublocations extends ListRecords
{
    protected static string $resource = SublocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
