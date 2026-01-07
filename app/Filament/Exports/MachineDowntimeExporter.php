<?php

namespace App\Filament\Exports;

use App\Models\MachineDowntime;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class MachineDowntimeExporter extends Exporter
{
    protected static ?string $model = MachineDowntime::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('equipment.name'),
            ExportColumn::make('problem'),
            ExportColumn::make('root_cause'),
            ExportColumn::make('start_datetime'),
            ExportColumn::make('end_datetime'),
            ExportColumn::make('downtime_minutes'),
            ExportColumn::make('year'),
            ExportColumn::make('month'),
            ExportColumn::make('reported_by'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your machine downtime export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
