<x-filament-panels::page>
    <style>
        .sla-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 1280px) {
            .sla-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .sla-grid { grid-template-columns: 1fr; }
        }
        .stat-card {
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
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
        }
        .filter-select {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: #fff;
            font-size: 0.875rem;
        }
        .dark .filter-select {
            background: #374151;
            border-color: #4b5563;
            color: #f9fafb;
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
        }
        .dark .data-table th {
            background: #374151;
            color: #f9fafb;
            border-color: #4b5563;
        }
        .data-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.875rem;
        }
        .dark .data-table td { border-color: #374151; }
        .availability-bar {
            width: 100px;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
            margin-right: 0.5rem;
        }
        .availability-fill {
            height: 100%;
            border-radius: 4px;
        }
        .badge-green { color: #10b981; }
        .badge-amber { color: #f59e0b; }
        .badge-red { color: #ef4444; }
    </style>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <span style="font-weight: 500;">📅 Period:</span>
        <form method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <select name="month" class="filter-select" onchange="this.form.submit()">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="year" class="filter-select" onchange="this.form.submit()">
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ $currentYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </form>
        <span style="color: #6b7280; font-size: 0.875rem;">Showing data for {{ $monthName }} {{ $currentYear }}</span>
    </div>

    <!-- Statistics Cards -->
    <div class="sla-grid">
        <div class="stat-card">
            <div class="stat-value" style="color: #ef4444;">{{ $statistics['totalFrequency'] }}</div>
            <div class="stat-label">Total Incidents</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #10b981;">{{ $statistics['averageAvailability'] }}%</div>
            <div class="stat-label">Average Availability</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #f59e0b;">
                @php
                    $hours = floor($statistics['totalDowntimeMinutes'] / 60);
                    $mins = $statistics['totalDowntimeMinutes'] % 60;
                @endphp
                {{ sprintf('%02d:%02d', $hours, $mins) }}
            </div>
            <div class="stat-label">Total Downtime (HH:MM)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #3b82f6; font-size: 1rem;">
                {{ $statistics['lastDowntime'] ? $statistics['lastDowntime']->format('d M Y H:i') : 'No incidents' }}
            </div>
            <div class="stat-label">Last Incident</div>
        </div>
    </div>

    <!-- Availability Table -->
    <div style="background: #fff; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" class="dark:bg-gray-800">
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; font-weight: 600;">
            📊 Equipment Availability Summary
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Serial Number</th>
                    <th style="text-align: center;">Incidents</th>
                    <th style="text-align: center;">Total Downtime</th>
                    <th style="text-align: center;">Availability %</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary as $item)
                    @php
                        $availability = $item->availability_percentage;
                        $colorClass = $availability >= 95 ? 'badge-green' : ($availability >= 85 ? 'badge-amber' : 'badge-red');
                        $barColor = $availability >= 95 ? '#10b981' : ($availability >= 85 ? '#f59e0b' : '#ef4444');
                        $hours = floor($item->total_downtime_minutes / 60);
                        $mins = $item->total_downtime_minutes % 60;
                    @endphp
                    <tr>
                        <td style="font-weight: 500;">{{ $item->equipment_name }}</td>
                        <td style="color: #6b7280;">{{ $item->serial_number ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $item->frequency }}</td>
                        <td style="text-align: center;">{{ sprintf('%02d:%02d', $hours, $mins) }}</td>
                        <td style="text-align: center;">
                            <div class="availability-bar">
                                <div class="availability-fill" style="width: {{ $availability }}%; background: {{ $barColor }};"></div>
                            </div>
                            <span class="{{ $colorClass }}" style="font-weight: 600;">{{ $availability }}%</span>
                        </td>
                        <td>
                            @if($availability >= 95)
                                <span style="color: #10b981;">✅ Excellent</span>
                            @elseif($availability >= 85)
                                <span style="color: #f59e0b;">⚠️ Warning</span>
                            @else
                                <span style="color: #ef4444;">🔴 Critical</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #9ca3af;">
                            No equipment data available. Add equipment first.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
