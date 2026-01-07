<x-filament-panels::page>
    <style>
        .slm-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 1280px) {
            .slm-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .slm-grid { grid-template-columns: 1fr; }
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
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            user-select: none;
        }
        .data-table th:hover { background: #e5e7eb; }
        .dark .data-table th {
            background: #374151;
            color: #f9fafb;
            border-color: #4b5563;
        }
        .dark .data-table th:hover { background: #4b5563; }
        .data-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.875rem;
        }
        .dark .data-table td { border-color: #374151; }
        .sort-icon { margin-left: 0.25rem; font-size: 0.75rem; }
        .slm-bar {
            width: 100px;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
            margin-right: 0.5rem;
        }
        .slm-fill {
            height: 100%;
            border-radius: 4px;
        }
        .badge-green { color: #10b981; }
        .badge-amber { color: #f59e0b; }
        .badge-red { color: #ef4444; }
        .clickable-row { cursor: pointer; transition: background 0.2s; }
        .clickable-row:hover { background: #f0fdf4 !important; }
        .dark .clickable-row:hover { background: #1e3a5f !important; }
        .detail-section {
            background: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        .dark .detail-section { background: #1e3a5f; border-color: #22c55e; }
        .back-link {
            color: #22c55e;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin-bottom: 1rem;
            cursor: pointer;
        }
        .back-link:hover { text-decoration: underline; }
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

    <!-- Statistics Cards -->
    <div class="slm-grid" wire:loading.class="opacity-50">
        <div class="stat-card">
            <div class="stat-value" style="color: #3b82f6;">{{ $totalTasks }}</div>
            <div class="stat-label">Total PM Tasks</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #10b981;">{{ $completedTasks }}</div>
            <div class="stat-label">Completed Tasks</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #f59e0b;">{{ $totalTasks - $completedTasks }}</div>
            <div class="stat-label">Pending/Open</div>
        </div>
        <div class="stat-card">
            @php
                $slmColor = $overallSlm >= 90 ? '#10b981' : ($overallSlm >= 70 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="stat-value" style="color: {{ $slmColor }};">{{ $overallSlm }}%</div>
            <div class="stat-label">Overall SLM</div>
        </div>
    </div>

    <!-- Equipment Detail View (if selected) -->
    @if($selectedEquipment && $equipmentDetails)
        <div class="detail-section">
            <span wire:click="backToSummary" class="back-link">
                ← Back to Summary
            </span>
            <h3 style="margin: 0 0 1rem 0; font-size: 1.25rem; font-weight: 600;">
                📋 PM Details: {{ $selectedEquipment->name }} 
                <span style="color: #6b7280; font-weight: normal;">({{ $selectedEquipment->serial_number ?? 'N/A' }})</span>
            </h3>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Maint. Type</th>
                        <th>Title</th>
                        <th style="text-align: center;">Start</th>
                        <th style="text-align: center;">End</th>
                        <th style="text-align: center;">Duration</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipmentDetails as $task)
                        @php
                            $duration = $task->started_at && $task->ended_at 
                                ? $task->started_at->diffInMinutes($task->ended_at) 
                                : 0;
                            $hours = floor($duration / 60);
                            $mins = $duration % 60;
                        @endphp
                        <tr>
                            <td>{{ $task->maintCategory?->name ?? 'N/A' }}</td>
                            <td style="font-weight: 500;">{{ $task->notes ?? 'PM Task' }}</td>
                            <td style="text-align: center;">{{ $task->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td style="text-align: center;">{{ $task->ended_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td style="text-align: center;">{{ $duration > 0 ? sprintf('%02d:%02d', $hours, $mins) : '-' }}</td>
                            <td style="text-align: center;">
                                @if($task->status === 4)
                                    <span style="color: #10b981;">✅ Closed</span>
                                @elseif($task->status >= 1)
                                    <span style="color: #f59e0b;">⏳ In Progress</span>
                                @else
                                    <span style="color: #6b7280;">📋 Open</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #9ca3af;">
                                No PM tasks for this equipment in {{ $monthName }} {{ $year }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Equipment SLM Summary Table -->
    <div style="background: #fff; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 1rem; position: relative;" class="dark:bg-gray-800">
        <div wire:loading.flex class="loading-overlay">
            <span class="text-lg font-medium">Loading...</span>
        </div>
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; font-weight: 600;">
            📊 Equipment SLM Summary <span style="font-weight: normal; color: #6b7280;">(Click a row to view PM details)</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th wire:click="sortBy('equipment')">
                        Equipment
                        @if($sort === 'equipment')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th>Serial Number</th>
                    <th style="text-align: center;" wire:click="sortBy('frequency')">
                        Frequency
                        @if($sort === 'frequency')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th style="text-align: center;" wire:click="sortBy('actual_time')">
                        Actual Time
                        @if($sort === 'actual_time')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th style="text-align: center;">Requested Time</th>
                    <th style="text-align: center;" wire:click="sortBy('slm')">
                        SLM %
                        @if($sort === 'slm')
                            <span class="sort-icon">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary as $item)
                    @php
                        $slm = $item->slm_percentage;
                        $colorClass = $slm >= 90 ? 'badge-green' : ($slm >= 70 ? 'badge-amber' : 'badge-red');
                        $barColor = $slm >= 90 ? '#10b981' : ($slm >= 70 ? '#f59e0b' : '#ef4444');
                        $actualHours = floor($item->actual_minutes / 60);
                        $actualMins = $item->actual_minutes % 60;
                        $reqHours = floor($item->requested_minutes / 60);
                        $reqMins = $item->requested_minutes % 60;
                        $isSelected = $equipment_id == $item->equipment_id;
                    @endphp
                    <tr class="clickable-row" 
                        wire:click="viewEquipment({{ $item->equipment_id }})"
                        style="{{ $isSelected ? 'background: #dcfce7;' : '' }}">
                        <td style="font-weight: 500;">
                            {{ $item->equipment_name }}
                            @if($isSelected)
                                <span style="color: #22c55e;">✓</span>
                            @endif
                        </td>
                        <td style="color: #6b7280;">{{ $item->serial_number ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $item->frequency }}</td>
                        <td style="text-align: center;">{{ sprintf('%02d:%02d', $actualHours, $actualMins) }}</td>
                        <td style="text-align: center;">{{ sprintf('%02d:%02d', $reqHours, $reqMins) }}</td>
                        <td style="text-align: center;">
                            <div class="slm-bar">
                                <div class="slm-fill" style="width: {{ min($slm, 100) }}%; background: {{ $barColor }};"></div>
                            </div>
                            <span class="{{ $colorClass }}" style="font-weight: 600;">{{ $slm }}%</span>
                        </td>
                        <td>
                            @if($slm >= 90)
                                <span style="color: #10b981;">✅ Good</span>
                            @elseif($slm >= 70)
                                <span style="color: #f59e0b;">⚠️ Warning</span>
                            @else
                                <span style="color: #ef4444;">🔴 Critical</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: #9ca3af;">
                            No equipment data available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
