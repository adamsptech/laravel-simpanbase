<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeCheck extends Model
{
    protected $fillable = [
        'equipment_id',
        'period_id',
        'name',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PeriodPm::class, 'period_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class, 'type_check_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'type_check_id');
    }
}
