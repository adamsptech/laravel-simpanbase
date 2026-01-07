<?php

namespace App\Filament\Pages;

use App\Models\Task;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static \UnitEnum|string|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Reports';

    protected string $view = 'filament.pages.reports';

    public ?string $period = 'month';
    public ?string $year = null;
    public ?string $month = null;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function getTitle(): string|Htmlable
    {
        return 'SLM Reports & Statistics';
    }

    public function getViewData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        return [
            'statistics' => $this->getStatistics($startDate, $endDate),
            'statusBreakdown' => $this->getStatusBreakdown($startDate, $endDate),
            'categoryBreakdown' => $this->getCategoryBreakdown($startDate, $endDate),
            'monthlyTrend' => $this->getMonthlyTrend(),
            'priorityBreakdown' => $this->getPriorityBreakdown($startDate, $endDate),
            'currentPeriod' => $startDate->format('M Y') . ' - ' . $endDate->format('M Y'),
            'years' => range(now()->year - 5, now()->year + 1),
            'currentYear' => $this->year,
            'currentMonth' => $this->month,
        ];
    }

    protected function getStartDate(): Carbon
    {
        if ($this->period === 'month') {
            return Carbon::create($this->year, $this->month, 1)->startOfMonth();
        }
        return Carbon::create($this->year, 1, 1)->startOfYear();
    }

    protected function getEndDate(): Carbon
    {
        if ($this->period === 'month') {
            return Carbon::create($this->year, $this->month, 1)->endOfMonth();
        }
        return Carbon::create($this->year, 12, 31)->endOfYear();
    }

    protected function getStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $tasksQuery = Task::whereBetween('due_date', [$startDate, $endDate]);
        
        $total = (clone $tasksQuery)->count();
        $completed = (clone $tasksQuery)->where('status', Task::STATUS_CLOSED)->count();
        $open = (clone $tasksQuery)->where('status', Task::STATUS_OPEN)->count();
        $pending = (clone $tasksQuery)->whereIn('status', [
            Task::STATUS_SUBMITTED_SUPERVISOR,
            Task::STATUS_SUBMITTED_MANAGER,
            Task::STATUS_SUBMITTED_CUSTOMER,
        ])->count();
        
        $overdue = (clone $tasksQuery)
            ->where('status', '!=', Task::STATUS_CLOSED)
            ->where('due_date', '<', now())
            ->count();

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        // Average completion time (days)
        $avgCompletionTime = Task::whereBetween('due_date', [$startDate, $endDate])
            ->where('status', Task::STATUS_CLOSED)
            ->whereNotNull('started_at')
            ->whereNotNull('ended_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, started_at, ended_at)) as avg_hours')
            ->value('avg_hours');
        
        $avgCompletionDays = $avgCompletionTime ? round($avgCompletionTime / 24, 1) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'open' => $open,
            'pending' => $pending,
            'overdue' => $overdue,
            'completionRate' => $completionRate,
            'avgCompletionDays' => $avgCompletionDays,
        ];
    }

    protected function getStatusBreakdown(Carbon $startDate, Carbon $endDate): array
    {
        return Task::whereBetween('due_date', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => match ($item->status) {
                        Task::STATUS_OPEN => 'Open',
                        Task::STATUS_SUBMITTED_SUPERVISOR => 'Pending Supervisor',
                        Task::STATUS_SUBMITTED_MANAGER => 'Pending Manager',
                        Task::STATUS_SUBMITTED_CUSTOMER => 'Pending Customer',
                        Task::STATUS_CLOSED => 'Closed',
                        default => 'Unknown',
                    },
                    'count' => $item->count,
                    'color' => match ($item->status) {
                        Task::STATUS_OPEN => '#3B82F6',
                        Task::STATUS_SUBMITTED_SUPERVISOR, Task::STATUS_SUBMITTED_MANAGER, Task::STATUS_SUBMITTED_CUSTOMER => '#F59E0B',
                        Task::STATUS_CLOSED => '#10B981',
                        default => '#6B7280',
                    },
                ];
            })
            ->toArray();
    }

    protected function getCategoryBreakdown(Carbon $startDate, Carbon $endDate): array
    {
        return Task::whereBetween('due_date', [$startDate, $endDate])
            ->join('maint_categories', 'tasks.maint_category_id', '=', 'maint_categories.id')
            ->select('maint_categories.name', DB::raw('count(*) as count'))
            ->groupBy('maint_categories.name')
            ->get()
            ->toArray();
    }

    protected function getPriorityBreakdown(Carbon $startDate, Carbon $endDate): array
    {
        return Task::whereBetween('due_date', [$startDate, $endDate])
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->map(function ($item) {
                return [
                    'priority' => match ($item->priority) {
                        Task::PRIORITY_LOW => 'Low',
                        Task::PRIORITY_MEDIUM => 'Medium',
                        Task::PRIORITY_HIGH => 'High',
                        default => 'Unknown',
                    },
                    'count' => $item->count,
                    'color' => match ($item->priority) {
                        Task::PRIORITY_LOW => '#6B7280',
                        Task::PRIORITY_MEDIUM => '#F59E0B',
                        Task::PRIORITY_HIGH => '#EF4444',
                        default => '#6B7280',
                    },
                ];
            })
            ->toArray();
    }

    /**
     * Get monthly trend with optimized single query
     */
    protected function getMonthlyTrend(): array
    {
        // Use a single query with grouping instead of 24 separate queries
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();
        
        $monthlyData = Task::whereBetween('due_date', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(due_date, "%Y-%m") as month_key')
            ->selectRaw('DATE_FORMAT(due_date, "%b %Y") as month_label')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed', [Task::STATUS_CLOSED])
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        // Fill in missing months
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            
            $data = $monthlyData->get($key);
            $months[] = [
                'month' => $date->format('M Y'),
                'total' => $data ? $data->total : 0,
                'completed' => $data ? $data->completed : 0,
            ];
        }

        return $months;
    }
}

