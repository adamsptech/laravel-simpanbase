<?php

namespace App\Filament\Pages;

use App\Models\Equipment;
use App\Models\Location;
use App\Models\Task;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

class SlmReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static \UnitEnum|string|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'SLM Report';

    protected string $view = 'filament.pages.slm-report';

    // Livewire reactive properties with URL binding
    #[Url(as: 'year')]
    public ?int $year = null;
    
    #[Url(as: 'month')]
    public ?int $month = null;
    
    #[Url(as: 'equipment_id')]
    public ?int $equipment_id = null;
    
    #[Url(as: 'location_id')]
    public ?int $location_id = null;
    
    #[Url(as: 'search')]
    public string $search = '';
    
    #[Url(as: 'sort')]
    public string $sort = 'equipment';
    
    #[Url(as: 'direction')]
    public string $direction = 'asc';

    public function mount(): void
    {
        $this->year = $this->year ?? now()->year;
        $this->month = $this->month ?? now()->month;
    }

    // Method to toggle sort direction
    public function sortBy(string $column): void
    {
        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $column;
            $this->direction = 'asc';
        }
    }

    // Reset filters
    public function resetFilters(): void
    {
        $this->location_id = null;
        $this->search = '';
    }

    // View equipment details
    public function viewEquipment(int $id): void
    {
        $this->equipment_id = $id;
    }

    // Back to summary
    public function backToSummary(): void
    {
        $this->equipment_id = null;
    }

    public function getTitle(): string|Htmlable
    {
        return 'SLM (Service Level Maintenance) Report';
    }

    public function getViewData(): array
    {
        $startDate = sprintf('%04d-%02d-01', $this->year, $this->month);
        $endDate = date('Y-m-t', strtotime($startDate));

        // Build query with filters
        $query = "
            SELECT 
                e.id as equipment_id,
                e.name as equipment_name,
                e.serial_number,
                COUNT(t.id) as frequency,
                COALESCE(SUM(
                    CASE WHEN t.ended_at IS NOT NULL AND t.started_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(MINUTE, t.started_at, t.ended_at) 
                    ELSE 0 END
                ), 0) as actual_minutes,
                COUNT(t.id) * 30 as requested_minutes,
                CASE 
                    WHEN COUNT(t.id) > 0
                    THEN ROUND(
                        (COUNT(CASE WHEN t.status = 4 THEN 1 END) * 100.0 / COUNT(t.id)), 0)
                    ELSE 100
                END as slm_percentage
            FROM equipment e
            LEFT JOIN sublocations s ON e.sublocation_id = s.id
            LEFT JOIN tasks t ON e.id = t.equipment_id 
                AND t.due_date BETWEEN ? AND ?
                AND t.maint_category_id IN (SELECT id FROM maint_categories WHERE name LIKE '%Preventive%' OR name LIKE '%Periodic%' OR name LIKE '%Predictive%')
            WHERE 1=1
        ";
        
        $params = [$startDate, $endDate];
        
        if ($this->location_id) {
            $query .= " AND s.location_id = ?";
            $params[] = $this->location_id;
        }
        
        if ($this->search) {
            $query .= " AND (e.name LIKE ? OR e.serial_number LIKE ?)";
            $params[] = "%{$this->search}%";
            $params[] = "%{$this->search}%";
        }
        
        $query .= " GROUP BY e.id, e.name, e.serial_number";
        
        // Apply sorting
        $sortColumn = match($this->sort) {
            'frequency' => 'frequency',
            'actual_time' => 'actual_minutes',
            'slm' => 'slm_percentage',
            default => 'e.name',
        };
        
        $query .= " ORDER BY {$sortColumn} " . ($this->direction === 'desc' ? 'DESC' : 'ASC');

        $summary = DB::select($query, $params);

        // Calculate overall stats
        $totalTasks = 0;
        foreach ($summary as $item) {
            $totalTasks += $item->frequency;
        }
        
        $completedCount = Task::whereBetween('due_date', [$startDate, $endDate])
            ->where('status', 4)
            ->when($this->location_id, function ($q) {
                $q->whereHas('equipment.sublocation', function ($sq) {
                    $sq->where('location_id', $this->location_id);
                });
            })
            ->when($this->search, function ($q) {
                $q->whereHas('equipment', function ($sq) {
                    $sq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('serial_number', 'like', "%{$this->search}%");
                });
            })
            ->count();
        
        $overallSlm = $totalTasks > 0 ? round(($completedCount / $totalTasks) * 100, 0) : 100;

        // Get equipment details if selected
        $equipmentDetails = null;
        $selectedEquipment = null;
        if ($this->equipment_id) {
            $selectedEquipment = Equipment::find($this->equipment_id);
            $equipmentDetails = Task::where('equipment_id', $this->equipment_id)
                ->whereBetween('due_date', [$startDate, $endDate])
                ->whereHas('maintCategory', function ($q) {
                    $q->where('name', 'like', '%Preventive%')
                      ->orWhere('name', 'like', '%Periodic%')
                      ->orWhere('name', 'like', '%Predictive%');
                })
                ->with(['maintCategory'])
                ->orderBy('due_date')
                ->get();
        }

        return [
            'summary' => $summary,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedCount,
            'overallSlm' => $overallSlm,
            'monthName' => date('F', mktime(0, 0, 0, $this->month, 1, $this->year)),
            'years' => range(now()->year - 3, now()->year + 2),
            'months' => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ],
            'locations' => Location::orderBy('name')->pluck('name', 'id')->toArray(),
            'selectedEquipment' => $selectedEquipment,
            'equipmentDetails' => $equipmentDetails,
        ];
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner', 'Supervisor', 'Customer']);
    }
}

