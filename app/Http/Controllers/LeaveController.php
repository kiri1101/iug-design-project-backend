<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Support\Str;
use App\Mail\LeaveRequested;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Events\NotifyMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\LeaveResource;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\LeaveStatusRequest;
use App\Http\Resources\LeaveTypeResource;

class LeaveController extends BaseController
{
    public function list(Request $request): JsonResponse
    {
        if (
            $request->user()
            ->roles()
            ->whereIn('roles.id', [Role::CEO])
            ->exists()
        ) {
            // get leaves for all directors only
            $directorLeaves = Leave::with('leaveType')
                ->whereHas(
                    'user.roles',
                    fn($q) => $q->whereIn(
                        'roles.id',
                        [Role::DF, Role::DT, Role::DHR, Role::DP]
                    )
                )
                ->where('status', '>=', '2')
                ->get();

            $outputLeaves = [
                'personal' => [],
                'department' => LeaveResource::collection($directorLeaves),
            ];
        } else if (
            $request->user()
            ->roles()
            ->whereIn('roles.id', [Role::DT])
            ->exists()
        ) {
            // get leaves for technical department
            $personalLeaves = Leave::with('leaveType')->whereHas(
                'user',
                fn($q) => $q->whereId($request->user()->id)
            )->get();

            $departmentLeaves = Leave::with('leaveType')
                ->whereHas(
                    'user.profile.department',
                    fn($q) => $q->whereId(Department::TECHNICAL)
                )
                ->where('status', '2')
                ->get();

            $outputLeaves = [
                'personal' => LeaveResource::collection($personalLeaves),
                'department' => LeaveResource::collection($departmentLeaves),
            ];
        } else if (
            $request->user()
            ->roles()
            ->whereIn('roles.id', [Role::DF])
            ->exists()
        ) {
            // get leaves for financial department
            $personalLeaves = Leave::with('leaveType')->whereHas(
                'user',
                fn($q) => $q->whereId($request->user()->id)
            )->get();

            $departmentLeaves = Leave::with('leaveType')
                ->whereHas(
                    'user.profile.department',
                    fn($q) => $q->whereId(Department::FINANCIAL)
                )
                ->where('status', '2')
                ->get();

            $outputLeaves = [
                'personal' => LeaveResource::collection($personalLeaves),
                'department' => LeaveResource::collection($departmentLeaves),
            ];
        } else if (
            $request->user()
            ->roles()
            ->whereIn('roles.id', [Role::DP])
            ->exists()
        ) {
            // get leaves for product department
            $personalLeaves = Leave::with('leaveType')->whereHas(
                'user',
                fn($q) => $q->whereId($request->user()->id)
            )->get();

            $departmentLeaves = Leave::with('leaveType')
                ->whereHas(
                    'user.profile.department',
                    fn($q) => $q->whereId(Department::PRODUCT)
                )
                ->where('status', '2')
                ->get();

            $outputLeaves = [
                'personal' => LeaveResource::collection($personalLeaves),
                'department' => LeaveResource::collection($departmentLeaves),
            ];
        } else if (
            $request->user()
            ->roles()
            ->whereIn('roles.id', [Role::DHR])
            ->exists()
        ) {
            // get all leaves with status greater than SUPERIOR VALIDATION
            $personalLeaves = Leave::with('leaveType')->whereHas(
                'user',
                fn($q) => $q->whereId($request->user()->id)
            )->get();

            $departmentLeaves = Leave::with('leaveType')
                ->where('status', '>', '2')
                ->get();

            $outputLeaves = [
                'personal' => LeaveResource::collection($personalLeaves),
                'department' => LeaveResource::collection($departmentLeaves),
            ];
        } else {
            $personalLeaves = Leave::with('leaveType')->whereHas(
                'user',
                fn($q) => $q->whereId($request->user()->id)
            )->get();

            $outputLeaves = [
                'personal' => LeaveResource::collection($personalLeaves),
                'department' => [],
            ];
        }

        return $this->successResponse('List of leaves', [
            'leaves' => $outputLeaves
        ]);
    }

