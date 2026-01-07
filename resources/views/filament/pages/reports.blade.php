<x-filament-panels::page>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1280px) {
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }
        .dark .stat-card {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border-color: rgba(255,255,255,0.1);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dark .stat-label {
            color: #9ca3af;
        }
        .section-card {
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .dark .section-card {
            background: #1f2937;
            border-color: rgba(255,255,255,0.1);
        }
        .section-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            font-size: 1rem;
            color: #111827;
        }
        .dark .section-header {
            border-color: rgba(255,255,255,0.1);
            color: #f9fafb;
        }
        .section-content {
            padding: 1.5rem;
        }
        .breakdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .breakdown-item {
            border-color: rgba(255,255,255,0.05);
        }
        .breakdown-item:last-child {
            border-bottom: none;
        }
        .breakdown-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 0.75rem;
        }
        .breakdown-label {
            display: flex;
            align-items: center;
            color: #374151;
        }
        .dark .breakdown-label {
            color: #d1d5db;
        }
        .breakdown-value {
            font-weight: 600;
            color: #111827;
        }
        .dark .breakdown-value {
            color: #f9fafb;
        }
        .category-item {
            background: #f9fafb;
            border-radius: 0.5rem;
            padding: 1.25rem;
            text-align: center;
        }
        .dark .category-item {
            background: #374151;
        }
        .category-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
        }
        .dark .category-value {
            color: #f9fafb;
        }
        .category-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        .dark .category-label {
            color: #9ca3af;
        }
        .trend-table {
            width: 100%;
            border-collapse: collapse;
        }
        .trend-table th {
            text-align: left;
            padding: 0.75rem;
            font-weight: 500;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
        }
        .dark .trend-table th {
            color: #9ca3af;
            border-color: rgba(255,255,255,0.1);
        }
        .trend-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .trend-table td {
            border-color: rgba(255,255,255,0.05);
        }
        .text-right {
            text-align: right;
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Period Info -->
        <div class="section-card">
            <div class="section-content" style="padding: 1rem 1.5rem;">
                <span style="font-weight: 500; color: #374151;">📅 Period:</span>
                <span style="color: #6b7280; margin-left: 0.5rem;">{{ $currentPeriod }}</span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value" style="color: #111827;">{{ $statistics['total'] }}</div>
                <div class="stat-label">Total Tasks</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #10b981;">{{ $statistics['completed'] }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #3b82f6;">{{ $statistics['open'] }}</div>
                <div class="stat-label">Open</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #f59e0b;">{{ $statistics['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #ef4444;">{{ $statistics['overdue'] }}</div>
                <div class="stat-label">Overdue</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #6366f1;">{{ $statistics['completionRate'] }}%</div>
                <div class="stat-label">Completion Rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #8b5cf6;">{{ $statistics['avgCompletionDays'] }}</div>
                <div class="stat-label">Avg Days</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <!-- Status Breakdown -->
            <div class="section-card">
                <div class="section-header">📊 Status Breakdown</div>
                <div class="section-content">
                    @forelse($statusBreakdown as $status)
                        <div class="breakdown-item">
                            <div class="breakdown-label">
                                <div class="breakdown-dot" style="background-color: {{ $status['color'] }};"></div>
                                {{ $status['status'] }}
                            </div>
                            <div class="breakdown-value">{{ $status['count'] }}</div>
                        </div>
                    @empty
                        <p style="color: #9ca3af; text-align: center;">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Priority Breakdown -->
            <div class="section-card">
                <div class="section-header">🎯 Priority Breakdown</div>
                <div class="section-content">
                    @forelse($priorityBreakdown as $priority)
                        <div class="breakdown-item">
                            <div class="breakdown-label">
                                <div class="breakdown-dot" style="background-color: {{ $priority['color'] }};"></div>
                                {{ $priority['priority'] }}
                            </div>
                            <div class="breakdown-value">{{ $priority['count'] }}</div>
                        </div>
                    @empty
                        <p style="color: #9ca3af; text-align: center;">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="section-card">
            <div class="section-header">🔧 Tasks by Maintenance Type</div>
            <div class="section-content">
                @if(count($categoryBreakdown) > 0)
                    <div class="category-grid">
                        @foreach($categoryBreakdown as $category)
                            <div class="category-item">
                                <div class="category-value">{{ $category['count'] }}</div>
                                <div class="category-label">{{ $category['name'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #9ca3af; text-align: center;">No data available</p>
                @endif
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="section-card">
            <div class="section-header">📈 12-Month Trend</div>
            <div class="section-content" style="overflow-x: auto;">
                <table class="trend-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Completed</th>
                            <th class="text-right">Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyTrend as $month)
                            <tr>
                                <td style="color: #374151;">{{ $month['month'] }}</td>
                                <td class="text-right" style="font-weight: 500; color: #111827;">{{ $month['total'] }}</td>
                                <td class="text-right" style="color: #10b981;">{{ $month['completed'] }}</td>
                                <td class="text-right">
                                    @if($month['total'] > 0)
                                        <span style="color: #6366f1; font-weight: 500;">{{ round(($month['completed'] / $month['total']) * 100, 1) }}%</span>
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
