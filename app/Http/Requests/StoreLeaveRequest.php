<?php

namespace App\Http\Requests;

use App\Models\LeaveType;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->user()->roles->contains(fn(Role $role) => $role->id !== Role::CEO);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|min:5',
            'departureDate' => 'required|date',
            'returnDate' => 'required|date',
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (gettype($this->type) === 'array') {
                    if (!LeaveType::withUuid($this->type['id'])->exists()) {
                        $validator->errors()->add(
                            'type',
                            'The type of leave provided is invalid!'
                        );
                    }
                } else {
                    $validator->errors()->add(
                        'type',
                        'The type of leave provided must be valid!',
                    );
                }
            }
        ];
    }
}
