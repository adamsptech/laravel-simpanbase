<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartAddress extends Model
{
    protected $fillable = [
        'name',
    ];

    public function partStocks(): HasMany
    {
        return $this->hasMany(PartStock::class, 'address_id');
    }
}
