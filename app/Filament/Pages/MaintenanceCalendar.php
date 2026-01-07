<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Task;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class MaintenanceCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static \UnitEnum|string|null $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Calendar';

    protected string $view = 'filament.pages.maintenance-calendar';

    public function getTitle(): string|Htmlable
    {
        return 'Maintenance Calendar';
    }

    public function getViewData(): array
    {
        // Optimized query: only select needed columns, eager load minimal relations
        $tasks = Task::select(['id', 'due_date', 'status', 'equipment_id', 'maint_category_id'])
            ->with([
                'equipment:id,name',
                'maintCategory:id,name',
            ])
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->get()
            ->map(function ($task) {
                $color = match ($task->status) {
                    Task::STATUS_OPEN => '#3B82F6', // blue
                    Task::STATUS_SUBMITTED_SUPERVISOR, Task::STATUS_SUBMITTED_MANAGER, Task::STATUS_SUBMITTED_CUSTOMER => '#F59E0B', // amber
                    Task::STATUS_CLOSED => '#10B981', // green
                    default => '#6B7280', // gray
                };

                return [
                    'id' => $task->id,
                    'title' => ($task->equipment?->name ?? 'No Equipment') . ' - ' . ($task->maintCategory?->name ?? 'No Type'),
                    'start' => $task->due_date->format('Y-m-d'),
                    'color' => $color,
                    'url' => TaskResource::getUrl('view', ['record' => $task]),
                ];
            });

        return [
            'events' => $tasks->toJson(),
        ];
    }
}