    public function store(Request $request, StoreLeaveRequest $storeLeaveRequest): JsonResponse
    {
        Leave::create([
            'uuid' => Str::uuid()->toString(),
            'user_id' => $request->user()->id,
            'type_id' => LeaveType::withUuid($storeLeaveRequest->input('type')['id'])->first()->id,
            'cause' => $storeLeaveRequest->input('description'),
            'departure' => $storeLeaveRequest->input('departureDate'),
            'return' => $storeLeaveRequest->input('returnDate'),
            'status' => Leave::PENDING,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $this->successResponse('Leave request saved!');
    }

    public function updateLeave(Leave $leave, StoreLeaveRequest $storeLeaveRequest): JsonResponse
    {
        $leave->update([
            'type_id' => LeaveType::withUuid($storeLeaveRequest->input('type')['id'])->first()->id,
            'cause' => $storeLeaveRequest->input('description'),
            'departure' => $storeLeaveRequest->input('departureDate'),
            'return' => $storeLeaveRequest->input('returnDate'),
        ]);

        return $this->successResponse('Leave request updated!');
    }

    public function deleteLeave(Leave $leave): JsonResponse
    {
        $leave->delete();

        return $this->successResponse('Leave deleted');
    }

    public function updateLeaveStatus(
        Leave $leave,
        LeaveStatusRequest $leaveStatusRequest
    ): JsonResponse {
        match (intval($leaveStatusRequest->input('action'))) {
            1 => $response = $this->updatingStatus($leave, 'send', $leaveStatusRequest->user()),
            2 => $response = $this->updatingStatus($leave, 'superior', $leaveStatusRequest->user()),
            3 => $response = $this->updatingStatus($leave, 'grant', $leaveStatusRequest->user()),
            4 => $response = $this->updatingStatus($leave, 'back', $leaveStatusRequest->user()),
            5 => $response = $this->updatingStatus($leave, 'confirm', $leaveStatusRequest->user()),
            6 => $response = $this->updatingStatus($leave, 'reject', $leaveStatusRequest->user()),
            default => $response = $this->errorResponse('Update failed! Please try again')
        };

        return $response;
    }

    private function updatingStatus(Leave $leave, string $action, User $authUser): JsonResponse
    {
        DB::beginTransaction();

        $employee = $leave->user->first_name . ' ' . $leave->user->last_name;
        $superior = $authUser->first_name . ' ' . $authUser->last_name;
        $authUserRole = $authUser->roles->first();
        $leaveName = $leave->leaveType->name;

        // set the status based on action parameter
        match ($action) {
            'send' => $status = Leave::AWAIT_SUPERIOR_VALIDATION,
            'superior' => $status = Leave::AWAIT_HR_VALIDATION,
            'grant' => $status = Leave::GRANTED,
            'back' => $status = Leave::RETURNED_FROM_LEAVE,
            'confirm' => $status = Leave::HR_CONFIRM_RETURN,
            'reject' => $status = Leave::REJECT
        };

        // build messages for appropriate action
        match ($action) {
            'send' => $message = "$employee is requesting for a leave from work for $leaveName. Please connect to the platform and process their request",
            'superior' => $message = "$superior, $authUserRole->name has approved of $employee's leave request.",
            'grant' => $message = "$employee's leave has been confirmed.",
            'back' => $message = "$employee has filed a request to resume service.",
            'confirm' => $message = "$employee's request to resume service has been approved by $superior, $authUserRole",
            'reject' => $message = "$superior, $authUserRole->name has rejected your leave request. Please contact them for more information"
        };

        // determine who receives the mail or notification
        $receiverRoles = $this->getReceiverRoles($leave, $action);

        // get all users who have a role found in the receiver roles array
        $receiverAddress = User::whereRelation('roles', 'id', $receiverRoles)->get()->map(fn(User $user) => [
            'email' => $user->email
        ]);

        try {
            // update leave status
            $leave->update([
                'status' => $status
            ]);

            // register notification
            Notification::create([
                'uuid' => Str::uuid()->toString(),
                'type' => 1,
                'user_id' => $leave->user->id,
                'subject' => 'Leave status updated',
                'message' => $message,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // broadcast message
            broadcast(
                new NotifyMessage(
                    $leave->user,
                    $message,
                )
            )->toOthers();

            DB::commit();

            // send mail
            foreach ($receiverAddress as $key => $value) {
                Mail::to($value['email'])->send(
                    new LeaveRequested($leave->user, $message)->afterCommit()
                );
            }

            $response = $this->successResponse('Leave status updated!');
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $response;
    }

    private function getReceiverRoles(Leave $leave, string $action): array
    {
        if ($leave->user->profile->department->id === Department::TECHNICAL) {
            if ($leave->user->roles->contains(fn(Role $role) => $role->id === Role::DT)) {
                $receiverRoles = [Role::CEO];
            } else if ($leave->user->roles->contains(fn(Role $role) => $role->id === Role::RT)) {
                match ($action) {
                    'send' => $receiverRoles = [Role::DT],
                    'superior' => $receiverRoles =  [Role::DHR],
                    'grant' => $receiverRoles = [Role::DT, Role::DHR],
                    'back' => $receiverRoles = [Role::DT, Role::DHR],
                    'confirm' => $receiverRoles = [Role::DT, Role::DHR],
                    'reject' => $receiverRoles = [Role::DT, Role::DHR],
                };
            } else {
                match ($action) {
                    'send' => $receiverRoles = [Role::DT, Role::RT],
                    'superior' => $receiverRoles =  [Role::DHR, Role::RHR],
                    'grant' => $receiverRoles = [Role::DT, Role::RT, Role::DHR, Role::RHR],
                    'back' => $receiverRoles = [Role::DT, Role::RT, Role::DHR, Role::RHR],
                    'confirm' => $receiverRoles = [Role::DT, Role::RT, Role::DHR, Role::RHR],
                    'reject' => $receiverRoles = [Role::DT, Role::RT, Role::DHR, Role::RHR],
                };
            }
        } else if ($leave->user->profile->department->id === Department::HR) {
            if ($leave->user->roles->contains(fn(Role $role) => $role->id === Role::DHR)) {
                $receiverRoles = [Role::CEO];
            } else if ($leave->user->roles->contains(fn(Role $role) => $role->id === Role::RHR)) {
                match ($action) {
                    'send' => $receiverRoles = [Role::DHR],
                    'superior' => $receiverRoles =  [Role::DHR],
                    'grant' => $receiverRoles = [Role::DHR],
                    'back' => $receiverRoles = [Role::DHR],
                    'confirm' => $receiverRoles = [Role::DHR],
                    'reject' => $receiverRoles = [Role::DHR]
                };
            } else {
                match ($action) {
                    'send' => $receiverRoles = [Role::DHR, Role::RHR],
                    'superior' => $receiverRoles =  [Role::DHR, Role::RHR],
                    'grant' => $receiverRoles = [Role::DHR, Role::RHR],
                    'back' => $receiverRoles = [Role::DHR, Role::RHR],
                    'confirm' => $receiverRoles = [Role::DHR, Role::RHR],
                    'reject' => $receiverRoles = [Role::DHR, Role::RHR],
                };
            }
        } else if ($leave->user->profile->department->id === Department::FINANCIAL) {
            if ($leave->user->roles->contains(fn(Role $role) => $role->id === Role::DF)) {
                $receiverRoles = [Role::CEO];
            } else if ($leave->user->roles->contains(fn(Role $role) => $role->id === Role::RF)) {
                match ($action) {
                    'send' => $receiverRoles = [Role::DF],
                    'superior' => $receiverRoles =  [Role::DHR],
                    'grant' => $receiverRoles = [Role::DF, Role::DHR],
                    'back' => $receiverRoles = [Role::DF, Role::DHR],
                    'confirm' => $receiverRoles = [Role::DF, Role::DHR],
                    'reject' => $receiverRoles = [Role::DF, Role::RHR],
                };
            } else {
                match ($action) {
                    'send' => $receiverRoles = [Role::DF, Role::RF],
                    'superior' => $receiverRoles =  [Role::DHR, Role::RHR],
                    'grant' => $receiverRoles = [Role::DF, Role::RF, Role::DHR, Role::RHR],
                    'back' => $receiverRoles = [Role::DF, Role::RF, Role::DHR, Role::RHR],
                    'confirm' => $receiverRoles = [Role::DF, Role::RF, Role::DHR, Role::RHR],
                    'reject' => $receiverRoles = [Role::DF, Role::RF, Role::DHR, Role::RHR],
                };
            }
        } else if ($leave->user->profile->department->id === Department::PRODUCT) {
            if ($leave->user->roles->contains(fn(Role $role) => $role->id === Role::DP)) {
                $receiverRoles = [Role::CEO];
            } else if ($leave->user->roles->contains(fn(Role $role) => $role->id === Role::RP)) {
                match ($action) {
                    'send' => $receiverRoles = [Role::DP],
                    'superior' => $receiverRoles =  [Role::DHR],
                    'grant' => $receiverRoles = [Role::DP, Role::DHR],
                    'back' => $receiverRoles = [Role::DP, Role::DHR],
                    'confirm' => $receiverRoles = [Role::DP, Role::DHR],
                    'reject' => $receiverRoles = [Role::DP, Role::RHR],
                };
            } else {
                match ($action) {
                    'send' => $receiverRoles = [Role::DP, Role::RP],
                    'superior' => $receiverRoles =  [Role::DHR, Role::RHR],
                    'grant' => $receiverRoles = [Role::DP, Role::RP, Role::DHR, Role::RHR],
                    'back' => $receiverRoles = [Role::DP, Role::RP, Role::DHR, Role::RHR],
                    'confirm' => $receiverRoles = [Role::DP, Role::RP, Role::DHR, Role::RHR],
                    'reject' => $receiverRoles = [Role::DP, Role::RP, Role::DHR, Role::RHR],
                };
            }
        } else {
            $receiverRoles = [Role::CEO, Role::DHR];
        }

        return $receiverRoles;
    }
}
