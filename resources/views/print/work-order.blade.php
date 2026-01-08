<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order #{{ $task->id }} - {{ $isPm ? 'PM' : 'CM' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 15mm; }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover { background: #2563eb; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-info h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }
        .wo-info {
            text-align: right;
        }
        .wo-number {
            font-size: 16pt;
            font-weight: bold;
        }
        .wo-type {
            display: inline-block;
            padding: 3px 10px;
            background: {{ $isPm ? '#10b981' : '#f59e0b' }};
            color: white;
            border-radius: 3px;
            font-size: 10pt;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
            width: 25%;
        }
        .section-title {
            background: #1f2937;
            color: white;
            padding: 8px 12px;
            font-size: 12pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
        .checklist-table td {
            padding: 6px 8px;
        }
        .checkbox {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1px solid #333;
            margin-right: 8px;
            vertical-align: middle;
        }
        .checkbox.checked {
            background: #10b981;
            position: relative;
        }
        .checkbox.checked::after {
            content: '✓';
            color: white;
            position: absolute;
            left: 2px;
            top: -2px;
            font-size: 12px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            width: 30%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10pt;
            font-weight: bold;
        }
        .status-open { background: #dbeafe; color: #1d4ed8; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-closed { background: #dcfce7; color: #16a34a; }
        .priority-low { color: #6b7280; }
        .priority-medium { color: #d97706; }
        .priority-high { color: #dc2626; font-weight: bold; }
        .notes-box {
            min-height: 100px;
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <h1>MAINTENANCE WORK ORDER</h1>
            <p>{{ config('app.name', 'Simpanbase') }}</p>
        </div>
        <div class="wo-info">
            <div class="wo-number">WO #{{ $task->id }}</div>
            <div class="wo-type">{{ $isPm ? 'PREVENTIVE MAINTENANCE' : 'CORRECTIVE MAINTENANCE' }}</div>
        </div>
    </div>

    <!-- Work Order Details -->
    <table>
        <tr>
            <th>Equipment</th>
            <td>{{ $task->equipment?->name ?? 'N/A' }}</td>
            <th>Serial Number</th>
            <td>{{ $task->equipment?->serial_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Location</th>
            <td colspan="3">
                {{ $task->equipment?->sublocation?->location?->name ?? $task->location?->name ?? 'N/A' }}
                @if($task->equipment?->sublocation)
                    → {{ $task->equipment->sublocation->name }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Maintenance Type</th>
            <td>{{ $task->maintCategory?->name ?? 'N/A' }}</td>
            <th>Priority</th>
            <td>
                @php
                    $priorityClass = match($task->priority) {
                        1 => 'priority-low',
                        2 => 'priority-medium',
                        3 => 'priority-high',
                        default => '',
                    };
                    $priorityLabel = match($task->priority) {
                        1 => 'Low',
                        2 => 'Medium',
                        3 => 'High',
                        default => 'N/A',
                    };
                @endphp
                <span class="{{ $priorityClass }}">{{ $priorityLabel }}</span>
            </td>
        </tr>
        <tr>
            <th>Assigned To</th>
            <td>{{ $task->assignedUser?->name ?? 'Unassigned' }}</td>
            <th>Supervisor</th>
            <td>{{ $task->supervisor?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Due Date</th>
            <td>{{ $task->due_date?->format('d M Y') ?? 'Not set' }}</td>
            <th>Status</th>
            <td>
                @php
                    $statusClass = match($task->status) {
                        0 => 'status-open',
                        1, 2, 3 => 'status-pending',
                        4 => 'status-closed',
                        default => '',
                    };
                    $statusLabel = match($task->status) {
                        0 => 'Open',
                        1 => 'Pending Supervisor',
                        2 => 'Pending Manager',
                        3 => 'Pending Customer',
                        4 => 'Closed',
                        default => 'Unknown',
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </td>
        </tr>
        <tr>
            <th>Started At</th>
            <td>{{ $task->started_at?->format('d M Y H:i') ?? '-' }}</td>
            <th>Ended At</th>
            <td>{{ $task->ended_at?->format('d M Y H:i') ?? '-' }}</td>
        </tr>
    </table>

    <!-- PM Checklist Section -->
    @if($isPm && $task->typeCheck && $task->typeCheck->checklists->count() > 0)
        <div class="section-title">📋 Checklist Items</div>
        <table class="checklist-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 60%;">Item</th>
                    <th style="width: 15%;">Check</th>
                    <th style="width: 20%;">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->typeCheck->checklists as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>
                            <span class="checkbox"></span> OK
                            &nbsp;&nbsp;
                            <span class="checkbox"></span> NG
                        </td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- CM Details Section -->
    @if(!$isPm && $task->cmDetail)
        <div class="section-title">🔧 Corrective Maintenance Details</div>
        <table>
            <tr>
                <th>Problem Description</th>
                <td colspan="3">{{ $task->cmDetail->problem_description ?? '-' }}</td>
            </tr>
            <tr>
                <th>Root Cause</th>
                <td colspan="3">{{ $task->cmDetail->root_cause ?? '-' }}</td>
            </tr>
            <tr>
                <th>Action Taken</th>
                <td colspan="3">{{ $task->cmDetail->action_taken ?? '-' }}</td>
            </tr>
            <tr>
                <th>Recommendation</th>
                <td colspan="3">{{ $task->cmDetail->recommendation ?? '-' }}</td>
            </tr>
        </table>
    @endif

    <!-- Spare Parts Used -->
    @if($task->partUsages->count() > 0)
        <div class="section-title">🔩 Spare Parts Used</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 30%;">Part Name</th>
                    <th style="width: 20%;">Part ID</th>
                    <th style="width: 20%;">Quantity</th>
                    <th style="width: 20%;">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->partUsages as $index => $usage)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $usage->partStock?->name ?? 'N/A' }}</td>
                        <td>{{ $usage->partStock?->part_id ?? '-' }}</td>
                        <td>{{ $usage->quantity }}</td>
                        <td>{{ $usage->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Work Order Notes -->
    <div class="section-title">📝 Notes</div>
    <div class="notes-box">
        {{ $task->notes ?? '' }}
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong>Engineer</strong><br>
                {{ $task->assignedUser?->name ?? '________________' }}
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Supervisor</strong><br>
                {{ $task->supervisor?->name ?? '________________' }}
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Manager</strong><br>
                ________________
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 9pt; color: #666;">
        Printed on {{ now()->format('d M Y H:i') }} | Work Order #{{ $task->id }}
    </div>
</body>
</html>
