<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Models\Equipment;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = -2;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'filament.pages.dashboard';

    public function getTitle(): string|Htmlable
    {
        return 'Dashboard';
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $role = $user->role?->name ?? 'User';

        // Get statistics based on role
        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('Y-m');

        // Base queries
        $baseQuery = Task::query();
        
        // Role-specific filtering
        if ($role === 'Engineer') {
            $baseQuery->where('assigned_to', $user->id);
        } elseif ($role === 'Supervisor') {
            $baseQuery->where('supervisor_id', $user->id);
        }

        // Work Order Statistics
        $totalTasks = (clone $baseQuery)->count();
        $openTasks = (clone $baseQuery)->where('status', Task::STATUS_OPEN)->count();
        $pendingApproval = (clone $baseQuery)->whereIn('status', [
            Task::STATUS_SUBMITTED_SUPERVISOR,
            Task::STATUS_SUBMITTED_MANAGER,
            Task::STATUS_SUBMITTED_CUSTOMER,
        ])->count();
        $closedTasks = (clone $baseQuery)->where('status', Task::STATUS_CLOSED)->count();
        
        // Overdue tasks
        $overdueTasks = (clone $baseQuery)
            ->where('status', '!=', Task::STATUS_CLOSED)
            ->whereDate('due_date', '<', $today)
            ->count();

        // This month's tasks
        $thisMonthTotal = Task::whereRaw("DATE_FORMAT(due_date, '%Y-%m') = ?", [$thisMonth])->count();
        $thisMonthCompleted = Task::whereRaw("DATE_FORMAT(due_date, '%Y-%m') = ?", [$thisMonth])
            ->where('status', Task::STATUS_CLOSED)
            ->count();

        // Recent work orders (last 10)
        $recentTasks = Task::with(['equipment:id,name', 'assignedUser:id,name', 'maintCategory:id,name'])
            ->select(['id', 'equipment_id', 'assigned_to', 'maint_category_id', 'status', 'priority', 'due_date', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Upcoming due (next 7 days)
        $upcomingDue = Task::with(['equipment:id,name', 'assignedUser:id,name'])
            ->select(['id', 'equipment_id', 'assigned_to', 'status', 'priority', 'due_date'])
            ->where('status', '!=', Task::STATUS_CLOSED)
            ->whereDate('due_date', '>=', $today)
            ->whereDate('due_date', '<=', now()->addDays(7)->format('Y-m-d'))
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        // Tasks by priority
        $tasksByPriority = Task::where('status', '!=', Task::STATUS_CLOSED)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->keyBy('priority')
            ->toArray();

        // Equipment count (for admin/planner)
        $equipmentCount = Equipment::count();
        $userCount = User::where('is_active', true)->count();

        return [
            'role' => $role,
            'totalTasks' => $totalTasks,
            'openTasks' => $openTasks,
            'pendingApproval' => $pendingApproval,
            'closedTasks' => $closedTasks,
            'overdueTasks' => $overdueTasks,
            'thisMonthTotal' => $thisMonthTotal,
            'thisMonthCompleted' => $thisMonthCompleted,
            'completionRate' => $thisMonthTotal > 0 ? round(($thisMonthCompleted / $thisMonthTotal) * 100, 1) : 0,
            'recentTasks' => $recentTasks,
            'upcomingDue' => $upcomingDue,
            'tasksByPriority' => $tasksByPriority,
            'equipmentCount' => $equipmentCount,
            'userCount' => $userCount,
        ];
    }
}
