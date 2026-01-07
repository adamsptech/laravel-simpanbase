<?php

namespace App\Filament\Resources\Tasks;

use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Filament\Resources\Tasks\Pages\EditTask;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Filament\Resources\Tasks\Pages\ViewTask;
use App\Filament\Resources\Tasks\RelationManagers\TaskDetailsRelationManager;
use App\Filament\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Resources\Tasks\Tables\TasksTable;
use App\Models\Task;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static \UnitEnum|string|null $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Work Orders';

    protected static ?string $modelLabel = 'Work Order';

    protected static ?string $pluralModelLabel = 'Work Orders';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TaskDetailsRelationManager::class,
            RelationManagers\CmDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'view' => ViewTask::route('/{record}'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }

    /**
     * Eager load relationships to prevent N+1 queries
     * Filter results based on user role
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'equipment:id,name',
                'maintCategory:id,name',
                'assignedUser:id,name',
                'supervisor:id,name',
                'location:id,name',
            ]);

        // Role-based filtering
        $user = auth()->user();
        $role = $user?->role?->name ?? 'User';

        if ($role === 'Engineer') {
            // Engineers only see tasks assigned to them
            $query->where('assigned_to', $user->id);
        } elseif ($role === 'Supervisor') {
            // Supervisors see tasks they supervise
            $query->where('supervisor_id', $user->id);
        } elseif ($role === 'Customer') {
            // Customers see tasks pending their approval or closed
            $query->whereIn('status', [Task::STATUS_SUBMITTED_CUSTOMER, Task::STATUS_CLOSED]);
        }
        // Admin, Manager, Planner see all tasks

        return $query;
    }

    /**
     * Only Admin, Manager, Planner can create tasks
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();
        $role = $user?->role?->name ?? 'User';
        
        return in_array($role, ['Admin', 'Manager', 'Planner']);
    }
}

