<?php

namespace App\Traits;
use Illuminate\Http\Request;

trait FiltersModels
{
    protected $modelFilter;

    public function index(Request $request)
    {
        $filters = [
            'user_ids' => $request->input('user_ids', []),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'search' => $request->input('search'),
        ];

        $modelClass = $this->modelClass; // Must be defined in the controller

        $results = $this->modelFilter->filter(new $modelClass(), $filters)->toArray()['data'];

        $transformedResults = array_map(function ($item) {
            $itemArray = (array) $item;
            if (isset($itemArray['user'])) {
                $itemArray['user_data'] = [
                    'name' => $itemArray['user']['name'] ?? ''
                ];
                unset($itemArray['user_id_maker']);
                unset($itemArray['user']);
            }
            return $itemArray;
        }, $results);

        return $this->generateResponse(
            [
                'data' => $transformedResults ,
                'message' => 'success' ,
                'statusCode' => 200
            ]
        );
    }

}