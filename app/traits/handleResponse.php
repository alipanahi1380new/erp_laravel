<?php

namespace App\Traits;

trait handleResponse
{

    protected function generateResponse(string $message, int $statusCode = 400, array $data = [])
    {
        return [
            'alert' => $message,
            'statusCode' => $statusCode,
            'data' => $data,
        ];
    }
}
