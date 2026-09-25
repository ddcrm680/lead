<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_id' => [
                'required',
                'integer',
                'exists:lead_follow_up_types,id',
            ],
            'status_id' => [
                'required',
                'integer',
                'exists:lead_follow_up_statuses,id',
            ],
            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'due_at' => [
                'required',
                'date',
            ],
        ];
    }
}
