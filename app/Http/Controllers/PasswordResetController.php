<?php

namespace App\Http\Controllers;

use App\Action\SendResetLink;
use App\Action\UpdatingPassword;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\InitiatePasswordResetRequest;
use App\Http\Requests\UpdatePasswordRequest;

class PasswordResetController extends Controller
{
    public function init(
        InitiatePasswordResetRequest $initiatePasswordResetRequest,
        SendResetLink $sendResetLink
    ): JsonResponse {
        return $sendResetLink->send($initiatePasswordResetRequest->input('mailing_address'));
    }

    public function updatingPassword(
        UpdatePasswordRequest $updatePasswordRequest,
        UpdatingPassword $updatingPassword
    ): JsonResponse {
        return $updatingPassword->reset(
            $updatePasswordRequest->input('token'),
            $updatePasswordRequest->input('new_secret')
        );
    }
}
