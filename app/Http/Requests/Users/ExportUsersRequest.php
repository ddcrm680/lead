<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExportUsersRequest extends FormRequest
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
            'format' => [
                'nullable',
                'string',
                'in:csv,xlsx,ods,pdf',
            ],
        ];
    }

    /**
     * Get custom validation messages for the request.
     */
    public function messages(): array
    {
        return [
            'search.string' => 'The search value must be valid text.',
            'search.max' => 'The search may not be longer than 100 characters.',

            'role_id.integer' => 'The selected role is invalid.',
            'role_id.exists' => 'The selected role does not exist.',

            'status.boolean' => 'The selected status is invalid.',

            'format.string' => 'The export format must be valid.',
            'format.in' => 'The selected export format is not supported.',
        ];
    }
}