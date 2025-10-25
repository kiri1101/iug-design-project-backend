<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignOutController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->successResponse('Good bye!');
    }
}
