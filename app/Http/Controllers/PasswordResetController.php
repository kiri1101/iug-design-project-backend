<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\InitiatePasswordResetRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\BaseController;
use Illuminate\Database\UniqueConstraintViolationException;

class PasswordResetController extends BaseController
{
    public function init(
        InitiatePasswordResetRequest $initiatePasswordResetRequest
    ): JsonResponse {
        DB::beginTransaction();

        try {
            $token = Str::uuid()->toString();

            DB::table('password_reset_tokens')->insert([
                'email' => $initiatePasswordResetRequest->input('mailing_address'),
                'token' => $token,
                'created_at' => now(),
            ]);

            $link = env('FRONTEND_URL') . env('PASSWORD_RESET_URL') . "?token=$token";

            Mail::to($initiatePasswordResetRequest->input('mailing_address'))->send(new PasswordReset($link));

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

    public function updatingPassword(
        UpdatePasswordRequest $updatePasswordRequest
    ): JsonResponse {
        PasswordResetToken::withToken($updatePasswordRequest->input('token'))->first()?->user()->update([
            'password' => Hash::make($updatePasswordRequest->input('new_secret')),
        ]);

        DB::table('password_reset_tokens')->where('token', $updatePasswordRequest->input('token'))->delete();

        return $this->successResponse('Password updated successfully');
    }
}
