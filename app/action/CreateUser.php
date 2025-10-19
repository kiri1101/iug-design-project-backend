<?php

namespace App\Action;

use Exception;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;

class CreateUser extends BaseController
{
    public function store(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $secret
    ): JsonResponse {
        DB::beginTransaction();

        try {
            $user = User::create([
                'uuid' => Str::uuid()->toString(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make($secret),
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
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
