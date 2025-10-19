<?php

namespace App\Action;

use App\Models\PasswordResetToken;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdatingPassword extends BaseController
{
    public function reset(string $token, string $password): JsonResponse
    {
        PasswordResetToken::withToken($token)->first()?->user()->update([
            'password' => Hash::make($password),
        ]);

        DB::table('password_reset_tokens')->where('token', $token)->delete();

        return $this->successResponse('Password updated successfully');
    }
}
