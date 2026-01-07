<?php

namespace App\Filament\Exports;

use App\Models\PartStock;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class PartStockExporter extends Exporter
{
    protected static ?string $model = PartStock::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('part_id'),
            ExportColumn::make('sap_id'),
            ExportColumn::make('name'),
            ExportColumn::make('quantity'),
            ExportColumn::make('min_quantity'),
            ExportColumn::make('price'),
            ExportColumn::make('supplier.name'),
            ExportColumn::make('address.name'),
            ExportColumn::make('equipment.name'),
            ExportColumn::make('is_obsolete'),
            ExportColumn::make('image'),
            ExportColumn::make('reminder_days'),
            ExportColumn::make('last_reminder_at'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your part stock export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
