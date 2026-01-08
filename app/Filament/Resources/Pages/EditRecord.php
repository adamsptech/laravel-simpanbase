<?php

namespace App\Filament\Resources\Pages;

use Filament\Resources\Pages\EditRecord as BaseEditRecord;

/**
 * Base EditRecord class for all resources.
 * - Redirects to list after save
 */
abstract class EditRecord extends BaseEditRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
