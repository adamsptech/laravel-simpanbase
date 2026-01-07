<?php

namespace App\Filament\Pages;

use App\Models\Location;
use App\Models\OeeMonthly;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;

class OeeReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static \UnitEnum|string|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 23;

    protected static ?string $navigationLabel = 'OEE Report';

    protected string $view = 'filament.pages.oee-report';

    // Livewire reactive properties with URL binding
    #[Url(as: 'year')]
    public ?int $year = null;
    
    #[Url(as: 'month')]
    public ?int $month = null;
    
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
        // Set defaults if not in URL
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

    public function getTitle(): string|Htmlable
    {
        return 'OEE (Overall Equipment Effectiveness) Report';
    }

    public function getViewData(): array
    {
        $summary = OeeMonthly::getMonthlySummary(
            $this->year, 
            $this->month, 
            $this->location_id, 
            $this->sort, 
            $this->direction,
            $this->search ?: null
        );

        return [
            'summary' => $summary,
            'monthName' => date('F', mktime(0, 0, 0, $this->month, 1, $this->year)),
            'years' => range(now()->year - 3, now()->year + 2),
            'months' => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ],
            'locations' => Location::orderBy('name')->pluck('name', 'id')->toArray(),
        ];
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner']);
    }
}
