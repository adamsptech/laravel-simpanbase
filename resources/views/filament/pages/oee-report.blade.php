<x-filament-panels::page>
    <style>
        .oee-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 1280px) {
            .oee-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .oee-grid { grid-template-columns: 1fr; }
        }
        .stat-card {
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }
        .dark .stat-card {
            background: #1f2937;
            border-color: rgba(255,255,255,0.1);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        .dark .stat-label { color: #9ca3af; }
        .filter-bar {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            background: #fff;
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .dark .filter-bar { background: #1f2937; }
        .filter-select, .filter-input {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: #fff;
            font-size: 0.875rem;
        }
        .dark .filter-select, .dark .filter-input {
            background: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        .filter-input { min-width: 200px; }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .filter-label {
            font-weight: 500;
            font-size: 0.875rem;
            white-space: nowrap;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .dark .data-table { background: #1f2937; }
        .data-table th {
            background: #f3f4f6;
            padding: 0.75rem 1rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.875rem;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .data-table th:hover { background: #e5e7eb; }
        .dark .data-table th {
            background: #374151;
            color: #f9fafb;
            border-color: #4b5563;
        }
        .dark .data-table th:hover { background: #4b5563; }
        .data-table th:first-child { text-align: left; }
        .data-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.875rem;
            text-align: center;
        }
        .data-table td:first-child { text-align: left; }
        .dark .data-table td { border-color: #374151; }
        .sort-icon { margin-left: 0.25rem; font-size: 0.75rem; }
        .oee-bar {
            background: #e5e7eb;
            border-radius: 9999px;
            height: 8px;
            width: 80px;
            display: inline-block;
            vertical-align: middle;
        }
        .oee-bar-fill {
            border-radius: 9999px;
            height: 100%;
        }
        .clickable-row { cursor: pointer; transition: background 0.15s; }
        .clickable-row:hover { background: #f9fafb; }
        .dark .clickable-row:hover { background: #374151; }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .breakdown-table th, .breakdown-table td {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .breakdown-table th { background: #f9fafb; text-align: left; }
        .breakdown-table td:first-child { text-align: left; font-weight: 500; }
        .btn-reset {
            padding: 0.5rem 1rem;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .btn-reset:hover { background: #4b5563; }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .dark .loading-overlay { background: rgba(31,41,55,0.7); }
    </style>

    <!-- Enhanced Filter Bar with Livewire -->
    <div class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">📅 Period:</span>
            <select wire:model.live="month" class="filter-select">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}">{{ $name }}</option>
                @endforeach
            </select>
            <select wire:model.live="year" class="filter-select">
                @foreach($years as $yr)
                    <option value="{{ $yr }}">{{ $yr }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">📍 Location:</span>
            <select wire:model.live="location_id" class="filter-select">
                <option value="">All Locations</option>
                @foreach($locations as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">🔍 Search:</span>
            <input type="text" wire:model.live.debounce.300ms="search" class="filter-input" 
                   placeholder="Equipment name or serial...">
        </div>

        @if($location_id || $search)
            <button wire:click="resetFilters" class="btn-reset">Reset Filters</button>
        @endif
        
        <div wire:loading class="text-sm text-gray-500" style="margin-left: auto;">
            ⏳ Loading...
        </div>
    </div>

    <!-- Average Statistics Cards -->
    <div class="oee-grid" wire:loading.class="opacity-50">
        <div class="stat-card">
            @php
                $avgProd = $summary['averageActualProduction'] ?? 0;
                $prodColor = $avgProd >= 80 ? '#10b981' : ($avgProd >= 60 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="stat-value" style="color: {{ $prodColor }};">{{ number_format($avgProd, 1) }}%</div>
            <div class="stat-label">Avg. Actual Production</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #3b82f6;">{{ $summary['count'] }}</div>
            <div class="stat-label">Equipment Tracked</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #8b5cf6;">{{ number_format($summary['averageAvailability'] ?? 0, 1) }}%</div>
            <div class="stat-label">Avg. Availability</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #06b6d4;">{{ number_format($summary['averageOee'] ?? 0, 1) }}%</div>
            <div class="stat-label">Avg. OEE (A×P×Q)</div>
        </div>
    </div>

    <!-- Equipment Summary Table (Compact with Sortable Headers) -->
    <div style="background: #fff; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; position: relative;" class="dark:bg-gray-800">
        <div wire:loading.flex class="loading-overlay">
            <span class="text-lg font-medium">Loading...</span>
        </div>
        <table class="data-table" id="oee-table">
            <thead>
                <tr>
                    <th style="text-align: left;" wire:click="sortBy('equipment')">
                        Equipment 
                        @if($sort === 'equipment')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th>Serial #</th>
                    <th wire:click="sortBy('availability')">
                        Availability
                        @if($sort === 'availability')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('performance')">
                        Performance
                        @if($sort === 'performance')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('quality')">
                        Quality
                        @if($sort === 'quality')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('oee')">
                        OEE
                        @if($sort === 'oee')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('actual_production')">
                        Actual Prod %
                        @if($sort === 'actual_production')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary['records'] as $record)
                    <tr class="clickable-row" onclick="toggleDetails({{ $record->id }})">
                        <td>🏭 {{ $record->equipment?->name ?? 'Unknown' }}</td>
                        <td>{{ $record->equipment?->serial_number ?? 'N/A' }}</td>
                        <td>{{ number_format($record->availability ?? 0, 1) }}%</td>
                        <td>{{ number_format($record->performance ?? 0, 1) }}%</td>
                        <td>{{ number_format($record->quality ?? 0, 1) }}%</td>
                        <td>
                            @php
                                $oee = $record->oee_percentage ?? 0;
                                $oeeColor = $oee >= 85 ? '#10b981' : ($oee >= 70 ? '#22c55e' : ($oee >= 55 ? '#f59e0b' : '#ef4444'));
                            @endphp
                            <span class="oee-bar"><span class="oee-bar-fill" style="width: {{ min($oee, 100) }}%; background: {{ $oeeColor }};"></span></span>
                            <strong style="color: {{ $oeeColor }};">{{ number_format($oee, 1) }}%</strong>
                        </td>
                        <td>{{ number_format($record->actual_production_percentage ?? 0, 1) }}%</td>
                        <td>
                            @if(in_array(auth()->user()?->role?->name, ['Admin', 'Manager', 'Planner']))
                                <a href="/panels/oee-monthlies/{{ $record->id }}/edit" onclick="event.stopPropagation();" style="color: #3b82f6; text-decoration: none;">✏️</a>
                            @endif
                        </td>
                    </tr>
                    <tr id="detail-{{ $record->id }}" style="display: none;">
                        <td colspan="8" style="padding: 1rem; background: #f9fafb;">
                            <strong>📊 Breakdown for {{ $record->equipment?->name }}</strong>
                            <table class="breakdown-table">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Days</th>
                                        <th>Hours</th>
                                        <th>Minutes</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Monthly Calendar</td>
                                        <td>{{ $record->working_days }}</td>
                                        <td>{{ $record->working_hours }}</td>
                                        <td>{{ $record->working_minutes }}</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>Plant Operating</td>
                                        <td>{{ $record->plant_operating_days }}</td>
                                        <td>{{ $record->plant_operating_hours }}</td>
                                        <td>{{ $record->plant_operating_minutes }}</td>
                                        <td>{{ number_format($record->plant_operating_percentage, 2) }}%</td>
                                    </tr>
                                    <tr style="background: #fef3c7;">
                                        <td>Planned Maintenance</td>
                                        <td>{{ $record->planned_maintenance_days }}</td>
                                        <td>{{ $record->planned_maintenance_hours }}</td>
                                        <td>{{ $record->planned_maintenance_minutes }}</td>
                                        <td>{{ number_format($record->planned_maintenance_percentage, 2) }}%</td>
                                    </tr>
                                    <tr>
                                        <td>Plant Production</td>
                                        <td>{{ $record->plant_production_days }}</td>
                                        <td>{{ $record->plant_production_hours }}</td>
                                        <td>{{ $record->plant_production_minutes }}</td>
                                        <td>{{ number_format($record->plant_production_percentage, 2) }}%</td>
                                    </tr>
                                    <tr style="background: #fee2e2;">
                                        <td>Unplanned Maintenance</td>
                                        <td>{{ $record->unplanned_maintenance_days }}</td>
                                        <td>{{ $record->unplanned_maintenance_hours }}</td>
                                        <td>{{ $record->unplanned_maintenance_minutes }}</td>
                                        <td>{{ number_format($record->unplanned_maintenance_percentage, 2) }}%</td>
                                    </tr>
                                    <tr style="background: #d1fae5; font-weight: 600;">
                                        <td>Actual Production</td>
                                        <td>{{ $record->actual_production_days }}</td>
                                        <td>{{ $record->actual_production_hours }}</td>
                                        <td>{{ $record->actual_production_minutes }}</td>
                                        <td>{{ number_format($record->actual_production_percentage, 2) }}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($summary['count'] == 0)
        <div style="background: #fff; border-radius: 0.75rem; padding: 3rem; text-align: center; color: #9ca3af;">
            No OEE data for {{ $monthName }} {{ $year }}. 
            <a href="/panels/oee-monthlies/create" style="color: #3b82f6;">Add OEE Data</a>
        </div>
    @endif

    <script>
        function toggleDetails(id) {
            const row = document.getElementById('detail-' + id);
            if (row) {
                row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
            }
        }
    </script>
</x-filament-panels::page>
