<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Filament\Exports\TaskExporter;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('WO #')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('series_id')
                    ->label('')
                    ->width('40px')
                    ->formatStateUsing(fn ($state) => $state ? '🔄' : '')
                    ->tooltip(fn ($record) => $record->series_id ? "Recurring: {$record->recurrence_label}" : null)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('equipment.name')
                    ->label('Equipment')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('maintCategory.name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Preventive Maintenance' => 'success',
                        'Corrective Maintenance' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        Task::STATUS_OPEN => 'Open',
                        Task::STATUS_SUBMITTED_SUPERVISOR => 'Pending Supervisor',
                        Task::STATUS_SUBMITTED_MANAGER => 'Pending Manager',
                        Task::STATUS_SUBMITTED_CUSTOMER => 'Pending Customer',
                        Task::STATUS_CLOSED => 'Closed',
                        default => 'Unknown',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        Task::STATUS_OPEN => 'info',
                        Task::STATUS_SUBMITTED_SUPERVISOR => 'warning',
                        Task::STATUS_SUBMITTED_MANAGER => 'warning',
                        Task::STATUS_SUBMITTED_CUSTOMER => 'warning',
                        Task::STATUS_CLOSED => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        Task::PRIORITY_LOW => 'Low',
                        Task::PRIORITY_MEDIUM => 'Medium',
                        Task::PRIORITY_HIGH => 'High',
                        default => 'Unknown',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        Task::PRIORITY_LOW => 'gray',
                        Task::PRIORITY_MEDIUM => 'warning',
                        Task::PRIORITY_HIGH => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('assignedUser.name')
                    ->label('Engineer')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== Task::STATUS_CLOSED ? 'danger' : null),

                TextColumn::make('location.name')
                    ->label('Location')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Task::STATUS_OPEN => 'Open',
                        Task::STATUS_SUBMITTED_SUPERVISOR => 'Pending Supervisor',
                        Task::STATUS_SUBMITTED_MANAGER => 'Pending Manager',
                        Task::STATUS_SUBMITTED_CUSTOMER => 'Pending Customer',
                        Task::STATUS_CLOSED => 'Closed',
                    ]),

                SelectFilter::make('priority')
                    ->options([
                        Task::PRIORITY_LOW => 'Low',
                        Task::PRIORITY_MEDIUM => 'Medium',
                        Task::PRIORITY_HIGH => 'High',
                    ]),

                SelectFilter::make('maint_category_id')
                    ->label('Maintenance Type')
                    ->relationship('maintCategory', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Task $record) => route('work-order.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(TaskExporter::class)
                    ->label('Export'),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(TaskExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
