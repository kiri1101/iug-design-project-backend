<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LogInRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Http\Resources\LeaveTypeResource;
use App\Http\Resources\DepartmentResource;

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
                    'departments' => $user->roles->filter(fn(Role $role) => $role->id === Role::CEO)->count() > 0 ? DepartmentResource::collection($departments) : DepartmentResource::collection($departments->splice(1)->all()),
                    'leaveTypes' => LeaveTypeResource::collection(LeaveType::all())
                ]
            ]);
    }
}
