<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartAddition extends Model
{
    protected $fillable = [
        'part_stock_id',
        'quantity',
        'price',
        'opb_number',
        'supplier_id',
        'add_date',
        'current_stock_after',
        'added_by',
        'notes',
    ];

    protected $casts = [
        'add_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function partStock(): BelongsTo
    {
        return $this->belongsTo(PartStock::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Boot the model and register observers
     */
    protected static function booted(): void
    {
        // When a new addition is created, update the part stock quantity
        static::created(function (PartAddition $addition) {
            $partStock = $addition->partStock;
            if ($partStock) {
                $partStock->increment('quantity', $addition->quantity);
            }
        });

        // If an addition is deleted, reverse the quantity
        static::deleted(function (PartAddition $addition) {
            $partStock = $addition->partStock;
            if ($partStock) {
                $partStock->decrement('quantity', $addition->quantity);
            }
        });
    }
}
