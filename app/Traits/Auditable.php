<?php

namespace App\Traits;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Sublocation;
use App\Models\MaintCategory;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Mapping of foreign key fields to their related models and display fields
     */
    protected static function getRelationshipMap(): array
    {
        return [
            'assigned_to' => ['model' => User::class, 'field' => 'name'],
            'supervisor_id' => ['model' => User::class, 'field' => 'name'],
            'user_id' => ['model' => User::class, 'field' => 'name'],
            'submitted_by' => ['model' => User::class, 'field' => 'name'],
            'equipment_id' => ['model' => Equipment::class, 'field' => 'name'],
            'location_id' => ['model' => Location::class, 'field' => 'name'],
            'sublocation_id' => ['model' => Sublocation::class, 'field' => 'name'],
            'maint_category_id' => ['model' => MaintCategory::class, 'field' => 'name'],
        ];
    }

    /**
     * Boot the Auditable trait
     */
    public static function bootAuditable(): void
    {
        // Log created events
        static::created(function (Model $model) {
            static::logAudit($model, AuditLog::ACTION_CREATED);
        });

        // Log updated events
        static::updated(function (Model $model) {
            // Only log if there are actual changes
            if ($model->getChanges()) {
                static::logAudit($model, AuditLog::ACTION_UPDATED);
            }
        });

        // Log deleted events
        static::deleted(function (Model $model) {
            static::logAudit($model, AuditLog::ACTION_DELETED);
        });
    }

    /**
     * Create an audit log entry
     */
    protected static function logAudit(Model $model, string $action): void
    {
        $user = auth()->user();
        $request = request();

        AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'model_label' => static::getAuditLabel($model),
            'old_values' => $action === AuditLog::ACTION_UPDATED 
                ? static::resolveValues(static::getOldValues($model)) 
                : null,
            'new_values' => $action === AuditLog::ACTION_DELETED 
                ? null 
                : static::resolveValues(static::getNewValues($model, $action)),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Resolve foreign key values to human-readable names
     */
    protected static function resolveValues(?array $values): ?array
    {
        if (empty($values)) {
            return $values;
        }

        $relationshipMap = static::getRelationshipMap();
        $resolved = [];

        foreach ($values as $field => $value) {
            // Check if this field has a relationship mapping
            if (isset($relationshipMap[$field]) && $value !== null) {
                $config = $relationshipMap[$field];
                $modelClass = $config['model'];
                $displayField = $config['field'];

                // Try to resolve the related model's name
                try {
                    $related = $modelClass::find($value);
                    if ($related) {
                        // Store as "Name (ID: X)" for clarity
                        $resolved[$field] = $related->{$displayField} . ' (ID: ' . $value . ')';
                    } else {
                        $resolved[$field] = 'Deleted (ID: ' . $value . ')';
                    }
                } catch (\Exception $e) {
                    $resolved[$field] = $value;
                }
            } else {
                $resolved[$field] = $value;
            }
        }

        return $resolved;
    }

    /**
     * Get a human-readable label for the model
     */
    protected static function getAuditLabel(Model $model): string
    {
        // Check for common identifier fields
        if (isset($model->name)) {
            return $model->name;
        }
        if (isset($model->title)) {
            return $model->title;
        }
        if (isset($model->email)) {
            return $model->email;
        }

        return "#{$model->getKey()}";
    }

    /**
     * Get the old values before update
     */
    protected static function getOldValues(Model $model): ?array
    {
        $changes = $model->getChanges();
        $original = $model->getOriginal();

        $oldValues = [];
        foreach (array_keys($changes) as $field) {
            if (isset($original[$field])) {
                $oldValues[$field] = $original[$field];
            }
        }

        // Exclude timestamps and sensitive fields
        return static::filterAuditFields($oldValues);
    }

    /**
     * Get the new values after create/update
     */
    protected static function getNewValues(Model $model, string $action): ?array
    {
        if ($action === AuditLog::ACTION_CREATED) {
            return static::filterAuditFields($model->getAttributes());
        }

        if ($action === AuditLog::ACTION_UPDATED) {
            return static::filterAuditFields($model->getChanges());
        }

        return null;
    }

    /**
     * Filter out fields that shouldn't be logged
     */
    protected static function filterAuditFields(array $values): array
    {
        $excluded = [
            'password',
            'remember_token',
            'updated_at',
            'created_at',
        ];

        return array_diff_key($values, array_flip($excluded));
    }

    /**
     * Get all audit logs for this model
     */
    public function auditLogs()
    {
        return AuditLog::where('model_type', get_class($this))
            ->where('model_id', $this->getKey())
            ->orderBy('created_at', 'desc');
    }
}
