<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
    ];

    public function sublocations(): HasMany
    {
        return $this->hasMany(Sublocation::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
