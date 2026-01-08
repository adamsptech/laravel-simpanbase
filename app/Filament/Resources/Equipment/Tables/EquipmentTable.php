<?php

namespace App\Filament\Resources\Equipment\Tables;

use App\Filament\Exports\EquipmentExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sublocation.name')
                    ->searchable(),
                TextColumn::make('supplier.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('warranty_expiry_date')
                    ->label('Warranty Expiry')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->warranty_expiry_date === null => 'gray',
                        $record->warranty_expiry_date->isPast() => 'danger',
                        $record->warranty_expiry_date->diffInDays(now()) <= 60 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(EquipmentExporter::class)
                    ->label('Export'),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(EquipmentExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
