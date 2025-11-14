<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($message = 'Success', $data = null, $statusCode = 200)
    {
        $response = [
            'statusCode' => $statusCode,
            'status' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public static function error($message = 'Something went wrong', $errors = [], $statusCode = 400)
    {
        return response()->json([
            'statusCode' => $statusCode,
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
}
