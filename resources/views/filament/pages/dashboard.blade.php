<x-filament-panels::page>
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1280px) {
            .dashboard-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .dark .stat-card {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border-color: rgba(255,255,255,0.1);
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        .dark .stat-label { color: #9ca3af; }
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dark .section-header {
            border-color: rgba(255,255,255,0.1);
            color: #f9fafb;
        }
        .section-content { padding: 1rem 1.5rem; }
        .task-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .task-row { border-color: rgba(255,255,255,0.05); }
        .task-row:last-child { border-bottom: none; }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-amber { background: #fef3c7; color: #d97706; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-gray { background: #f3f4f6; color: #4b5563; }
        .dark .badge-blue { background: #1e3a5f; color: #93c5fd; }
        .dark .badge-amber { background: #4a3728; color: #fcd34d; }
        .dark .badge-green { background: #14532d; color: #86efac; }
        .dark .badge-red { background: #450a0a; color: #fca5a5; }
        .dark .badge-gray { background: #374151; color: #d1d5db; }
        .two-col-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        @media (max-width: 1024px) {
            .two-col-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Welcome Banner -->
        <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
            <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">👋 Welcome back!</h2>
            <p style="opacity: 0.9;">You are logged in as <strong>{{ $role }}</strong>. Here's your maintenance overview.</p>
        </div>

        <!-- Main Statistics -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-value" style="color: #3b82f6;">{{ $openTasks }}</div>
                <div class="stat-label">Open Work Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #f59e0b;">{{ $pendingApproval }}</div>
                <div class="stat-label">Pending Approval</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #ef4444;">{{ $overdueTasks }}</div>
                <div class="stat-label">Overdue</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #10b981;">{{ $closedTasks }}</div>
                <div class="stat-label">Closed This Month</div>
            </div>
        </div>

        <!-- This Month Progress -->
        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="font-weight: 600; color: #374151;">📊 This Month's Progress</span>
                <span style="color: #6b7280;">{{ $thisMonthCompleted }} / {{ $thisMonthTotal }} completed</span>
            </div>
            <div style="background: #e5e7eb; border-radius: 9999px; height: 8px; overflow: hidden;">
                <div style="background: linear-gradient(90deg, #10b981, #34d399); height: 100%; width: {{ $completionRate }}%; transition: width 0.5s ease;"></div>
            </div>
            <div style="text-align: right; margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                <strong style="color: #10b981;">{{ $completionRate }}%</strong> completion rate
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-col-grid">
            <!-- Upcoming Due -->
            <div class="section-card">
                <div class="section-header">⏰ Upcoming Due (Next 7 Days)</div>
                <div class="section-content">
                    @forelse($upcomingDue as $task)
                        <div class="task-row">
                            <div>
                                <a href="/panels/tasks/{{ $task->id }}" style="color: #3b82f6; text-decoration: none; font-weight: 500;">
                                    WO #{{ $task->id }}
                                </a>
                                <span style="color: #6b7280; margin-left: 0.5rem;">{{ $task->equipment?->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                @php
                                    $daysUntil = now()->diffInDays($task->due_date, false);
                                    $badgeClass = $daysUntil <= 1 ? 'badge-red' : ($daysUntil <= 3 ? 'badge-amber' : 'badge-blue');
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $task->due_date->format('M j') }}
                                    @if($daysUntil == 0) (Today)
                                    @elseif($daysUntil == 1) (Tomorrow)
                                    @else ({{ $daysUntil }} days)
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <p style="color: #9ca3af; text-align: center; padding: 2rem;">No upcoming tasks in the next 7 days 🎉</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Work Orders -->
            <div class="section-card">
                <div class="section-header">📋 Recent Work Orders</div>
                <div class="section-content">
                    @forelse($recentTasks as $task)
                        <div class="task-row">
                            <div>
                                <a href="/panels/tasks/{{ $task->id }}" style="color: #3b82f6; text-decoration: none; font-weight: 500;">
                                    WO #{{ $task->id }}
                                </a>
                                <span style="color: #6b7280; margin-left: 0.5rem;">{{ Str::limit($task->equipment?->name ?? 'N/A', 20) }}</span>
                            </div>
                            <div>
                                @php
                                    $statusBadge = match($task->status) {
                                        0 => ['Open', 'badge-blue'],
                                        1, 2, 3 => ['Pending', 'badge-amber'],
                                        4 => ['Closed', 'badge-green'],
                                        default => ['Unknown', 'badge-gray'],
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge[1] }}">{{ $statusBadge[0] }}</span>
                            </div>
                        </div>
                    @empty
                        <p style="color: #9ca3af; text-align: center; padding: 2rem;">No recent work orders</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Stats for Admin/Planner -->
        @if(in_array($role, ['Admin', 'Planner', 'Manager']))
        <div class="dashboard-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card">
                <div class="stat-value" style="color: #8b5cf6;">{{ $equipmentCount }}</div>
                <div class="stat-label">Total Equipment</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #06b6d4;">{{ $userCount }}</div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #ec4899;">{{ $totalTasks }}</div>
                <div class="stat-label">Total Work Orders</div>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
