<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRolePermissionsRequest extends FormRequest
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
            'permission_ids' => [
                'nullable',
                'array',
            ],
            'permission_ids.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ];
    }

    /**
     * Get custom validation messages for the request.
     */
    public function messages(): array
    {
        return [
            'permission_ids.array' => 'The permissions must be provided as a valid list.',
            'permission_ids.*.integer' => 'Each permission must be valid.',
            'permission_ids.*.exists' => 'One or more selected permissions do not exist.',
        ];
    }
}