<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use Auditable;

    protected $table = 'equipment';
    
    protected $fillable = [
        'sublocation_id',
        'supplier_id',
        'name',
        'serial_number',
        'category',
        'notes',
        'warranty_expiry_date',
    ];

    protected $casts = [
        'warranty_expiry_date' => 'date',
    ];

    public function sublocation(): BelongsTo
    {
        return $this->belongsTo(Sublocation::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function typeChecks(): HasMany
    {
        return $this->hasMany(TypeCheck::class);
    }

    public function partStocks(): HasMany
    {
        return $this->hasMany(PartStock::class);
    }

    // Accessor to get full location path
    public function getFullLocationAttribute(): string
    {
        $location = $this->sublocation?->location?->name ?? '';
        $sublocation = $this->sublocation?->name ?? '';
        return trim("$location / $sublocation", ' /');
    }
}
