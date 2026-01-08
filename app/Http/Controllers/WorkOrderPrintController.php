<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class WorkOrderPrintController extends Controller
{
    /**
     * Display a printable work order
     */
    public function show(Task $task)
    {
        $task->load([
            'equipment',
            'equipment.sublocation',
            'equipment.sublocation.location',
            'maintCategory',
            'assignedUser',
            'supervisor',
            'location',
            'typeCheck',
            'typeCheck.checklists',
            'taskDetails',
            'cmDetail',
            'partUsages',
            'partUsages.partStock',
        ]);

        // Determine if PM or CM based on maintenance category
        $isPm = str_contains(strtolower($task->maintCategory?->name ?? ''), 'preventive');

        return view('print.work-order', [
            'task' => $task,
            'isPm' => $isPm,
        ]);
    }

    /**
     * Generate PDF of work order
     */
    public function pdf(Task $task)
    {
        // For now, just redirect to print view
        // You can add a PDF library like dompdf later
        return $this->show($task);
    }
}
