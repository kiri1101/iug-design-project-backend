<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'fullName' => $this->user->first_name . ' ' . $this->user->last_name,
            'description' => $this->cause,
            'departureDate' => $this->departure,
            'returnDate' => $this->return,
            'status' => match (intval($this->status)) {
                1 => [
                    'label' => 'Draft',
                    'color' => 'orange',
                    'level' => 1
                ],
                2 => [
                    'label' => 'Pending superior validation',
                    'color' => 'sky',
                    'level' => 2
                ],
                3 => [
                    'label' => 'Pending HR validation',
                    'color' => 'blue',
                    'level' => 3
                ],
                4 => [
                    'label' => 'Validated',
                    'color' => 'green',
                    'level' => 4
                ],
                5 => [
                    'label' => 'Returned',
                    'color' => 'yellow',
                    'level' => 5
                ],
                6 => [
                    'label' => 'HR confirmed return',
                    'color' => 'violet',
                    'level' => 6
                ],
                7 => [
                    'label' => 'Absence overdue',
                    'color' => 'red',
                    'level' => 7
                ],
                default => 'Pending',
            },
            'type' => new LeaveTypeResource($this->leaveType)
        ];
    }
}
