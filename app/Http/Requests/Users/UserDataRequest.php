<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserDataRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'in:25,50,100',
            ],
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'role_id' => [
                'nullable',
                'integer',
                'exists:roles,id',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom validation messages for the request.
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'The page number must be a valid number.',
            'page.min' => 'The page number must be at least 1.',

            'per_page.integer' => 'The number of users per page must be valid.',
            'per_page.in' => 'The number of users per page must be 25, 50, or 100.',

            'search.string' => 'The search value must be valid text.',
            'search.max' => 'The search may not be longer than 100 characters.',

            'role_id.integer' => 'The selected role is invalid.',
            'role_id.exists' => 'The selected role does not exist.',

            'status.boolean' => 'The selected status is invalid.',
        ];
    }
}