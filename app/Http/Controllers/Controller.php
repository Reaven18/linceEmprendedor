<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Un método global para respuestas exitosas en tu API
     */
    public function sendResponse($result, $message, $code = 200)
    {
        return response()->json([
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ], $code);
    }

    /**
     * Un método global para respuestas de error
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}