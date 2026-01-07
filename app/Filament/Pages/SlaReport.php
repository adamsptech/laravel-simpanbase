<?php

namespace App\Filament\Pages;

use App\Models\MachineDowntime;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class SlaReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static \UnitEnum|string|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 21;

    protected static ?string $navigationLabel = 'SLA Report';

    protected string $view = 'filament.pages.sla-report';

    public ?int $year = null;
    public ?int $month = null;

    public function mount(): void
    {
        $this->year = (int) request()->get('year', now()->year);
        $this->month = (int) request()->get('month', now()->month);
    }

    public function getTitle(): string|Htmlable
    {
        return 'SLA / Availability Report';
    }

    public function getViewData(): array
    {
        $summary = MachineDowntime::getAvailabilitySummary($this->year, $this->month);
        $statistics = MachineDowntime::getStatistics($this->year, $this->month);

        return [
            'summary' => $summary,
            'statistics' => $statistics,
            'currentYear' => $this->year,
            'currentMonth' => $this->month,
            'monthName' => date('F', mktime(0, 0, 0, $this->month, 1, $this->year)),
            'years' => range(now()->year - 3, now()->year + 1),
            'months' => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ],
        ];
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner', 'Supervisor']);
    }
}
