<?php

namespace App\Filament\Resources\MaintCategories\Pages;

use App\Filament\Resources\MaintCategories\MaintCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

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
