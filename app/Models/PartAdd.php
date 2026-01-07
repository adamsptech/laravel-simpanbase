<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartAdd extends Model
{
    protected $fillable = [
        'part_stock_id',
        'quantity',
        'price',
        'opb_number',
        'serial_number',
        'supplier_id',
        'stock_after',
        'added_by',
        'notes',
    ];

    protected $casts = [
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
}
