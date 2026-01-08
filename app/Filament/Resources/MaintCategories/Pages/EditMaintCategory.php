<?php

namespace App\Filament\Resources\MaintCategories\Pages;

use App\Filament\Resources\MaintCategories\MaintCategoryResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditMaintCategory extends EditRecord
{
    protected static string $resource = MaintCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
