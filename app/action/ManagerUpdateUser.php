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
use App\Models\Department;

class ManagerUpdateUser extends BaseController
{
    public function update(
        User $user,
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
            $user->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => str_replace('-', '', $phone),
            ]);

            // update user role if incoming role is different
            if ($user->roles->first()->id !== $role->id) {
                $user->roles()->delete();

                $user->roles()->attach($role->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // create user profile
            $user->profile()->update([
                'department_id' => $department->id,
            ]);

            DB::commit();

            return $this->successResponse('User updated successfully.', [
                'user' => new UserResource($user)
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
