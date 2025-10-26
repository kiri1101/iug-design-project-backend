<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\DepartmentResource;
use App\Models\Role;
use App\Models\User;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Models\Department;

class SignInController extends BaseController
{
    public function __invoke(
        LogInRequest $logInRequest
    ): JsonResponse {

        $user = User::withEmail($logInRequest->input('pseudo'))->first();
        $departments = Department::all();

        return !$user || !Hash::check($logInRequest->input('secret'), $user->password) ?
            $this->errorResponse('Invalid credentials') : $this->successResponse('Logged In!', [
                'token' => $user->createToken(env('APP_KEY'), ['server:access'])->plainTextToken,
                'user' => new UserResource($user),
                'settings' => [
                    'roles' => $user->roles->filter(fn(Role $role) => $role->id === Role::CEO || $role->id === Role::DHR)->count() > 0 ? RoleResource::collection(Role::whereNot('id', 1)->get()) : [],
                    'departments' => $user->roles->filter(fn(Role $role) => $role->id === Role::CEO)->count() > 0 ? DepartmentResource::collection($departments) : DepartmentResource::collection($departments->splice(1)->all())
                ]
            ]);
    }
}
