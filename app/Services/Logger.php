<?php

namespace App\Services;

use App\Models\simpleFormLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Logger
{
    public function logCreation(Model $model, array $fields): void
    {
        simpleFormLog::create([
            'loggable_id' => $model->id,
            'loggable_type' => get_class($model),
            'user_id' => Auth::id(),
            'action' => 'created',
            'old_data' => null,
            'new_data' => $fields,
        ]);
    }

    public function logUpdate(Model $model, array $oldData, array $newData): void
    {
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