<?php

namespace App\Services;

use App\Models\simpleFormLog;
use App\Traits\handleResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Logger
{
    use handleResponse;
    public function logCreation(Model $model, array $fields): array
    {
        $returnArray = ['message' => 'success' , 'statusCode' => 201];
        try{
            simpleFormLog::create([
                'loggable_id' => $model->id,
                'loggable_type' => get_class($model),
                'user_id' => Auth::id(),
                'action' => 'created',
                'old_data' => null,
                'new_data' => $fields,
            ]);
        }
        catch (\Exception $e)
        {
            return $this->generateResponse(
                [
                    'returnArray' => $returnArray ,
                    'message' => 'insert_database_error' ,
                    'statusCode' => 500 ,
                    'errors' => ['simpleFormLog' , $e->getMessage()]
                ]
            );
        }


        return $returnArray;
    }

    public function logUpdate(Model $model, array $oldData, array $newData): array
    {
        $returnArray = ['message' => 'success' , 'statusCode' => 201];

        $changedFields = [];
        $oldChangedFields = [];

        foreach ($newData as $key => $value) {
            if ($oldData[$key] !== $value) {
                $changedFields[$key] = $value;
                $oldChangedFields[$key] = $oldData[$key];
            }
        }

        if (!empty($changedFields)) {
            try
            {
                simpleFormLog::create([
                    'loggable_id' => $model->id,
                    'loggable_type' => get_class($model),
                    'user_id' => Auth::id(),
                    'action' => 'updated',
                    'old_data' => $oldChangedFields,
                    'new_data' => $changedFields,
                ]);
            }
            catch (\Exception $e){
                return $this->generateResponse(
                    [
                        'returnArray' => $returnArray ,
                        'message' => 'update_database_error' ,
                        'statusCode' => 500 ,
                        'errors' => ['simpleFormLog' , $e->getMessage()]
                    ]
                );
            }

        }

        return $returnArray;
    }
}