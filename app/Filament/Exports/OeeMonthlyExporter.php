<?php

namespace App\Filament\Exports;

use App\Models\OeeMonthly;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class OeeMonthlyExporter extends Exporter
{
    protected static ?string $model = OeeMonthly::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('equipment.name'),
            ExportColumn::make('year'),
            ExportColumn::make('month'),
            ExportColumn::make('working_days'),
            ExportColumn::make('working_hours'),
            ExportColumn::make('working_minutes'),
            ExportColumn::make('plant_operating_days'),
            ExportColumn::make('plant_operating_hours'),
            ExportColumn::make('plant_operating_minutes'),
            ExportColumn::make('plant_operating_percentage'),
            ExportColumn::make('planned_maintenance_days'),
            ExportColumn::make('planned_maintenance_hours'),
            ExportColumn::make('planned_maintenance_minutes'),
            ExportColumn::make('planned_maintenance_percentage'),
            ExportColumn::make('plant_production_days'),
            ExportColumn::make('plant_production_hours'),
            ExportColumn::make('plant_production_minutes'),
            ExportColumn::make('plant_production_percentage'),
            ExportColumn::make('unplanned_maintenance_days'),
            ExportColumn::make('unplanned_maintenance_hours'),
            ExportColumn::make('unplanned_maintenance_minutes'),
            ExportColumn::make('unplanned_maintenance_percentage'),
            ExportColumn::make('actual_production_days'),
            ExportColumn::make('actual_production_hours'),
            ExportColumn::make('actual_production_minutes'),
            ExportColumn::make('actual_production_percentage'),
            ExportColumn::make('availability'),
            ExportColumn::make('performance'),
            ExportColumn::make('quality'),
            ExportColumn::make('oee_percentage'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your oee monthly export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
