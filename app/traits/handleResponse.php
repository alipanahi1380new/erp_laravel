<?php

namespace App\Traits;

trait handleResponse
{

    protected function transferResponse(array $returnArray ,array $response)
    {
        foreach(['message' , 'statusCode' , 'data' , 'errors'] as $key)
        {
            if(!isset($response[$key]))
            {
                continue;
            }
            $returnArray[$key] = $response[$key];
        }
        
        return $returnArray;
    }

    protected function generateResponse(array $params)
    {
        $defaults = [
            'returnArray' => [],
            'message' => '',
            'statusCode' => 400,
            'data' => [],
            'errors' => []
        ];

        // Merge provided parameters with defaults
        $params = array_merge($defaults, $params);

        $response = [
            'message' => $params['message'],
            'statusCode' => $params['statusCode']
        ];
        
        if ($params['message'] === 'success') {
            $response['data'] = $params['data'] ?? null;
        } else {
            $response['errors'] = $params['errors'] ?? $params['errors'];
            if (!empty($params['data'])) {
                $response['data'] = $params['data'];
            }
        }

        return array_merge($params['returnArray'], $response);
    }
}