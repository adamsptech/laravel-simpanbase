<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartUsage extends Model
{
    protected $fillable = [
        'task_id',
        'part_stock_id',
        'quantity',
        'stock_after',
        'status',
        'picked_by',
        'picked_at',
        'notes',
    ];

    protected $casts = [
        'picked_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = -1;

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function partStock(): BelongsTo
    {
        return $this->belongsTo(PartStock::class);
    }

    public function pickedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'picked_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }
}
