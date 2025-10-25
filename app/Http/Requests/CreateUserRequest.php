<?php

namespace App\Http\Requests;

use App\Models\Department;
use App\Models\Role;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'firstName' => 'required|string|min:2|max:255',
            'lastName' => 'required|string|min:2|max:255',
            'mailingAddress' => 'required|email',
            'phoneNumber' => 'required|string|min:8|max:14'
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (!Department::withUuid($this->department['id'])->exists()) {
                    $validator->errors()->add(
                        'department',
                        'The department provided is invalid!'
                    );
                }

                if (!Role::withUuid($this->position['id'])->exists()) {
                    $validator->errors()->add(
                        'position',
                        'The position provided is invalid!'
                    );
                }
            }
        ];
    }
}
