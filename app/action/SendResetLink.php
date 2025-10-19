<?php

namespace App\Action;

use Exception;
use App\Mail\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\BaseController;
use Illuminate\Database\UniqueConstraintViolationException;

class SendResetLink extends BaseController
{
    public function send(string $email): JsonResponse
    {
        DB::beginTransaction();

        try {
            $token = Str::uuid()->toString();

            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => now(),
            ]);

            $link = env('FRONTEND_URL') . env('PASSWORD_RESET_URL') . "?token=$token";

            Mail::to($email)->send(new PasswordReset($link));

            DB::commit();

            $response = $this->successResponse('Password reset link sent to your email.');
        } catch (UniqueConstraintViolationException $e) {
            DB::rollBack();

            logger()->error($e->getMessage(), [
                'exception' => $e
            ]);

            $response = $this->errorResponse('A password reset request is already pending for this email address. Please check your email.');
        } catch (Exception $e) {
            DB::rollBack();

            logger()->error($e->getMessage(), [
                'exception' => $e
            ]);

            $response = $this->errorResponse('Failed to initiate password reset. Please try again later.');
        }

        return $response;
    }
}
