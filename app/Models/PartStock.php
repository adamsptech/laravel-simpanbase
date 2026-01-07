<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartStock extends Model
{
    protected $fillable = [
        'part_id',
        'sap_id',
        'name',
        'quantity',
        'min_quantity',
        'price',
        'supplier_id',
        'address_id',
        'equipment_id',
        'is_obsolete',
        'image',
        'reminder_days',
        'last_reminder_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'min_quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'is_obsolete' => 'boolean',
        'last_reminder_at' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(PartAddress::class, 'address_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function partUsages(): HasMany
    {
        return $this->hasMany(PartUsage::class);
    }

    public function partAdditions(): HasMany
    {
        return $this->hasMany(PartAddition::class);
    }

    // Check if stock is low
    public function getIsLowStockAttribute(): bool
    {
        if (!$this->min_quantity) return false;
        return $this->quantity <= $this->min_quantity;
    }
}
