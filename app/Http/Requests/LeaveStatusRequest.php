<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveStatusRequest extends FormRequest
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
        // 1 => submit, 2 => superior validate, 3 => HR validate, 4 => Employee back, 5 => HR validate return, 6 => Reject
        return [
            'action' => 'required|in:1,2,3,4,5,6'
        ];
    }
}
