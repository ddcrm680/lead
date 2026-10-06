<?php

namespace App\Http\Requests\Leads\Import;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'match_decisions' => [
                'present',
                'array',
            ],

            'match_decisions.*' => [
                'required',
                'string',
                Rule::in([
                    'import',
                    'skip',
                ]),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'match_decisions' =>
                'potential match decisions',

            'match_decisions.*' =>
                'potential match decision',
        ];
    }
}
