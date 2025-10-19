<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function successResponse(string $message, array $data = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function errorResponse(string $message, array $data = [], int $code = 500): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
