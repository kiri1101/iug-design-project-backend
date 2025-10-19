<?php

namespace App\Http\Controllers;

use App\Action\CreateUser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;

class RegistrationController extends Controller
{
    public function __invoke(StoreUserRequest $storeUserRequest, CreateUser $createUser): JsonResponse
    {
        return $createUser->store(
            $storeUserRequest->input('firstName'),
            $storeUserRequest->input('lastName'),
            $storeUserRequest->input('mailingAddress'),
            $storeUserRequest->input('phoneNumber'),
            $storeUserRequest->input('secret'),
        );
    }
}
