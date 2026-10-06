<?php

namespace App\Http\Requests\Leads\Import;

use App\Enums\LeadPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrepareLeadImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => [
                'required',
                'integer',
                Rule::exists('lead_statuses', 'id')
                    ->where('is_active', true),
            ],

            'source_id' => [
                'nullable',
                'integer',
                Rule::exists('lead_sources', 'id')
                    ->where('is_active', true),
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('is_active', true),
            ],

            'priority' => [
                'required',
                'integer',
                Rule::enum(LeadPriority::class),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'status_id' => 'default status',
            'source_id' => 'default source',
            'assigned_user_id' => 'default assigned user',
            'priority' => 'default priority',
        ];
    }
}
