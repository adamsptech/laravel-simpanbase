<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    // Store the edit scope for series updates
    public ?string $editScope = 'single';

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $role = $user->role?->name;
        $task = $this->record;

        $actions = [];

        // Recurring indicator
        if ($task->isRecurring()) {
            $actions[] = Action::make('recurringIndicator')
                ->label('Recurring Series')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->disabled()
                ->extraAttributes(['title' => "Part of series: {$task->series_id}"]);
        }

        // Submit for Approval (Engineer/Planner when task is Open)
        if ($task->status === Task::STATUS_OPEN) {
            if (($role === 'Engineer' && $task->assigned_to === $user->id) || in_array($role, ['Admin', 'Planner'])) {
                $actions[] = Action::make('submitForApproval')
                    ->label('Submit for Approval')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Submit for Supervisor Approval')
                    ->modalDescription('This will submit the work order for supervisor review.')
                    ->action(function () use ($task, $user) {
                        $task->update([
                            'status' => Task::STATUS_SUBMITTED_SUPERVISOR,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Submitted for Approval')
                            ->body('Work order has been submitted to supervisor for review.')
                            ->send();

                        $this->redirect($this->getResource()::getUrl('index'));
                    });
            }
        }

        // Supervisor Approval
        if ($task->status === Task::STATUS_SUBMITTED_SUPERVISOR && $role === 'Supervisor') {
            $actions[] = Action::make('approveAsSupervisor')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve as Supervisor')
                ->action(function () use ($task, $user) {
                    $task->update([
                        'status' => Task::STATUS_SUBMITTED_MANAGER,
                        'approval1_by' => $user->id,
                        'approval1_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Approved')
                        ->body('Work order approved and sent to manager for review.')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                });

            $actions[] = $this->getRejectAction($task, 'Supervisor');
        }

        // Manager Approval
        if ($task->status === Task::STATUS_SUBMITTED_MANAGER && $role === 'Manager') {
            $actions[] = Action::make('approveAsManager')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve as Manager')
                ->action(function () use ($task, $user) {
                    $task->update([
                        'status' => Task::STATUS_SUBMITTED_CUSTOMER,
                        'approval2_by' => $user->id,
                        'approval2_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Approved')
                        ->body('Work order approved and sent to customer for final approval.')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                });

            $actions[] = $this->getRejectAction($task, 'Manager');
        }

        // Customer Final Approval
        if ($task->status === Task::STATUS_SUBMITTED_CUSTOMER && $role === 'Customer') {
            $actions[] = Action::make('approveAsCustomer')
                ->label('Final Approve')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Final Approval')
                ->modalDescription('This will close the work order as completed.')
                ->action(function () use ($task, $user) {
                    $task->update([
                        'status' => Task::STATUS_CLOSED,
                        'approval3_by' => $user->id,
                        'approval3_at' => now(),
                        'ended_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Work Order Closed')
                        ->body('Work order has been approved and closed.')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                });

            $actions[] = $this->getRejectAction($task, 'Customer');
        }

        // Admin can always close
        if (in_array($role, ['Admin']) && $task->status !== Task::STATUS_CLOSED) {
            $actions[] = Action::make('forceClose')
                ->label('Force Close')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Force Close Work Order')
                ->modalDescription('This will immediately close the work order.')
                ->action(function () use ($task) {
                    $task->update([
                        'status' => Task::STATUS_CLOSED,
                        'ended_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Work Order Closed')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                });
        }

        // Delete action - with series scope options for recurring tasks
        if ($task->isRecurring()) {
            $actions[] = $this->getSeriesDeleteAction($task);
        } else {
            $actions[] = DeleteAction::make();
        }

        return $actions;
    }

    /**
     * Override form actions to add series save scope for recurring tasks
     */
    protected function getFormActions(): array
    {
        $task = $this->record;

        // For recurring tasks, use custom save with scope options
        if ($task->isRecurring()) {
            return [
                $this->getSeriesSaveAction($task),
                $this->getCancelFormAction(),
            ];
        }

        // Default save actions for non-recurring tasks
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    /**
     * Series save action with scope options
     */
    protected function getSeriesSaveAction(Task $task): Action
    {
        $seriesCount = Task::where('series_id', $task->series_id)->count();
        $futureCount = Task::where('series_id', $task->series_id)
            ->where('due_date', '>=', $task->due_date)
            ->count();

        return Action::make('saveWithScope')
            ->label('Save')
            ->color('primary')
            ->form([
                Radio::make('edit_scope')
                    ->label('Apply changes to')
                    ->options([
                        'single' => "Only this occurrence",
                        'future' => "This and all future ({$futureCount} tasks)",
                        'all' => "All in series ({$seriesCount} tasks)",
                    ])
                    ->default('single')
                    ->required()
                    ->descriptions([
                        'single' => 'Updates only this work order. Marks it as an exception.',
                        'future' => 'Updates this work order and all scheduled for later dates.',
                        'all' => 'Updates all work orders in the entire series.',
                    ]),
            ])
            ->action(function (array $data) use ($task) {
                $scope = $data['edit_scope'];
                $formData = $this->form->getState();

                // Calculate date offset if due_date changed
                $originalDueDate = $task->due_date;
                $newDueDate = isset($formData['due_date']) ? \Carbon\Carbon::parse($formData['due_date']) : null;
                $dayOffset = ($originalDueDate && $newDueDate) 
                    ? $originalDueDate->diffInDays($newDueDate, false) 
                    : 0;

                // Fields to update in bulk (excluding due_date - handled separately)
                $updateFields = collect($formData)->only([
                    'priority',
                    'assigned_to',
                    'supervisor_id',
                    'notes',
                    'shift',
                ])->toArray();

                switch ($scope) {
                    case 'all':
                        // Update non-date fields
                        Task::updateSeriesTasks($task->series_id, $updateFields);
                        // Shift all dates if changed
                        if ($dayOffset !== 0) {
                            Task::shiftSeriesDates($task->series_id, $dayOffset);
                        }
                        // Update current record with form data
                        $task->update($formData);
                        
                        $count = Task::where('series_id', $task->series_id)->count();
                        Notification::make()
                            ->success()
                            ->title('Series Updated')
                            ->body("Updated {$count} work orders in series." . ($dayOffset !== 0 ? " Dates shifted by {$dayOffset} days." : ""))
                            ->send();
                        break;

                    case 'future':
                        // Update non-date fields for future tasks
                        Task::updateThisAndFutureTasks($task->series_id, $originalDueDate, $updateFields);
                        
                        // If date changed, regenerate future tasks with proper pattern
                        if ($dayOffset !== 0) {
                            // Update current task first
                            $task->update($formData);
                            $task->refresh();
                            
                            // Regenerate future tasks with correct pattern
                            $newTasks = Task::regenerateFutureTasks($task, $newDueDate, $updateFields);
                            $count = count($newTasks) + 1; // +1 for current task
                            
                            Notification::make()
                                ->success()
                                ->title('Future Tasks Regenerated')
                                ->body("Regenerated {$count} work orders with new schedule pattern.")
                                ->send();
                        } else {
                            // Just update without regenerating
                            $task->update($formData);
                            
                            $count = Task::where('series_id', $task->series_id)
                                ->whereDate('due_date', '>=', $originalDueDate->format('Y-m-d'))
                                ->count();
                            Notification::make()
                                ->success()
                                ->title('Future Tasks Updated')
                                ->body("Updated {$count} work orders.")
                                ->send();
                        }
                        break;

                    default: // single
                        $formData['is_series_exception'] = true;
                        $task->update($formData);
                        Notification::make()
                            ->success()
                            ->title('Task Updated')
                            ->body('Work order updated and marked as exception.')
                            ->send();
                        break;
                }

                $this->redirect($this->getResource()::getUrl('index'));
            });
    }

    protected function getSeriesDeleteAction(Task $task): Action
    {
        $seriesCount = Task::where('series_id', $task->series_id)->count();
        $futureCount = Task::where('series_id', $task->series_id)
            ->where('due_date', '>=', $task->due_date)
            ->count();

        return Action::make('deleteWithScope')
            ->label('Delete')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->form([
                Radio::make('delete_scope')
                    ->label('Delete Scope')
                    ->options([
                        'single' => "Only this occurrence",
                        'future' => "This and all future ({$futureCount} tasks)",
                        'all' => "All in series ({$seriesCount} tasks)",
                    ])
                    ->default('single')
                    ->required()
                    ->descriptions([
                        'single' => 'Removes only this work order.',
                        'future' => 'Removes this work order and all scheduled for later dates.',
                        'all' => 'Removes the entire recurring series.',
                    ]),
            ])
            ->action(function (array $data) use ($task) {
                $scope = $data['delete_scope'];

                switch ($scope) {
                    case 'all':
                        $count = Task::deleteSeriesTasks($task->series_id);
                        Notification::make()
                            ->success()
                            ->title('Series Deleted')
                            ->body("{$count} work orders deleted.")
                            ->send();
                        break;

                    case 'future':
                        $count = Task::deleteThisAndFutureTasks($task->series_id, $task->due_date);
                        Notification::make()
                            ->success()
                            ->title('Future Tasks Deleted')
                            ->body("{$count} work orders deleted.")
                            ->send();
                        break;

                    default: // single
                        $task->delete();
                        Notification::make()
                            ->success()
                            ->title('Task Deleted')
                            ->body('Work order deleted.')
                            ->send();
                        break;
                }

                $this->redirect($this->getResource()::getUrl('index'));
            });
    }

    protected function getRejectAction(Task $task, string $role): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading("Reject as {$role}")
            ->modalDescription('This will send the work order back to Open status.')
            ->action(function () use ($task) {
                $task->update([
                    'status' => Task::STATUS_OPEN,
                ]);

                Notification::make()
                    ->warning()
                    ->title('Rejected')
                    ->body('Work order has been rejected and returned to Open status.')
                    ->send();

                $this->redirect($this->getResource()::getUrl('index'));
            });
    }
}
