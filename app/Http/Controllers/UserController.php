<?php

namespace App\Http\Controllers;

use App\Action\ManagerCreateUser;
use App\Action\ManagerUpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function store(CreateUserRequest $createUserRequest, ManagerCreateUser $managerCreateUser): JsonResponse
    {
        return $managerCreateUser->store(
            $createUserRequest->input('firstName'),
            $createUserRequest->input('lastName'),
            $createUserRequest->input('mailingAddress'),
            $createUserRequest->input('phoneNumber'),
            $createUserRequest->input('department')['id'],
            $createUserRequest->input('position')['id']
        );
    }

    public function update(
        User $user,
        CreateUserRequest $createUserRequest,
        ManagerUpdateUser $managerUpdateUser
    ): JsonResponse {
        return $managerUpdateUser->update(
            $user,
            $createUserRequest->input('firstName'),
            $createUserRequest->input('lastName'),
            $createUserRequest->input('mailingAddress'),
            $createUserRequest->input('phoneNumber'),
            $createUserRequest->input('department')['id'],
            $createUserRequest->input('position')['id']
        );
    }

    public function delete(User $user): JsonResponse
    {
        $user->tokens()->delete();

        $user->delete();

        return $this->successResponse('User deleted successfully');
    }
}
