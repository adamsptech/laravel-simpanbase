<?php

namespace App\Filament\Exports;

use App\Models\Task;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class TaskExporter extends Exporter
{
    protected static ?string $model = Task::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('location.name'),
            ExportColumn::make('sublocation.name'),
            ExportColumn::make('equipment.name'),
            ExportColumn::make('period.name'),
            ExportColumn::make('typeCheck.name'),
            ExportColumn::make('maintCategory.name'),
            ExportColumn::make('status'),
            ExportColumn::make('priority'),
            ExportColumn::make('assigned_to'),
            ExportColumn::make('supervisor.name'),
            ExportColumn::make('approval1_by'),
            ExportColumn::make('approval1_at'),
            ExportColumn::make('approval2_by'),
            ExportColumn::make('approval2_at'),
            ExportColumn::make('approval3_by'),
            ExportColumn::make('approval3_at'),
            ExportColumn::make('due_date'),
            ExportColumn::make('duration'),
            ExportColumn::make('started_at'),
            ExportColumn::make('ended_at'),
            ExportColumn::make('notes'),
            ExportColumn::make('series_id'),
            ExportColumn::make('recurrence_type'),
            ExportColumn::make('recurrence_end_date'),
            ExportColumn::make('is_series_exception'),
            ExportColumn::make('shift'),
            ExportColumn::make('files'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your task export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
