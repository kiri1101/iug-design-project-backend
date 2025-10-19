<?php

namespace App\Action;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;

class AuthenticateUser extends BaseController
{
    public function signIn(string $pseudo, string $secret): JsonResponse
    {
        $user = User::withEmail($pseudo)->first();

        return !$user || !Hash::check($secret, $user->password) ?
            $this->errorResponse('Invalid credentials') : $this->successResponse('Logged In!', [
                'token' => $user->createToken(env('APP_KEY'), ['server:access'])->plainTextToken,
                'user' => new UserResource($user),
            ]);
    }
}
