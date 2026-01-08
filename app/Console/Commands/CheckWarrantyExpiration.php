<?php

namespace App\Console\Commands;

use App\Models\Equipment;
use App\Models\User;
use App\Notifications\WarrantyExpirationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckWarrantyExpiration extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:check-warranty-expiration';

    /**
     * The console command description.
     */
    protected $description = 'Check for equipment with warranties expiring within 60 days and send alerts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for equipment with expiring warranties...');

        // Find equipment with warranty expiring in the next 60 days
        $expiringEquipment = Equipment::with('sublocation')
            ->whereNotNull('warranty_expiry_date')
            ->whereDate('warranty_expiry_date', '>=', now())
            ->whereDate('warranty_expiry_date', '<=', now()->addDays(60))
            ->orderBy('warranty_expiry_date')
            ->get();

        if ($expiringEquipment->isEmpty()) {
            $this->info('No equipment with expiring warranties found.');
            return self::SUCCESS;
        }

        $this->info("Found {$expiringEquipment->count()} equipment item(s) with expiring warranties.");

        // Get recipients (Manager and Supervisor)
        $managers = User::whereHas('role', function ($query) {
            $query->where('name', 'Manager');
        })->get();

        $supervisors = User::whereHas('role', function ($query) {
            $query->where('name', 'Supervisor');
        })->get();

        // Send to managers
        if ($managers->isNotEmpty()) {
            $this->info("Sending to {$managers->count()} Manager(s)...");
            Notification::send(
                $managers,
                new WarrantyExpirationNotification($expiringEquipment)
            );
        }

        // Send to supervisors
        if ($supervisors->isNotEmpty()) {
            $this->info("Sending to {$supervisors->count()} Supervisor(s)...");
            Notification::send(
                $supervisors,
                new WarrantyExpirationNotification($expiringEquipment)
            );
        }

        $this->info('Warranty expiration alerts sent successfully!');

        return self::SUCCESS;
    }
}
