<?php

namespace App\Filament\Resources\MachineDowntimes\Tables;

use App\Filament\Exports\MachineDowntimeExporter;
use App\Models\MachineDowntime;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MachineDowntimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_datetime', 'desc')
            ->columns([
                TextColumn::make('equipment.name')
                    ->label('Equipment')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('problem')
                    ->label('Problem')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'closed' => 'Closed',
                        default => $state,
                    }),
                TextColumn::make('start_datetime')
                    ->label('Start')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('end_datetime')
                    ->label('End')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('downtime_minutes')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => MachineDowntime::formatDowntime($state))
                    ->sortable(),
                TextColumn::make('engineer.name')
                    ->label('Picked Up By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('year')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('month')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reporter.name')
                    ->label('Reported By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('equipment')
                    ->relationship('equipment', 'name'),
                SelectFilter::make('year')
                    ->options(array_combine(
                        range(now()->year - 2, now()->year + 1),
                        range(now()->year - 2, now()->year + 1)
                    )),
                SelectFilter::make('month')
                    ->options([
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(MachineDowntimeExporter::class)
                    ->label('Export'),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(MachineDowntimeExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
