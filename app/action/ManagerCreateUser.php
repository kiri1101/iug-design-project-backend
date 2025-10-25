<?php

namespace App\Action;

use Exception;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Models\Department;

class ManagerCreateUser extends BaseController
{
    public function store(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $departmentId,
        string $roleId
    ): JsonResponse {
        DB::beginTransaction();

        $role = Role::withUuid($roleId)->first();
        $department = Department::withUuid($departmentId)->first();

        try {
            $user = User::create([
                'uuid' => Str::uuid()->toString(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => str_replace('-', '', $phone),
                'password' => Hash::make('123123123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // save user role
            $user->roles()->attach($role->id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // create user profile
            $user->profile()->create([
                'uuid' => Str::uuid()->toString(),
                'department_id' => $department->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return $this->successResponse('User created successfully.', [
                'user' => new UserResource($user)
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
