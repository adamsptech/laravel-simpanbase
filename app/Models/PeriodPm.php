<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodPm extends Model
{
    protected $fillable = [
        'name',
        'days',
    ];

    public function typeChecks(): HasMany
    {
        return $this->hasMany(TypeCheck::class, 'period_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'period_id');
    }
}
