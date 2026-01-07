<?php

namespace App\Filament\Resources\MaintCategories\Pages;

use App\Filament\Resources\MaintCategories\MaintCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintCategories extends ListRecords
{
    protected static string $resource = MaintCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
