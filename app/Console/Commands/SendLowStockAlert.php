<?php

namespace App\Console\Commands;

use App\Models\PartStock;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendLowStockAlert extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:send-low-stock-alert';

    /**
     * The console command description.
     */
    protected $description = 'Send weekly alert for parts below minimum stock levels';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for parts below minimum stock levels...');

        // Find parts where current quantity is below minimum
        $lowStockParts = PartStock::with('supplier')
            ->whereColumn('quantity', '<', 'min_quantity')
            ->orderByRaw('quantity - min_quantity ASC') // Most urgent first
            ->get();

        if ($lowStockParts->isEmpty()) {
            $this->info('No parts below minimum stock levels.');
            return self::SUCCESS;
        }

        $this->info("Found {$lowStockParts->count()} part(s) below minimum stock levels.");

        // Get recipients (Planner, Supervisor, Manager)
        $planners = User::whereHas('role', function ($query) {
            $query->where('name', 'Planner');
        })->get();

        $supervisors = User::whereHas('role', function ($query) {
            $query->where('name', 'Supervisor');
        })->get();

        $managers = User::whereHas('role', function ($query) {
            $query->where('name', 'Manager');
        })->get();

        // Send to planners
        if ($planners->isNotEmpty()) {
            $this->info("Sending to {$planners->count()} Planner(s)...");
            Notification::send(
                $planners,
                new LowStockNotification($lowStockParts)
            );
        }

        // Send to supervisors
        if ($supervisors->isNotEmpty()) {
            $this->info("Sending to {$supervisors->count()} Supervisor(s)...");
            Notification::send(
                $supervisors,
                new LowStockNotification($lowStockParts)
            );
        }

        // Send to managers
        if ($managers->isNotEmpty()) {
            $this->info("Sending to {$managers->count()} Manager(s)...");
            Notification::send(
                $managers,
                new LowStockNotification($lowStockParts)
            );
        }

        $this->info('Low stock alerts sent successfully!');

        return self::SUCCESS;
    }
}
