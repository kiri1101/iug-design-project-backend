<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends BaseController
{
    public function usersCount(Request $request): JsonResponse
    {
        $users = User::all();

        if ($request->user()->roles->filter(fn(Role $role) => $role->id === Role::CEO)->count() > 0) {
            $count = $users->count();
        } else if ($request->user()->roles->filter(fn(Role $role) => $role->id === Role::DT)->count() > 0) {
            $count = $users->filter(fn(User $user) => $user->profile->department->id === Department::TECHNICAL)->count();
        } else if ($request->user()->roles->filter(fn(Role $role) => $role->id === Role::DF)->count() > 0) {
            $count = $users->filter(fn(User $user) => $user->profile->department->id === Department::FINANCIAL)->count();
        } else if ($request->user()->roles->filter(fn(Role $role) => $role->id === Role::DP)->count() > 0) {
            $count = $users->filter(fn(User $user) => $user->profile->department->id === Department::PRODUCT)->count();
        } else if ($request->user()->roles->filter(fn(Role $role) => $role->id === Role::DHR)->count() > 0) {
            $count = $users->filter(fn(User $user) => $user->profile->department->id === Department::HR)->count();
        } else {
            $count = 0;
        }

        return $this->successResponse('List of users count', [
            'count' => $count
        ]);
    }
}
