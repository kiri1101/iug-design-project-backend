<?php

namespace App\Http\Controllers;

use App\Action\AuthenticateUser;
use App\Http\Requests\LogInRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignInController extends BaseController
{
    public function __invoke(
        LogInRequest $logInRequest,
        AuthenticateUser $authenticateUser
    ): JsonResponse {
        return $authenticateUser->signIn($logInRequest->input('pseudo'), $logInRequest->input('secret'));
    }
}
