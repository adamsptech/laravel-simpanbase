<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checklist extends Model
{
    protected $fillable = [
        'type_check_id',
        'name',
        'recommended',
    ];

    public function typeCheck(): BelongsTo
    {
        return $this->belongsTo(TypeCheck::class);
    }
}
