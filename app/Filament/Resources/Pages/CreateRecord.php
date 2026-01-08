<?php

namespace App\Filament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord as BaseCreateRecord;

/**
 * Base CreateRecord class for all resources.
 * - Disables "Create & create another" button
 * - Redirects to list after create
 */
abstract class CreateRecord extends BaseCreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function canCreateAnother(): bool
    {
        return false;
    }
}
