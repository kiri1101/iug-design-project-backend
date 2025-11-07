<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends BaseController
{
    public function __invoke(StoreUserRequest $storeUserRequest): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'uuid' => Str::uuid()->toString(),
                'first_name' => $storeUserRequest->input('firstName'),
                'last_name' => $storeUserRequest->input('lastName'),
                'email' => $storeUserRequest->input('mailingAddress'),
                'phone' => str_replace('-', '', $storeUserRequest->input('phoneNumber')),
                'password' => Hash::make($storeUserRequest->input('secret')),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // save user role
            $user->roles()->attach(Role::EMPLOYEE, [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // create user profile
            $user->profile()->create([
                'uuid' => Str::uuid()->toString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return $this->successResponse('User created successfully.', [
                'token' => $user->createToken(env('APP_KEY'), ['server:access'])->plainTextToken,
                'user' => new UserResource($user),
                'settings' => []
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
