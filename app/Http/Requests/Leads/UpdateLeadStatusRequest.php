<?php

namespace App\Http\Requests\Leads;

use App\Models\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadStatusRequest extends FormRequest
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
                Rule::exists(LeadStatus::class, 'id')
                    ->where('is_active', true),
            ],
             'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}
