<?php

namespace App\Filament\Resources\OeeMonthlies\Tables;

use App\Filament\Exports\OeeMonthlyExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OeeMonthliesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('equipment.name')
                    ->searchable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('month')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('working_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('working_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('working_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('availability')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('performance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quality')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('oee_percentage')
                    ->numeric()
                    ->sortable(),
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
                    ->exporter(OeeMonthlyExporter::class)
                    ->label('Export'),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(OeeMonthlyExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
