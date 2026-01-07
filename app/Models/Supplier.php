<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'fax',
        'pic',
        'email',
    ];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function partStocks(): HasMany
    {
        return $this->hasMany(PartStock::class);
    }
}
