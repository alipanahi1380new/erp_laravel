<?php

namespace App\Traits;

use App\Models\simpleFormLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait LogsChanges
{
    public function logCreation(Model $model): void
    {
        if (!$model instanceof \App\Contracts\Loggable) {
            return;
        }

        simpleFormLog::create([
            'loggable_id' => $model->id,
            'loggable_type' => get_class($model),
            'user_id' => Auth::id(),
            'action' => 'created',
            'old_data' => null,
            'new_data' => $model->only($model->getLoggableFields()),
        ]);
    }

    public function logUpdate(Model $model, array $oldData): void
    {
        if (!$model instanceof \App\Contracts\Loggable) {
            return;
        }

        $newData = $model->fresh()->only($model->getLoggableFields());
        $changedFields = [];
        $oldChangedFields = [];

        foreach ($newData as $key => $value) {
            if ($oldData[$key] !== $value) {
                $changedFields[$key] = $value;
                $oldChangedFields[$key] = $oldData[$key];
            }
        }

        if (!empty($changedFields)) {
            simpleFormLog::create([
                'loggable_id' => $model->id,
                'loggable_type' => get_class($model),
                'user_id' => Auth::id(),
                'action' => 'updated',
                'old_data' => $oldChangedFields,
                'new_data' => $changedFields,
            ]);
        }
    }
}