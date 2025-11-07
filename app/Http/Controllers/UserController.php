<?php

namespace App\Http\Controllers;

use App\Mail\SendUserCredentials;
use Exception;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Models\Department;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class UserController extends BaseController
{
    public function list(Request $request): JsonResponse
    {
        $users = User::all();
        $usersExceptCeo = $users->filter(
            fn(User $user) => $user->roles->contains(
                fn(Role $role) => $role->id === Role::CEO
            )
        );

        $list = $request->user()->roles->filter(fn(Role $role) => $role->id === Role::CEO)->count() > 0 ? $users : $usersExceptCeo;

        return $this->successResponse('List of users', [
            'users' => UserResource::collection($list)
        ]);
    }

    public function store(CreateUserRequest $createUserRequest): JsonResponse
    {
        DB::beginTransaction();

        $role = Role::withUuid($createUserRequest->input('position')['id'])->first();
        $department = Department::withUuid($createUserRequest->input('department')['id'])->first();
        $defaultPassword = Str::random(8);

        try {
            $user = User::createOrRestore([
                'first_name' => strtolower($createUserRequest->input('firstName')),
                'last_name' => strtolower($createUserRequest->input('lastName')),
                'email' => $createUserRequest->input('mailingAddress'),
                'phone' => str_replace('-', '', $createUserRequest->input('phoneNumber')),
            ], [
                'uuid' => Str::uuid()->toString(),
                'first_name' => strtolower($createUserRequest->input('firstName')),
                'last_name' => strtolower($createUserRequest->input('lastName')),
                'email' => $createUserRequest->input('mailingAddress'),
                'phone' => str_replace('-', '', $createUserRequest->input('phoneNumber')),
                'password' => Hash::make($defaultPassword),
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

            // create internal notification
            Notification::create([
                'uuid' => Str::uuid()->toString(),
                'type' => 1,
                'user_id' => $user->id,
                'subject' => 'Welcome to IUG',
                'message' => "Your default account credentials are as shown as follows: pseudo: $user->email, password: xxxxxxxx",
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // notify user with credentials
            Mail::to($user->email)->send(
                new SendUserCredentials($user, $defaultPassword)->afterCommit()
            );

            return $this->successResponse('User created successfully.', [
                'user' => new UserResource($user)
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function update(
        User $user,
        CreateUserRequest $createUserRequest
    ): JsonResponse {
        DB::beginTransaction();

        $role = Role::withUuid($createUserRequest->input('position')['id'])->first();
        $department = Department::withUuid($createUserRequest->input('department')['id'])->first();

        try {
            $user->update([
                'first_name' => strtolower($createUserRequest->input('firstName')),
                'last_name' => strtolower($createUserRequest->input('lastName')),
                'email' => $createUserRequest->input('mailingAddress'),
                'phone' => str_replace('-', '', $createUserRequest->input('phoneNumber')),
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

    public function delete(User $user): JsonResponse
    {
        $user->tokens()->delete();

        $user->roles()->detach();

        $user->delete();

        return $this->successResponse('User deleted successfully');
    }
}
