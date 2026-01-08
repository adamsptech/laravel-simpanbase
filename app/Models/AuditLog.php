<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'model_type',
        'model_id',
        'model_label',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Action constants
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // Get human-readable action label
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_CREATED => 'Created',
            self::ACTION_UPDATED => 'Updated',
            self::ACTION_DELETED => 'Deleted',
            default => ucfirst($this->action),
        };
    }

    // Get short model type (without namespace)
    public function getModelNameAttribute(): string
    {
        return class_basename($this->model_type);
    }

    // Get changed fields for display
    public function getChangedFieldsAttribute(): array
    {
        if ($this->action !== self::ACTION_UPDATED) {
            return [];
        }

        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];

        $changed = [];
        foreach ($new as $field => $value) {
            if (isset($old[$field]) && $old[$field] !== $value) {
                $changed[$field] = [
                    'old' => $old[$field],
                    'new' => $value,
                ];
            }
        }

        return $changed;
    }
}
